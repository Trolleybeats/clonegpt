<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

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
        $validated = $request->validate([
            'content' => 'required|string',
            'conversation_id' => 'required|integer|exists:conversations,id',
            'model' => 'required|string',
        ]);

        $conversation = \App\Models\Conversation::with('messages')->findOrFail($validated['conversation_id']);
        $user = auth()->user();
        
        // Sauvegarder le modèle sélectionné
        $conversation->update(['model' => $validated['model']]);
        $user->update(['preferred_model' => $validated['model']]);
        
        // Préparer l'historique des messages pour l'API AVANT d'ajouter le nouveau
        $messages = $conversation->messages->map(fn($msg) => [
            'role' => $msg->role,
            'content' => $msg->content,
        ])->toArray();
        
        // Ajouter le nouveau message utilisateur à l'historique
        $messages[] = [
            'role' => 'user',
            'content' => $validated['content'],
        ];
        
        // Sauvegarder le message de l'utilisateur en DB
        $userMessage = Message::create([
            'content' => $validated['content'],
            'conversation_id' => $validated['conversation_id'],
            'role' => 'user',
        ]);

        $response = null;
        $error = null;

        try {
            // Envoyer à l'API
            $askService = app(\App\Services\SimpleAskService::class);
            $response = $askService->sendMessage(
                messages: $messages,
                model: $validated['model']
            );

            // Sauvegarder la réponse de l'assistant
            Message::create([
                'content' => $response,
                'conversation_id' => $validated['conversation_id'],
                'role' => 'assistant',
            ]);
            
            // Générer un titre automatiquement si c'est le premier message
            if ($conversation->messages()->count() === 2 && 
                (empty($conversation->title) || $conversation->title === 'Nouvelle conversation')) {
                $title = $this->generateTitle($askService, $validated['content'], $validated['model']);
                $conversation->update(['title' => $title]);
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        $conversation->touch(); // Met à jour updated_at
        
        return redirect()->back();
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
            return \Illuminate\Support\Str::limit($firstMessage, 50);
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
