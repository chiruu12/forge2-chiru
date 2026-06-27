<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Ticket $ticket): JsonResponse
    {
        $validated = $request->validate([
            'body' => ['required', 'string'],
            'is_internal' => ['sometimes', 'boolean'],
        ]);

        $comment = Comment::create(array_merge($validated, [
            'ticket_id' => $ticket->id,
            'author_id' => $request->user()->id,
        ]));

        return response()->json([
            'data' => $comment->load('author'),
        ], 201);
    }
}
