<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Gate;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('author')
            ->latest()
            ->paginate(10);

        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        Gate::authorize('create', Post::class);
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'body'  => 'required',
        ]);

        $post = Post::create([
            'title' => $validated['title'],
            'body'  => $validated['body'],
            'user_id' => auth()->id(),
        ]);

        Redis::publish('new_post', json_encode([
            'id' => $post->id,
            'title' => $post->title,
            'body' => $post->body,
            'author' => auth()->user()->name,
            'created_at' => $post->created_at->toISOString(),
        ]));

        return redirect('/posts');
    }

    public function show(Post $post)
    {
        $post->load('author', 'comments.author');

        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        Gate::authorize('update', $post);

        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        Gate::authorize('update', $post);

        $data = $request->validate([
            'title' => 'required|string|max:200',
            'body' => 'required|string|max:5000',
        ]);

        $post->update($data);
        $post->load('author');
       
        Redis::publish('update_post', json_encode([
            'id' => $post->id,
            'title' => $post->title,
            'body' => $post->body,
            'author' => $post->author->name,
            'created_at' => $post->created_at->toISOString(),
       ]));
       
        return redirect()->route('posts.show', $post)
            ->with('success', 'Пост обновлён');
    }
    
    public function destroy(Post $post) 
    {
        Gate::authorize('delete', $post);

        $postId = $post->id;
        $post->delete();

        Redis::publish('delete_post', json_encode([
            'id' => $postId,
        ]));

        return redirect()->route('posts.index')->with('success', 'Пост удалён');
    }
}
