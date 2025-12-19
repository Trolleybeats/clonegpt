<?php

use App\Models\User;

it('allows authenticated user to see the dashboard', function () {
    $this->browse(function ($browser) {
        $user = User::factory()->create();
        $browser->loginAs($user)
                ->visit('/dashboard')
                ->assertSee('Dashboard');
    });
});

it('allows authenticated user to see the conversations index', function () {
    $this->browse(function ($browser) {
        $user = User::factory()->create();
        $browser->loginAs($user)
                ->visit('/conversations')
                ->assertSee('Conversations');
    });
});

it('allows authenticated user to see the conversation create page', function () {
    $this->browse(function ($browser) {
        $user = User::factory()->create();
        $browser->loginAs($user)
                ->visit('/conversations/create')
                ->assertSee('Nouvelle Conversation');
    });
});

it('allows authenticated user to see the instruction page', function () {
    $this->browse(function ($browser) {
        $user = User::factory()->create();
        $browser->loginAs($user)
                ->visit('/instructions')
                ->assertSee('Instructions');
    });
});

it('does not allow unauthenticated user to see the dashboard', function () {
    $this->browse(function ($browser) {
        $browser->visit('/dashboard')
                ->assertPathIs('/login');
    });
});

it('does not allow unauthenticated user to see the conversations index', function () {
    $this->browse(function ($browser) {
        $browser->visit('/conversations')
                ->assertPathIs('/login');
    });
});

it('does not allow unauthenticated user to see the conversation create page', function () {
    $this->browse(function ($browser) {
        $browser->visit('/conversations/create')
                ->assertPathIs('/login');
    });
});

it('does not allow unauthenticated user to see the instruction page', function () {
    $this->browse(function ($browser) {
        $browser->visit('/instructions')
                ->assertPathIs('/login');
    });
});