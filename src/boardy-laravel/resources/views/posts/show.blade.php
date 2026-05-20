@extends('layouts.app')

@section('title', $post->title)

@section('content')
    <article class="card mb-4">
        <div class="card-body">
            <h1 class="mb-3">{{ $post->title }}</h1>
            <p>{{ $post->body }}</p>
            <small class="text-muted d-block mb-3">
                Автор: {{ $post->author->name }} · {{ $post->created_at->format('d.m.Y H:i') }}
            </small>

            @can('update', $post)
                <a href="{{ route('posts.edit', $post) }}" class="btn btn-primary btn-sm">Редактировать</a>
            @endcan

            @can('delete', $post)
                <form method="POST" action="{{ route('posts.destroy', $post) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm">Удалить</button>
                </form>
            @endcan
        </div>
    </article>

    <section class="mb-4">
        <h2 class="h4 mb-3">Комментарии</h2>

        @forelse ($post->comments as $comment)
            <div class="card mb-2">
                <div class="card-body">
                    <p class="mb-1">{{ $comment->body }}</p>
                    <small class="text-muted">
                        {{ $comment->author->name }} · {{ $comment->created_at->format('d.m.Y H:i') }}
                    </small>
                </div>
            </div>
        @empty
            <p>Комментариев пока нет.</p>
        @endforelse
    </section>

    @auth
        <section class="card">
            <div class="card-body">
                <h2 class="h5 mb-3">Новый комментарий</h2>
                <form method="POST" action="{{ route('comments.store') }}">
                    @csrf
                    <input type="hidden" name="post_id" value="{{ $post->id }}">

                    <div class="mb-3">
                        <label for="body" class="form-label">Текст комментария</label>
                        <textarea name="body" id="body" rows="4" class="form-control">{{ old('body') }}</textarea>
                        @error('body')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <button class="btn btn-success">Отправить</button>
                </form>
            </div>
        </section>
    @endauth
@endsection
