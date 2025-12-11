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

    /**
     * Convert the message to the API format expected by the chat service.
     * Ensures role and content are normalized to strings.
     *
     * @return array{role:string, content:string}
     */
    public function toApiFormat(): array
    {
        return [
            'role' => $this->normalizeRole($this->role),
            'content' => $this->normalizeContent($this->content),
        ];
    }


    protected $casts = [
        'content' => 'array',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    private function normalizeRole(?string $role): string
    {
        $role = strtolower((string) $role);
        $allowed = [self::ROLE_USER, self::ROLE_ASSISTANT, self::ROLE_SYSTEM];
        return in_array($role, $allowed, true) ? $role : self::ROLE_USER;
    }

    private function normalizeContent($content): string
    {
        if ($content === null) {
            return '';
        }

        if (is_string($content)) {
            return $content;
        }

        if (is_array($content)) {
            $parts = [];
            foreach ($content as $part) {
                if (is_string($part)) {
                    $parts[] = $part;
                    continue;
                }
                if (is_array($part)) {
                    $type = $part['type'] ?? null;
                    if ($type === self::TYPE_TEXT) {
                        $text = $part['data'] ?? $part['text'] ?? '';
                        if (is_string($text)) {
                            $parts[] = $text;
                        }
                    }
                }
            }
            return implode("\n", $parts);
        }

        return (string) $content;
    }
}
