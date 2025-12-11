<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;

class ConversationPolicy
{
    /**
     * Determine whether the user can view the conversation.
     */
    public function view(User $user, Conversation $conversation): bool
    {
        return (int) $conversation->user_id === (int) $user->id;
    }

    /**
     * Determine whether the user can update the conversation (send messages/stream).
     */
    public function update(User $user, Conversation $conversation): bool
    {
        return (int) $conversation->user_id === (int) $user->id;
    }
}
