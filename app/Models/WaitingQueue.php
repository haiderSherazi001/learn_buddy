<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaitingQueue extends Model
{
    protected $fillable = ['user_id', 'topic'];
}
