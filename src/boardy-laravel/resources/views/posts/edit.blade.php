@extends('layouts.app')

@section('title', 'Редактировать пост')

@section('content')
    <h1 class="mb-4">Редактировать пост</h1>

    <form method="POST" action="{{ route('posts.update', $post) }}" class="card">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="mb-3">
                <label for="title" class="form-label">Заголовок</label>
                <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}" class="form-control">
                @error('title')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="body" class="form-label">Текст</label>
                <textarea name="body" id="body" rows="8" class="form-control">{{ old('body', $post->body) }}</textarea>
                @error('body')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <button class="btn btn-primary">Сохранить</button>
        </div>
    </form>
@endsection
