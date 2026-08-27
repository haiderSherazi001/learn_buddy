<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        return view('lobby.index', compact('popularTopics'));
    }
}