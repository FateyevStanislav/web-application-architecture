@extends('layouts.app')

@section('title', $post->title)

@php
    $initialComments = $post->comments->map(function ($comment) {
        return [
            'id' => $comment->id,
            'post_id' => $comment->post_id,
            'author_id' => $comment->author_id,
            'author_name' => $comment->author->name ?? ($comment->author_name ?? 'Без имени'),
            'body' => $comment->body,
            'created_at_human' => optional($comment->created_at)->format('d.m.Y H:i'),
        ];
    })->values()->toArray();
@endphp

@section('content')
    <article class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <h1 class="h2 mb-2">{{ $post->title }}</h1>
                    <div class="text-muted small">
                        Автор: {{ $post->author->name }} · {{ $post->created_at->format('d.m.Y H:i') }}
                    </div>
                </div>

                @can('update', $post)
                    <a href="{{ route('posts.edit', $post) }}" class="btn btn-outline-primary btn-sm">
                        Редактировать
                    </a>
                @endcan
            </div>

            <div class="fs-6" style="white-space: pre-line;">{{ $post->body }}</div>

            @can('delete', $post)
                <form action="{{ route('posts.destroy', $post) }}" method="POST" class="mt-4"
                      onsubmit="return confirm('Удалить этот пост?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm">Удалить пост</button>
                </form>
            @endcan
        </div>
    </article>

    <section>
        <div
            id="comments-app"
            data-post-id="{{ $post->id }}"
            data-user-auth='@json(auth()->check())'
            data-user-name='@json(auth()->user()?->name ?? "")'
            data-initial-comments='@json($initialComments)'
        ></div>
    </section>
@endsection
