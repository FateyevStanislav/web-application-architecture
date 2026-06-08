@extends('layouts.app')

@section('title', 'Редактирование поста')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 mb-4">Редактировать пост</h1>

                    <form action="{{ route('posts.update', $post) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Заголовок</label>
                            <input
                                type="text"
                                id="title"
                                name="title"
                                value="{{ old('title', $post->title) }}"
                                class="form-control @error('title') is-invalid @enderror"
                                required
                                maxlength="200"
                            >
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="body" class="form-label">Текст поста</label>
                            <textarea
                                id="body"
                                name="body"
                                rows="10"
                                class="form-control @error('body') is-invalid @enderror"
                                required
                            >{{ old('body', $post->body) }}</textarea>
                            @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn btn-primary">Обновить</button>
                            <a href="{{ route('posts.show', $post) }}" class="btn btn-outline-secondary">Назад</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
