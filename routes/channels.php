<?php

use App\Models\User;
use App\Models\Room;

Broadcast::channel('room.{roomId}', function (User $user, $roomId) {
    return $user->rooms->contains($roomId);
});
