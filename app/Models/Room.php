<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'status',
        'creator_id',
        'streak_count',
        'last_streak_date'
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
}
