<?php

namespace App\Services\Communication;

use App\Models\Communication\Conversation;
use App\Models\Communication\ConversationParticipant;
use App\Models\Communication\Message;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ConversationService
{
    public function listFor(User $user): Collection
    {
        return Conversation::query()
            ->whereHas('participants', fn ($query) => $query->where('users.id', $user->id))
            ->with(['participants', 'latestMessage.sender', 'messages.sender'])
            ->latest('updated_at')
            ->get();
    }

    public function send(User $sender, int $recipientUserId, string $body, array $context = []): Message
    {
        return DB::transaction(function () use ($sender, $recipientUserId, $body, $context): Message {
            $ids = collect([$sender->id, $recipientUserId])->sort()->values();

            $conversation = Conversation::query()
                ->where('type', $context['type'] ?? 'DIRECT')
                ->when($context['order_id'] ?? null, fn ($q, $id) => $q->where('order_id', $id))
                ->when($context['seller_order_id'] ?? null, fn ($q, $id) => $q->where('seller_order_id', $id))
                ->when($context['shipment_id'] ?? null, fn ($q, $id) => $q->where('shipment_id', $id))
                ->whereHas('participants', fn ($q) => $q->where('users.id', $ids[0]))
                ->whereHas('participants', fn ($q) => $q->where('users.id', $ids[1]))
                ->first();

            if (! $conversation) {
                $conversation = Conversation::create([
                    'type' => $context['type'] ?? 'DIRECT',
                    'order_id' => $context['order_id'] ?? null,
                    'seller_order_id' => $context['seller_order_id'] ?? null,
                    'shipment_id' => $context['shipment_id'] ?? null,
                    'created_by_user_id' => $sender->id,
                ]);

                foreach ($ids as $id) {
                    ConversationParticipant::firstOrCreate([
                        'conversation_id' => $conversation->id,
                        'user_id' => $id,
                    ], ['joined_at' => now()]);
                }
            }

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_user_id' => $sender->id,
                'body' => $body,
                'sent_at' => now(),
            ]);

            $conversation->touch();

            ConversationParticipant::query()
                ->where('conversation_id', $conversation->id)
                ->where('user_id', $sender->id)
                ->update(['last_read_at' => now()]);

            return $message;
        });
    }

    public function markRead(Conversation $conversation, User $user): void
    {
        ConversationParticipant::query()
            ->where('conversation_id', $conversation->id)
            ->where('user_id', $user->id)
            ->update(['last_read_at' => now()]);
    }
}
