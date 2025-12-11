<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Conversation $conversation)
    {
        $conversations = Conversation::where('user_id', auth()->id())
            ->orderBy('updated_at', 'desc')
            ->get();
        return Inertia::render('conversations/index', [
            'conversations' => $conversations,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $selectedModel = auth()->user()->preferred_model 
            ?? \App\Services\SimpleAskService::DEFAULT_MODEL;
        
        return Inertia::render('conversations/create', [
            'models' => app(\App\Services\SimpleAskService::class)->getModels(),
            'selectedModel' => $selectedModel,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'model' => 'required|string',
        ]);
        
        // Créer la conversation
        $conversation = Conversation::create([
            'title' => 'Nouvelle conversation',
            'user_id' => auth()->id(),
            'model' => $validated['model'],
        ]);

        // Rediriger d'abord vers la vue show avec un prefill
        // afin que l'UI démarre le stream du premier message
        return redirect()
            ->route('conversations.show', $conversation)
            ->with('prefill', [
                'text' => $validated['content'],
                'model' => $validated['model'],
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Conversation $conversation)
    {
        $conversation->load('messages');
        
        // Utiliser le modèle de la conversation, sinon le préféré de l'utilisateur, sinon le défaut
        $selectedModel = $conversation->model 
            ?? auth()->user()->preferred_model 
            ?? \App\Services\SimpleAskService::DEFAULT_MODEL;
        
        return Inertia::render('conversations/show', [
            'conversation' => $conversation,
            'models' => app(\App\Services\SimpleAskService::class)->getModels(),
            'selectedModel' => $selectedModel,
            // Permet d'auto-démarrer le stream si on arrive depuis create
            'prefill' => session('prefill'),
        ]);
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
    public function update(Request $request, Conversation $conversation)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $conversation->update($validated);

        return redirect()->route('conversations.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Conversation $conversation)
    {
        $conversation->delete();
        
        return redirect()->route('conversations.index')
            ->with('success', 'Conversation deleted successfully.');

    }
}
