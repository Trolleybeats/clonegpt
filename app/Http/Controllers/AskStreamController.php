<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\SimpleAskStreamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Controller pour la démonstration du streaming SSE.
 *
 * Exemple pédagogique : streaming temps réel avec Laravel + Vue.
 */
class AskStreamController extends Controller
{
    public function __construct(
        private SimpleAskStreamService $streamService
    ) {}

    /**
     * Affiche la page de streaming.
     */
    public function index(Request $request): Response
    {
        $modelId = $request->input('model')
            ?? Auth::user()?->preferred_model
            ?? SimpleAskStreamService::DEFAULT_MODEL;

        return Inertia::render('conversations/show', [
            'models' => $this->streamService->getModelsLight(),
            'selectedModel' => $modelId,
            'selectedModelDetails' => fn() => $this->streamService->getModelDetails($modelId),
        ]);
    }

    /**
     * Endpoint de streaming.
     */
    public function stream(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:100000',
            'model' => 'required|string',
            'temperature' => 'nullable|numeric|min:0|max:2',
            'reasoning_effort' => 'nullable|string|in:low,medium,high',
            'conversation_id' => 'required|integer|exists:conversations,id',
        ]);

        // Update user's preferred model
        $user = Auth::user();
        if ($user && $user->preferred_model !== $validated['model']) {
            $user->update(['preferred_model' => $validated['model']]);
        }

        // Construire l'historique de la conversation (limité pour rester dans le contexte du modèle)
        $conversationId = (int) $validated['conversation_id'];
        $historyLimit = (int) env('CHAT_HISTORY_LIMIT', 20); // configurable via .env

        $history = \App\Models\Message::query()
            ->where('conversation_id', $conversationId)
            ->orderByDesc('id')
            ->take($historyLimit)
            ->get()
            ->reverse()
            ->values()
            ->map(fn($m) => [
                'role' => $m->role,
                'content' => $m->content,
            ])->toArray();

        // Ajouter le message courant de l'utilisateur à la suite
        $messages = array_merge($history, [[
            'role' => 'user',
            'content' => $validated['message'],
        ]]);
        $model = $validated['model'];
        $temperature = (float) ($validated['temperature'] ?? 1.0);
        $reasoningEffort = $validated['reasoning_effort'] ?? null;

        return response()->stream(
            function () use ($messages, $model, $temperature, $reasoningEffort, $conversationId): void {
                // La persistance (user + assistant) se fait en fin de stream côté service
                $this->streamService->streamToOutput(
                    messages: $messages,
                    model: $model,
                    temperature: $temperature,
                    reasoningEffort: $reasoningEffort,
                    persistConversationId: $conversationId,
                    persistUserContent: $messages[0]['content'] ?? ''
                );
            },
            headers: [
                'Content-Type' => 'text/plain; charset=utf-8',
                'Cache-Control' => 'no-cache, no-store',
                'X-Accel-Buffering' => 'no',
                'Connection' => 'keep-alive',
                'Content-Encoding' => 'none',
            ]
        );
    }
}