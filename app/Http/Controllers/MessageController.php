<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Mode "stream finish": on reçoit user_content + assistant_content et on persiste uniquement
        $validated = $request->validate([
            'conversation_id' => 'required|integer|exists:conversations,id',
            'user_content' => 'required|string',
            'assistant_content' => 'required|string',
            'model' => 'required|string',
        ]);

        $conversation = \App\Models\Conversation::with('messages')->findOrFail($validated['conversation_id']);
        $user = Auth::user();

        // Mettre à jour le modèle choisi
        $conversation->update(['model' => $validated['model']]);
        if ($user) {
            $user->update(['preferred_model' => $validated['model']]);
        }

        // Sauvegarder le message utilisateur
        Message::create([
            'content' => $validated['user_content'],
            'conversation_id' => $validated['conversation_id'],
            'role' => 'user',
        ]);

        // Sauvegarder la réponse (contenu déjà nettoyé côté client)
        Message::create([
            'content' => $validated['assistant_content'],
            'conversation_id' => $validated['conversation_id'],
            'role' => 'assistant',
        ]);

        // Générer un titre si c'est le premier échange
        if ($conversation->messages()->count() === 2 && (empty($conversation->title) || $conversation->title === 'Nouvelle conversation')) {
            try {
                $askService = app(\App\Services\SimpleAskService::class);
                $title = $this->generateTitle($askService, $validated['user_content'], $validated['model']);
                $conversation->update(['title' => $title]);
            } catch (\Exception $e) {
                $fallback = \Illuminate\Support\Str::limit($validated['user_content'], 150);
                $conversation->update(['title' => $fallback]);
            }
        }

        $conversation->touch();

        // Réponse JSON pour les appels fetch()
        return response()->json(['status' => 'ok']);
    }
    
    /**
     * Génère un titre pour la conversation basé sur le premier message
     */
    private function generateTitle($askService, string $firstMessage, string $model): string
    {
        try {
            return $askService->generateTitle($firstMessage, $model);
        } catch (\Exception $e) {
            // Si ça échoue, on utilise les premiers mots du message
            return \Illuminate\Support\Str::limit($firstMessage, 150);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Message $message)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $message->update([
            'content' => $validated['content'],
        ]);

        return redirect()->back()->with('success', 'Message updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
