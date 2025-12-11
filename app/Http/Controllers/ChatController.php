<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMessageRequest;
use App\Services\SimpleAskService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ChatController extends Controller
{
    use AuthorizesRequests;
    protected SimpleAskService $simpleAskService;

    public function __construct(SimpleAskService $simpleAskService)
    {
        $this->simpleAskService = $simpleAskService;
    }

    public function sendMessageStream(StoreMessageRequest $request, Conversation $conversation)
{
    $this->authorize('update', $conversation);
    
    // Créer le message utilisateur
    Message::create([
        'conversation_id' => $conversation->id,
        'role' => Message::ROLE_USER,
        'content' => [
            [
                'type' => Message::TYPE_TEXT,
                'data' => $request->text,
            ],
        ],
    ]);
    
    // Préparer les messages pour l'API
    $apiMessages = $conversation
        ->messages()
        ->get()
        ->map(fn (Message $message) => $message->toApiFormat())
        ->toArray();
    
    return response()->stream(function () use ($conversation, $apiMessages, $request) {
        $fullResponse = '';
        
        // Utiliser le générateur de chunks pour unifier le flux
        $stream = $this->simpleAskService->streamChunks(
            messages: $apiMessages,
            model: $conversation->model ?? $conversation->model_id,
            temperature: method_exists($conversation, 'getTemperature') ? $conversation->getTemperature() : null
        );
        
        foreach ($stream as $chunk) {
            $fullResponse .= $chunk;
            yield $chunk;
        }
        
        // Créer le message de l'assistant avec la réponse complète
        Message::create([
            'conversation_id' => $conversation->id,
            'role' => Message::ROLE_ASSISTANT,
            'content' => [
                [
                    'type' => Message::TYPE_TEXT,
                    'data' => $fullResponse,
                ],
            ],
        ]);

        // Génération automatique du titre si nécessaire
        try {
            // Générer si le titre est vide OU encore celui par défaut
            if (empty($conversation->title) || $conversation->title === 'Nouvelle conversation') {
                // Utiliser le premier message utilisateur pour générer le titre
                $firstUserMessage = $conversation->messages()
                    ->where('role', Message::ROLE_USER)
                    ->orderBy('created_at', 'asc')
                    ->first();

                $firstText = '';
                if ($firstUserMessage) {
                    $content = $firstUserMessage->content;
                    if (is_array($content) && isset($content[0]['data'])) {
                        $firstText = (string) $content[0]['data'];
                    } elseif (is_string($content)) {
                        $firstText = $content;
                    }
                } else {
                    $firstText = (string) $request->validated('text');
                }

                if (!empty($firstText)) {
                    $title = $this->simpleAskService->generateTitle($firstText, $conversation->model ?? $conversation->model_id);
                    $conversation->title = $title;
                    $conversation->save();
                }
            }
        } catch (\Throwable $t) {
            // Ignorer silencieusement les erreurs de génération de titre
        }
    }, 200, [
        'Content-Type' => 'text/event-stream',
        'Cache-Control' => 'no-cache, no-transform',
        'X-Accel-Buffering' => 'no',
    ]);
}
}
