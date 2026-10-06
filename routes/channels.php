<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Communication\Conversation;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversations.{conversation}', function ($user, Conversation $conversation) {
    return $conversation->participants()
        ->whereKey($user->id)
        ->exists();
});
