<?php

use Illuminate\Support\Facades\Broadcast;
use Namu\WireChat\Models\Conversation;

Broadcast::routes();

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('wirechat.conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);

    if (!$conversation) {
        return false;
    }

    // Check if the user is a participant in the conversation
    return $conversation->participants->contains($user->id);
});