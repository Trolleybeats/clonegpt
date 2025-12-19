<?php

it('displays the home page', function () {
    $this->browse(function ($browser) {
        $browser->visit('/')
                ->assertSee('RunAI');
    });
});

it('displays the login page', function () {
    $this->browse(function ($browser) {
        $browser->visit('/login')
                ->assertSee('Log in');
    });
});

it('displays the register page', function () {
    $this->browse(function ($browser) {
        $browser->visit('/register')
                ->assertSee('Create');
    });
});

it('displays the Policy page', function () {
    $this->browse(function ($browser) {
        $browser->visit('/Politique')
                ->assertSee('Politique');
    });
});

it('displays the legal mentions page', function () {
    $this->browse(function ($browser) {
        $browser->visit('/Mentions')
                ->assertSee('Mentions');
    });
});

it('displays the AI Act page', function () {
    $this->browse(function ($browser) {
        $browser->visit('/Act')
                ->assertSee('AI Act');
    });
});
