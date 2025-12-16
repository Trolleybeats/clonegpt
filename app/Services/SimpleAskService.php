<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Service simplifié pour communiquer avec l'API OpenRouter.
 *
 * Exemple pédagogique utilisant le client HTTP de Laravel.
 */
class SimpleAskService
{
    public const DEFAULT_MODEL = 'openai/gpt-5-mini';

    private string $apiKey;
    private string $baseUrl;

    public function __construct($client = null)
    {
        $this->apiKey = config('services.openrouter.api_key');
        $this->baseUrl = rtrim(config('services.openrouter.base_url', 'https://openrouter.ai/api/v1'), '/');
    }

    /**
     * Récupère la liste des modèles disponibles.
     *
     * @return array<int, array{
     *     id: string,
     *     name: string,
     *     description: string,
     *     context_length: int,
     *     max_completion_tokens: int,
     *     input_modalities: array<string>,
     *     output_modalities: array<string>,
     *     supported_parameters: array<string>
     * }>
     */
    public function getModels(): array
    {
        return cache()->remember('openrouter.models', now()->addHour(), function (): array {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . '/models');

            return collect($response->json('data', []))
                ->sortBy('name')
                ->map(fn (array $model): array => [
                    'id' => $model['id'],
                    'name' => $model['name'],
                    'description' => $model['description'] ?? '',
                    'context_length' => $model['context_length'] ?? 0,
                    'max_completion_tokens' => $model['top_provider']['max_completion_tokens'] ?? 0,
                    'input_modalities' => $model['architecture']['input_modalities'] ?? [],
                    'output_modalities' => $model['architecture']['output_modalities'] ?? [],
                    'supported_parameters' => $model['supported_parameters'] ?? [],
                    'supports_image' => 'text+image->text' === $model['architecture']['modality'],
                ])
                ->values()
                ->toArray()
            ;
        });
    }

    /**
     * Envoie un message et retourne la réponse du modèle.
     *
     * @param array<int, array{
     *     role: 'assistant'|'system'|'tool'|'user',
     *     content: array<int, array{
     *         type: 'image_url'|'text',
     *         text?: string,
     *         image_url?: array{url: string, detail?: string}
     *     }>|string
     * }> $messages
     */
    public function sendMessage(array $messages, ?string $model = null, float $temperature = 0.8): string
    {
        $model = $model ?? self::DEFAULT_MODEL;
        $messages = [$this->getSystemPrompt(), ...$messages];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'HTTP-Referer' => config('app.url'),
            'X-Title' => config('app.name'),
        ])
            ->timeout(120)
            ->post($this->baseUrl . '/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'temperature' => $temperature,
            ])
        ;

        // Gestion des erreurs
        if ($response->failed()) {
            $error = $response->json('error.message', 'Erreur inconnue');
            throw new \RuntimeException("Erreur API: {$error}");
        }

        return $response->json('choices.0.message.content', '');
    }

    /**
     * Génère un titre court pour une conversation.
     */
    public function generateTitle(string $firstMessage, ?string $model = null): string
    {
        $model = $model ?? self::DEFAULT_MODEL;
        
        $payload = [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Tu es un assistant qui génère des titres courts et concis pour des conversations. Réponds UNIQUEMENT avec le titre, sans guillemets, sans markdown, sans formatage supplémentaire. Maximum 8 mots.',
                ],
                [
                    'role' => 'user',
                    'content' => "Génère un titre court pour cette conversation : \"$firstMessage\"",
                ],
            ],
            'temperature' => 0.7,
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name'),
            ])
                ->timeout(120)
                ->post($this->baseUrl . '/chat/completions', $payload);

            if ($response->failed()) {
                $errorMessage = $response->json('error.message', 'Erreur API inconnue');
                Log::error('Erreur génération titre', [
                    'error' => $errorMessage,
                    'status' => $response->status(),
                ]);
                throw new \RuntimeException("Erreur API: {$errorMessage}");
            }

            $title = trim(strip_tags($response->json('choices.0.message.content', '')));
       
            // Si le titre est vide, utiliser un fallback
            if (empty($title)) {
                return \Illuminate\Support\Str::limit($firstMessage, 50);
            }
            
            return $title;
        } catch (\Exception $e) {
            Log::error('Exception génération titre', [
                'message' => $e->getMessage(),
                'model' => $model,
            ]);
            
            // Fallback sur les premiers mots du message
            return \Illuminate\Support\Str::limit($firstMessage, 50);
        }
    }

    /**
     * Retourne le prompt système.
     *
     * @return array{role: 'system', content: string}
     */
    private function getSystemPrompt(): array
    {
        $user = Auth::user();
        $userName = $user?->name ?? 'l\'utilisateur';
        $instructions = $user?->instructions ?? '';
        $now = now()->locale('fr')->format('l d F Y H:i');

        return [
            'role' => 'system',
            'content' => view('prompts.system', [
                'now' => $now,
                'user' => $userName,
                'instructions' => $instructions,
            ])->render(),
        ];
    }
}