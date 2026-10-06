<?php

namespace App\Events;

use App\Models\Communication\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(public Message $message)
    {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('conversations.'.$this->message->conversation_id)];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        $message = $this->message->loadMissing('sender');
        $sentAt = $message->sent_at ?? $message->created_at;

        return [
            'id' => (string) $message->id,
            'conversation_id' => (string) $message->conversation_id,
            'sender_id' => (string) $message->sender_user_id,
            'sender_name' => $message->sender?->name ?? 'User',
            'body' => $message->body,
            'time' => $sentAt?->diffForHumans() ?? 'Just now',
            'sent_at' => $sentAt?->toIso8601String(),
        ];
    }
}
