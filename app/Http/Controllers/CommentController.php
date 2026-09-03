<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Standup;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(Request $request, Standup $standup)
    {
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        if (!$standup->room->users->contains(auth()->id())) {
            abort(403, 'You cannot comment in this room.');
        }

        $comment = Comment::create([
            'user_id' => auth()->id(),
            'standup_id' => $standup->id,
            'body' => $request->body,
        ]);

        // ⚡ NEW: Broadcast the reply!
        broadcast(new \App\Events\StandupReplyCreated($comment, $standup->room_id))->toOthers();

        // ⚡ NEW: Return JSON
        return response()->json([
            'comment' => $comment->load('user'),
            'standup_id' => $standup->id
        ]);
    }
}