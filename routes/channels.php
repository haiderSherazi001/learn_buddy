<?php

use App\Models\User;
use App\Models\Room;

Broadcast::channel('room.{roomId}', function (User $user, $roomId) {
    return $user->rooms->contains($roomId);
});

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
