<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    /** @use HasFactory<\Database\Factories\MessageFactory> */
    use HasFactory;

    public const ROLE_USER = 'user';
    public const ROLE_ASSISTANT = 'assistant';
    public const ROLE_SYSTEM = 'system';

    public const TYPE_TEXT = 'text';

    protected $fillable = [
        'conversation_id',
        'role',
        'content',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}