<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomEvent extends Model
{
    use HasFactory;

    protected $fillable = ['room_id', 'message', 'type'];

    public function room() { return $this->belongsTo(Room::class); }
}
