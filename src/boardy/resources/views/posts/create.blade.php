@extends('layouts.app')

@section('title', 'Создать пост')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Создать пост</h1>

    <form method="POST" action="{{ route('posts.store') }}" class="space-y-4 bg-white p-6 rounded-lg shadow">
        @csrf

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Заголовок</label>
            <input
                type="text"
                name="title"
                id="title"
                value="{{ old('title') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                required
            >
            @error('title')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="body" class="block text-sm font-medium text-gray-700">Текст</label>
            <textarea
                name="body"
                id="body"
                rows="8"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                required
            >{{ old('body') }}</textarea>
            @error('body')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-3">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">
                Сохранить
            </button>

            <a href="{{ route('posts.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md">
                Отмена
            </a>
        </div>
    </form>
@endsection
