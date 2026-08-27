<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaitingQueue;

class LobbyController extends Controller
{
    public function index()
    {
        $popularTopics = [
            'Laravel',
            'React',
            'Python',
            'JavaScript',
            'Vue.js'
        ];

        $user = auth()->user();

        $activeQueue = WaitingQueue::where('user_id', $user->id)->first();

        $myRooms = $user->rooms()->orderBy('created_at', 'desc')->get();

        return view('lobby.index', compact('popularTopics', 'activeQueue', 'myRooms'));
    }
}