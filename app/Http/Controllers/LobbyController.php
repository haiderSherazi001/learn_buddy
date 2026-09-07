<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaitingQueue;

class LobbyController extends Controller
{
    public function index()
    {
        // ⚡ Expanded topic list!
        $popularTopics = [
            'HTML & CSS',
            'JavaScript',
            'TypeScript',
            'React',
            'Vue.js',
            'Node.js',
            'PHP',
            'Laravel',
            'Python',
            'Java',
            'C++ / C#',
            'SQL / Databases',
            'Data Science',
            'Machine Learning',
            'AWS / Cloud',
            'Docker / DevOps',
            'UI/UX Design',
            'General Study'
        ];

        $user = auth()->user();

        $activeQueue = WaitingQueue::where('user_id', $user->id)->first();

        $myRooms = $user->rooms()->with('users')->orderBy('created_at', 'desc')->get();

        return view('lobby.index', compact('popularTopics', 'activeQueue', 'myRooms'));
    }
}