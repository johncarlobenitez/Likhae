<?php

namespace App\Services\Communication;

use App\Events\MessageSent;
use App\Models\Communication\Conversation;
use App\Models\Communication\ConversationParticipant;
use App\Models\Communication\Message;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class ConversationService
{
    public function listFor(User $user, bool $includeMessages = true, ?int $limit = null): Collection
    {
        $query = Conversation::query()
            ->whereHas('participants', fn ($query) => $query->where('users.id', $user->id))
            ->with(['participants', 'latestMessage.sender'])
            ->latest('updated_at');

        if ($limit !== null) {
            $query->limit(max(1, min($limit, 100)));
        }

        $conversations = $query->get();

        if ($includeMessages) {
            $conversations->load('messages.sender');
        }

        return $conversations;
    }

    /** Load conversation summaries plus only the selected thread's recent messages. */
    public function workspaceThreads(User $user, ?int $selectedConversationId = null): Collection
    {
        $conversations = $this->listFor($user, false, 100);
        $active = $selectedConversationId
            ? $conversations->firstWhere('id', $selectedConversationId)
            : $conversations->first();

        if ($active) {
            $active->setRelation('messages', $this->messagesFor($user, (int) $active->id));
            $this->markRead($active, $user);
        }

        return $conversations;
    }

    /** Return a small authorized message window for the five-second fallback. */
    public function messagesFor(User $user, int $conversationId, int $limit = 100): Collection
    {
        $conversation = Conversation::query()
            ->select('conversations.id')
            ->whereKey($conversationId)
            ->whereHas('participants', fn ($query) => $query->where('users.id', $user->id))
            ->firstOrFail();

        return Message::query()
            ->where('conversation_id', $conversation->id)
            ->latest('sent_at')
            ->limit(max(1, min($limit, 100)))
            ->get(['id', 'conversation_id', 'sender_user_id', 'body', 'sent_at', 'created_at'])
            ->reverse()
            ->values();
    }

    public function messagePayload(Message $message): array
    {
        $sentAt = $message->sent_at ?? $message->created_at;

        return [
            'id' => (string) $message->id,
            'conversation_id' => (string) $message->conversation_id,
            'sender_id' => (string) $message->sender_user_id,
            'sender_user_id' => (string) $message->sender_user_id,
            'body' => $message->body,
            'time' => $sentAt?->diffForHumans() ?? 'Just now',
            'sent_at' => $sentAt?->toIso8601String(),
        ];
    }

    public function streamPayload(User $user, int $conversationId): Collection
    {
        return $this->messagesFor($user, $conversationId)
            ->map(fn (Message $message): array => $this->messagePayload($message));
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
        if ((int) $ids[0] === (int) $ids[1]) {
            throw new \InvalidArgumentException('A conversation requires two different accounts.');
        }

        $conversation = $this->findPairConversation($ids);

        if ($conversation) {
            return $conversation;
        }

        try {
            $conversation = Conversation::create([
                'type' => $context['type'] ?? 'DIRECT',
                'direct_pair_key' => $ids->implode(':'),
                'order_id' => $context['order_id'] ?? null,
                'seller_order_id' => $context['seller_order_id'] ?? null,
                'shipment_id' => $context['shipment_id'] ?? null,
                'created_by_user_id' => $creator->id,
            ]);
        } catch (QueryException $exception) {
            // A concurrent first message may win the unique pair key. Reuse
            // that committed conversation instead of creating a second one.
            if (! str_contains($exception->getMessage(), 'direct_pair_key')) {
                throw $exception;
            }

            $conversation = $this->findPairConversation($ids);
            if ($conversation) {
                return $conversation;
            }

            throw $exception;
        }

        foreach ($ids as $id) {
            ConversationParticipant::firstOrCreate([
                'conversation_id' => $conversation->id,
                'user_id' => $id,
            ], ['joined_at' => now()]);
        }

        return $conversation;
    }

    private function findPairConversation(Collection $ids): ?Conversation
    {
        return Conversation::query()
            ->whereHas('participants', fn ($query) => $query->where('users.id', $ids[0]))
            ->whereHas('participants', fn ($query) => $query->where('users.id', $ids[1]))
            ->whereDoesntHave('participants', fn ($query) => $query->whereNotIn('users.id', $ids->all()))
            ->oldest('id')
            ->first();
    }
}
