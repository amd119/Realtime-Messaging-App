<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('message.{id}', function ($user, $id) { // /{id}
    return (int) $user->id === (int) $id;
});

Broadcast::channel('online', function($user) {
    return $user->only(['id']);
});