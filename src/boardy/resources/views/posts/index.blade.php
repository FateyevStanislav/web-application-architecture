@extends('layouts.app')

@section('title', 'Лента постов')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Лента постов</h1>

        @auth
            <a href="{{ route('posts.create') }}"
               class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
                Создать пост
            </a>
        @endauth
    </div>

    <div id="posts-feed" class="space-y-4">
        @forelse ($posts as $post)
            <article class="rounded-lg bg-white p-4 shadow">
                <h3 class="text-lg font-semibold">{{ $post->title }}</h3>
                <p class="mt-2 text-gray-700 whitespace-pre-line">{{ $post->body }}</p>
                <small class="mt-3 block text-sm text-gray-500">
                    {{ $post->author->name ?? $post->user->name ?? 'Неизвестный автор' }}
                </small>
            </article>
        @empty
            <p class="text-gray-500">Постов пока нет.</p>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $posts->links() }}
    </div>
@endsection

@push('scripts')
<script>
const wsProtocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:'
const wsUrl = `${wsProtocol}//api.{{ config('app.fastapi_domain') }}/ws`
function connect() {
    const ws = new WebSocket(wsUrl)

    ws.onopen = () => console.log('WS connected')

    ws.onmessage = (e) => {
        const msg = JSON.parse(e.data)
        if (msg.type === 'new_post') {
            prependPost(msg.post)
        }
    }

    ws.onclose = () => {
        setTimeout(connect, 3000)
    }
}

function prependPost(post) {
    const feed = document.getElementById('posts-feed')
    if (!feed) return

    const el = document.createElement('article')
    el.className = 'rounded-lg bg-white p-4 shadow'

    el.innerHTML = `
        <h3 class="text-lg font-semibold">${escapeHtml(post.title)}</h3>
        <p class="mt-2 text-gray-700 whitespace-pre-line">${escapeHtml(post.body)}</p>
        <small class="mt-3 block text-sm text-gray-500">${escapeHtml(post.author ?? 'Неизвестный автор')}</small>
    `

    feed.prepend(el)
}

function escapeHtml(str) {
    const d = document.createElement('div')
    d.textContent = str ?? ''
    return d.innerHTML
}

connect()
</script>
@endpush
