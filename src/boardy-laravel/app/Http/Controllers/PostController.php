<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $posts = Post::with('author')
            ->latest()
            ->paginate(10);

        return view('posts.index', compact('posts'));
    }

    public function create(): View
    {
        $this->authorize('create', Post::class);

        return view('posts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'body'  => 'required',
        ]);

        $post = Post::create([
            'title'   => $validated['title'],
            'body'    => $validated['body'],
            'user_id' => auth()->id(),
        ]);

        try {
            Http::timeout(2)->post('http://localhost:8000/internal/broadcast', [
                'id'         => $post->id,
                'title'      => $post->title,
                'body'       => $post->body,
                'author'     => auth()->user()->name,
                'created_at' => $post->created_at->toISOString(),
            ]);
        } catch (\Exception $e) {
            \Log::warning('WS broadcast failed: ' . $e->getMessage());
        }

        return redirect('/posts')->with('success', 'Пост создан');
    }

    public function show(Post $post): View
    {
        $post->load('author', 'comments.author');

        return view('posts.show', compact('post'));
    }

    public function edit(Post $post): View
    {
        $this->authorize('update', $post);

        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);

        $data = $request->validate([
            'title' => 'required|string|max:200',
            'body' => 'required|string|max:5000',
        ]);

        $post->update($data);

        return redirect()->route('posts.show', $post)
            ->with('success', 'Пост обновлён');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', 'Пост удалён');
    }
}
