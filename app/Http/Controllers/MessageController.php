<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Message;
use App\Events\MessageSent;

class MessageController extends Controller
{
    public function store(Request $request, \App\Models\Room $room)
    {
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'room_id' => $room->id,
            'user_id' => auth()->id(),
            'body' => $request->body,
        ]);

        // Load the user data so the frontend knows who sent it
        $message->load('user');

        // ⚡ Fire the WebSocket Event!
        MessageSent::dispatch($message);

        // Return JSON instead of a redirect
        return response()->json([
            'message' => $message,
            'user' => $message->user
        ]);
    }
}
