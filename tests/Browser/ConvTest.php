<?php

use App\Models\User;

it('allows authenticated user to create a conversation and watch the streaming response with title generation', function () {
    $this->browse(function ($browser) {
        $user = User::factory()->create();
        $browser->loginAs($user)
                ->visit('/conversations/create')
                ->assertSee('Nouvelle Conversation')
                ->waitForReload(function ($browser) {
                    $browser->type('@content', 'Créé moi un poème sur les chats en 5 vers.')
                            ->press('@send')
                            ->pause(30000);
                })
                ->assertPathBeginsWith('/conversations/')
                ->assertSee('Créé moi un poème sur les chats en 5 vers.')
                ->pause(10000)
                ->assertDontSee('Nouvelle Conversation')
                ->pause(20000);
    });
});

it('allows authenticated user to delete a conversation', function () {
    $this->browse(function ($browser) {
        $user = User::factory()->create();
        $conversations = \App\Models\Conversation::factory()->create([
            'user_id' => $user->id,
        ]);
        $browser->loginAs($user)
                ->visit('/conversations')
                ->assertSee('Conversations')
                ->click('@actions-trigger')
                ->waitFor('@delete-conversation')
                ->click('@delete-conversation')
                ->pause(1000)
                ->click('@confirm-delete')
                ->pause(1000);
    });
});

it('allows authenticated user to rename a conversation', function () {
    $this->browse(function ($browser) {
        $user = User::factory()->create();
        $conversations = \App\Models\Conversation::factory()->create([
            'user_id' => $user->id,
        ]);
        $browser->loginAs($user)
                ->visit('/conversations')
                ->assertSee('Conversations')
                ->click('@actions-trigger')
                ->waitFor('@rename-conversation')
                ->click('@rename-conversation')
                ->pause(1000)
                ->type('@rename-input', 'Conversation Renommée')
                ->click('@confirm-rename')
                ->pause(1000)
                ->assertSee('Conversation Renommée')
                ->pause(1000);
    });
});

it('allows authenticated user to chat and change model', function () {
    $this->browse(function ($browser) {
        $user = User::factory()->create();
        $conversations = \App\Models\Conversation::factory()->create([
            'user_id' => $user->id,
        ]);
        $browser->loginAs($user)
                ->visit('/conversations/' . $conversations->id)
                ->assertSee('Conversations')
                ->whenAvailable('[role="dialog"][aria-label="Bandeau de consentement aux cookies"]', function ($modal) {
                    $modal->click('button');
                })
                ->pause(200)
            ->waitFor('@model-select')
            ->assertSelected('@model-select', 'openai/gpt-5-mini')
            ->select('@model-select', 'openai/gpt-4o-mini')
            ->assertSelected('@model-select', 'openai/gpt-4o-mini')
                ->waitForReload(function ($browser) {
                    $browser->type('@content', 'Créé moi un poème sur les chats.')
                            ->press('@send')
                            ->pause(20000);
                })
            ->assertSee('Créé moi un poème sur les chats.')
            ->assertSelected('@model-select', 'openai/gpt-4o-mini');
    });
});