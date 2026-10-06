<?php

namespace App\Services\Communication;

use App\Events\MessageSent;
use App\Models\Communication\Conversation;
use App\Models\Communication\ConversationParticipant;
use App\Models\Communication\Message;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

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
        $message = DB::transaction(function () use ($sender, $recipientUserId, $body, $context): Message {
            $conversation = $this->findOrCreate($sender, $recipientUserId, $context);

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

        try {
            MessageSent::dispatch($message->load('sender'));
        } catch (Throwable $exception) {
            // A WebSocket outage must not make a committed web or mobile message fail.
            report($exception);
        }

        return $message;
    }

    /** Start a thread before either participant has written a message. */
    public function start(User $creator, int $recipientUserId, array $context = []): Conversation
    {
        return DB::transaction(fn (): Conversation => $this->findOrCreate($creator, $recipientUserId, $context));
    }

    /** Preserve a thread's order metadata when a participant replies. */
    public function contextFor(Conversation $conversation): array
    {
        return [
            'type' => $conversation->type,
            'order_id' => $conversation->order_id,
            'seller_order_id' => $conversation->seller_order_id,
            'shipment_id' => $conversation->shipment_id,
        ];
    }

    public function markRead(Conversation $conversation, User $user): void
    {
        ConversationParticipant::query()
            ->where('conversation_id', $conversation->id)
            ->where('user_id', $user->id)
            ->update(['last_read_at' => now()]);
    }

    private function findOrCreate(User $creator, int $recipientUserId, array $context): Conversation
    {
        $ids = collect([$creator->id, $recipientUserId])->sort()->values();

        $conversation = Conversation::query()
            ->where('type', $context['type'] ?? 'DIRECT')
            ->when($context['order_id'] ?? null, fn ($query, $id) => $query->where('order_id', $id))
            ->when($context['seller_order_id'] ?? null, fn ($query, $id) => $query->where('seller_order_id', $id))
            ->when($context['shipment_id'] ?? null, fn ($query, $id) => $query->where('shipment_id', $id))
            ->whereHas('participants', fn ($query) => $query->where('users.id', $ids[0]))
            ->whereHas('participants', fn ($query) => $query->where('users.id', $ids[1]))
            ->first();

        if ($conversation) {
            return $conversation;
        }

        $conversation = Conversation::create([
            'type' => $context['type'] ?? 'DIRECT',
            'order_id' => $context['order_id'] ?? null,
            'seller_order_id' => $context['seller_order_id'] ?? null,
            'shipment_id' => $context['shipment_id'] ?? null,
            'created_by_user_id' => $creator->id,
        ]);

        foreach ($ids as $id) {
            ConversationParticipant::firstOrCreate([
                'conversation_id' => $conversation->id,
                'user_id' => $id,
            ], ['joined_at' => now()]);
        }

        return $conversation;
    }
}
