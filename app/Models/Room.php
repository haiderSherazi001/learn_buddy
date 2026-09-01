<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'type', 'status', 'creator_id', 'streak_count', 'last_streak_date', 'max_capacity', 'invite_code'
    ];
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function commitments()
    {
        return $this->hasMany(Commitment::class);
    }

    public function standups()
    {
        return $this->hasMany(Standup::class);
    }
    
    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    public function events()
    {
        return $this->hasMany(RoomEvent::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
