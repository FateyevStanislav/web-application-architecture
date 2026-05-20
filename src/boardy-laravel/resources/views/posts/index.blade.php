@extends('layouts.app')

@section('title', 'Посты')

@section('content')
    <h1 class="mb-4">Лента постов</h1>

    <div id="posts-feed">
        @foreach($posts as $post)
            <article class="card">
                <h3>{{ $post->title }}</h3>
                <p>{{ $post->body }}</p>
                <small>{{ $post->author->name }}</small>
            </article>
        @endforeach
    </div>

    {{ $posts->links() }}
@endsection

@section('scripts')
<script>
@if(app()->environment('production'))
const wsUrl = 'wss://api.{{ config("app.fastapi_domain") }}/ws'
@else
const wsUrl = 'ws://localhost:8000/ws'
@endif

function connect() {
    const ws = new WebSocket(wsUrl)
    ws.onopen = () => console.log('WS connected')
    ws.onmessage = (e) => {
        const msg = JSON.parse(e.data)
        if (msg.type === 'new_post') prependPost(msg.post)
    }
    ws.onclose = () => setTimeout(connect, 3000)
}

function prependPost(post) {
    const feed = document.getElementById('posts-feed')
    if (!feed) return
    const el = document.createElement('article')
    el.className = 'card'
    el.innerHTML = `
        <h3>${escapeHtml(post.title)}</h3>
        <p>${escapeHtml(post.body)}</p>
        <small>${escapeHtml(post.author)}</small>`
    feed.prepend(el)
}

function escapeHtml(str) {
    const d = document.createElement('div')
    d.textContent = str
    return d.innerHTML
}

connect()
</script>
@endsection
