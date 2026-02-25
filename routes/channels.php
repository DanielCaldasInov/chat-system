<?php

use App\Models\Room;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.room.{roomId}', function ($user, $roomId) {
    $room = Room::find($roomId);

    if ($room) {
        return $room->users->contains($user->id);
    }

    return false;
});
