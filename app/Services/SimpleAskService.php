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
    /**
     * Optional SDK client that can produce StreamedResponse
     * (e.g., OpenRouter PHP SDK with chat()->createStreamed()).
     */
    private $client = null;

    public function __construct($client = null)
    {
        $this->apiKey = config('services.openrouter.api_key');
        $this->baseUrl = rtrim(config('services.openrouter.base_url', 'https://openrouter.ai/api/v1'), '/');
        $this->client = $client;
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
                ->timeout(60)
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

    /**
     * Version streaming qui retourne un générateur de chunks texte.
     * Adapte l'appel OpenRouter pour lire le flux SSE et extraire le contenu.
     *
     * @param array<int, array{role: 'assistant'|'system'|'tool'|'user', content: mixed}> $messages
     * @return \Generator<string>
     */
    /**
     * Version streaming retournant un StreamedResponse (SSE) si un client SDK est disponible,
     * sinon fallback en lisant le flux SSE via Http et en émettant les chunks.
     *
     * @param array<int, array{role: 'assistant'|'system'|'tool'|'user', content: mixed}> $messages
     */
    public function stream(array $messages, ?string $model = null, float $temperature = 0.7): StreamedResponse
    {
        try {
            Log::info('Envoi du message en streaming', [
                'model' => $model,
                'temperature' => $temperature,
            ]);

            $models = collect($this->getModels());
            if (!$model || !$models->contains('id', $model)) {
                $model = self::DEFAULT_MODEL;
                Log::info('Modèle par défaut utilisé:', ['model' => $model]);
            }

            $messages = [$this->getSystemPrompt(), ...$messages];

            // If an SDK client is available and supports createStreamed, use it to return a StreamedResponse
            if ($this->client && method_exists($this->client, 'chat')) {
                $chat = $this->client->chat();
                if ($chat && method_exists($chat, 'createStreamed')) {
                    /** @var StreamedResponse $stream */
                    $stream = $chat->createStreamed([
                        'model' => $model,
                        'messages' => $messages,
                        'temperature' => $temperature,
                        'stream' => true,
                    ]);
                    return $stream;
                }
            }

            // Fallback: manual SSE streaming via Laravel StreamedResponse
            $payload = [
                'model' => $model,
                'messages' => $messages,
                'temperature' => $temperature,
                'stream' => true,
            ];

            return new StreamedResponse(function () use ($payload) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                    'HTTP-Referer' => config('app.url'),
                    'X-Title' => config('app.name'),
                ])->send('POST', $this->baseUrl . '/chat/completions', [
                    'body' => json_encode($payload),
                    'stream' => true,
                    'timeout' => 120,
                ]);

                $body = $response->toPsrResponse()->getBody();

                while (!$body->eof()) {
                    $line = $body->read(1024);
                    foreach (preg_split('/\r?\n/', $line) as $l) {
                        $l = trim($l);
                        if (!str_starts_with($l, 'data:')) {
                            continue;
                        }
                        $json = trim(substr($l, 5));
                        if ($json === '[DONE]') {
                            break;
                        }
                        try {
                            $parsed = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
                            $delta = $parsed['choices'][0]['delta']['content'] ?? '';
                            if ($delta !== '') {
                                echo $delta;
                                flush();
                            }
                        } catch (\Throwable $t) {
                            // ignore invalid lines
                        }
                    }
                }
            }, 200, [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache, no-transform',
                'X-Accel-Buffering' => 'no',
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur dans sendMessageStream:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Version streaming qui renvoie un générateur de chunks texte.
     * Lit le flux SSE et extrait uniquement le contenu textuel.
     *
     * @param array<int, array{role: 'assistant'|'system'|'tool'|'user', content: mixed}> $messages
     * @return \Generator<string>
     */
    public function streamChunks(array $messages, ?string $model = null, float $temperature = 0.7): \Generator
    {
        $models = collect($this->getModels());
        if (!$model || !$models->contains('id', $model)) {
            $model = self::DEFAULT_MODEL;
        }

        $payload = [
            'model' => $model,
            'messages' => [$this->getSystemPrompt(), ...$messages],
            'temperature' => $temperature,
            'stream' => true,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'HTTP-Referer' => config('app.url'),
            'X-Title' => config('app.name'),
        ])->send('POST', $this->baseUrl . '/chat/completions', [
            'body' => json_encode($payload),
            'stream' => true,
            'timeout' => 120,
        ]);

        $body = $response->toPsrResponse()->getBody();
        $buffer = '';

        while (!$body->eof()) {
            $chunk = $body->read(1024);
            if ($chunk === '') {
                continue;
            }
            $buffer .= $chunk;

            while (false !== ($pos = strpos($buffer, "\n"))) {
                $line = trim(substr($buffer, 0, $pos));
                $buffer = substr($buffer, $pos + 1);

                if ($line === '' || !str_starts_with($line, 'data:')) {
                    continue;
                }

                $json = trim(substr($line, 5));
                if ($json === '[DONE]') {
                    return; // end of stream
                }
                try {
                    $parsed = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
                    $delta = $parsed['choices'][0]['delta']['content'] ?? '';
                    if ($delta !== '') {
                        yield $delta;
                    }
                } catch (\Throwable $t) {
                    // Skip malformed lines
                }
            }
        }
    }
}