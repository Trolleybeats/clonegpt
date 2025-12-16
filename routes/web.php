<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\AskController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\InstructionController;
use App\Http\Controllers\MessageController;
use App\Models\Conversation;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    $nbrConversations = Conversation::where('user_id', auth()->id())->count();
    return Inertia::render('Dashboard', [
        'nbrConversations' => $nbrConversations,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

// Legal mentions page
Route::get('/Mentions', function () {
    return Inertia::render('Mentions');
})->name('Mentions');

// Privacy policy page
Route::get('/Politique', function () {
    return Inertia::render('Politique');
})->name('Politique');

// AI Act compliance & transparency page
Route::get('/Act', function () {
    return Inertia::render('Act');
})->name('Act');

Route::middleware('auth')->group(function() {
    Route::get('/ask', [AskController::class, 'index'])->name('ask.index');
    Route::post('/ask', [AskController::class, 'ask'])->name('ask.post');
});

Route::resource('conversations', ConversationController::class)->middleware('auth');
Route::resource('messages', MessageController::class)->middleware('auth');
Route::resource('instructions', InstructionController::class)->middleware('auth');

// Stream (utilisé par la vue conversations/show)
Route::post('/ask-stream', [\App\Http\Controllers\AskStreamController::class, 'stream'])
    ->middleware('auth')
    ->name('stream.post');

require __DIR__.'/settings.php';
