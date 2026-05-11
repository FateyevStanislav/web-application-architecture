@extends('layouts.app')

@section('title', 'Посты')

@section('content')
    <h1 class="mb-4">Лента постов</h1>

    @forelse ($posts as $post)
        <article class="card mb-3">
            <div class="card-body">
                <h2 class="h4">
                    <a href="{{ route('posts.show', $post) }}" class="text-decoration-none">{{ $post->title }}</a>
                </h2>
                <p class="mb-2">{{ \Illuminate\Support\Str::limit($post->body, 200) }}</p>
                <small class="text-muted">
                    {{ $post->author->name }} · {{ $post->created_at->format('d.m.Y H:i') }}
                </small>
            </div>
        </article>
    @empty
        <p>Постов пока нет.</p>
    @endforelse

    {{ $posts->links() }}
@endsection
