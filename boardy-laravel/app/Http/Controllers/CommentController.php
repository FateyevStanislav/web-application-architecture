<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'body'    => 'required|string|max:1000',
        ]);

        $comment = $request->user()->comments()->create($data);
        $comment->load('author');

        if ($request->expectsJson()) {
            return response()->json([
                'id' => $comment->id,
                'body' => $comment->body,
                'created_at' => $comment->created_at?->toISOString(),
                'created_at_human' => $comment->created_at?->format('d.m.Y H:i'),
                'author' => [
                    'id' => $comment->author?->id,
                    'name' => $comment->author?->name,
                ],
                'post_id' => $comment->post_id,
            ], 201);
        }

        return back()->with('success', 'Комментарий добавлен');
    }
}
