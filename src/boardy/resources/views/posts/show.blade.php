@extends('layouts.app')

@section('title', $post->title)

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

    <section class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h4 mb-0">Комментарии</h2>
            <span class="text-muted small" id="comments-count">{{ $post->comments->count() }} шт.</span>
        </div>

        <div id="comments-list">
            @forelse ($post->comments as $comment)
                <div class="card border-0 shadow-sm mb-3 comment-box">
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
                            <strong>{{ $comment->author->name }}</strong>
                            <span class="text-muted small">{{ $comment->created_at->format('d.m.Y H:i') }}</span>
                        </div>
                        <div style="white-space: pre-line;">{{ $comment->body }}</div>
                    </div>
                </div>
            @empty
                <div class="card border-0 shadow-sm" id="no-comments-box">
                    <div class="card-body text-muted">
                        Комментариев пока нет.
                    </div>
                </div>
            @endforelse
        </div>
    </section>

    <section>
        @auth
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="h5 mb-3">Добавить комментарий</h3>

                    <div id="comment-success" class="alert alert-success d-none" role="alert"></div>
                    <div id="comment-error" class="alert alert-danger d-none" role="alert"></div>

                    <form id="comment-form">
                        @csrf
                        <input type="hidden" name="post_id" id="post_id" value="{{ $post->id }}">

                        <div class="mb-3">
                            <label for="body" class="form-label">Текст комментария</label>
                            <textarea
                                name="body"
                                id="body"
                                rows="4"
                                class="form-control"
                                required
                            >{{ old('body') }}</textarea>
                            <div class="invalid-feedback d-none" id="body-error"></div>
                        </div>

                        <button class="btn btn-primary" id="comment-submit-btn" type="submit">
                            Отправить комментарий
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="alert alert-light border shadow-sm">
                Чтобы оставить комментарий, <a href="{{ route('login') }}">войдите</a> или
                <a href="{{ route('register') }}">зарегистрируйтесь</a>.
            </div>
        @endauth
    </section>
@endsection

@auth
    @push('scripts')
        <script type="module">
            import { getAccessToken, refreshToken, startLogin } from '/js/auth.js'

            const form = document.getElementById('comment-form')
            const bodyInput = document.getElementById('body')
            const postIdInput = document.getElementById('post_id')
            const submitBtn = document.getElementById('comment-submit-btn')
            const errorBox = document.getElementById('comment-error')
            const successBox = document.getElementById('comment-success')
            const bodyError = document.getElementById('body-error')
            const commentsList = document.getElementById('comments-list')
            const commentsCount = document.getElementById('comments-count')

            function showError(message) {
                errorBox.textContent = message
                errorBox.classList.remove('d-none')
            }

            function hideError() {
                errorBox.textContent = ''
                errorBox.classList.add('d-none')
            }

            function showSuccess(message) {
                successBox.textContent = message
                successBox.classList.remove('d-none')
            }

            function hideSuccess() {
                successBox.textContent = ''
                successBox.classList.add('d-none')
            }

            function showBodyError(message) {
                bodyInput.classList.add('is-invalid')
                bodyError.textContent = message
                bodyError.classList.remove('d-none')
            }

            function hideBodyError() {
                bodyInput.classList.remove('is-invalid')
                bodyError.textContent = ''
                bodyError.classList.add('d-none')
            }

            function escapeHtml(value) {
                return String(value)
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;')
            }

            function nl2br(value) {
                return escapeHtml(value).replace(/\n/g, '<br>')
            }

            function updateCommentsCount() {
                const current = document.querySelectorAll('.comment-box').length
                commentsCount.textContent = `${current} шт.`
            }

            function prependComment(comment) {
                const emptyBox = document.getElementById('no-comments-box')
                if (emptyBox) {
                    emptyBox.remove()
                }

                const wrapper = document.createElement('div')
                wrapper.className = 'card border-0 shadow-sm mb-3 comment-box'
                wrapper.innerHTML = `
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
                            <strong>${escapeHtml(comment.author?.name ?? 'Вы')}</strong>
                            <span class="text-muted small">${escapeHtml(comment.created_at_human ?? 'Только что')}</span>
                        </div>
                        <div style="white-space: pre-line;">${nl2br(comment.body ?? '')}</div>
                    </div>
                `
                commentsList.prepend(wrapper)
                updateCommentsCount()
            }

            async function authedFetch(url, options = {}) {
                const doFetch = async (token) => {
                    const headers = new Headers(options.headers || {})

                    if (token) {
                        headers.set('Authorization', `Bearer ${token}`)
                    }

                    headers.set('Accept', 'application/json')

                    return fetch(url, {
                        ...options,
                        headers,
                        credentials: 'include',
                    })
                }

                let token = getAccessToken()
                let res = await doFetch(token)

                if (res.status === 401) {
                    token = await refreshToken()

                    if (!token) {
                        await startLogin()
                        throw new Error('Сессия истекла. Выполняется повторный вход.')
                    }

                    res = await doFetch(token)
                }

                return res
            }

            form?.addEventListener('submit', async (e) => {
                e.preventDefault()

                hideError()
                hideSuccess()
                hideBodyError()

                submitBtn.disabled = true
                submitBtn.textContent = 'Отправка...'

                try {
                    const payload = {
                        post_id: postIdInput.value,
                        body: bodyInput.value.trim(),
                    }

                    const res = await authedFetch('{{ route('comments.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify(payload),
                    })

                    if (res.status === 422) {
                        const data = await res.json()
                        if (data.errors?.body?.[0]) {
                            showBodyError(data.errors.body[0])
                            return
                        }
                        throw new Error(data.message || 'Ошибка валидации')
                    }

                    if (!res.ok) {
                        const data = await res.text()
                        throw new Error(data || 'Не удалось отправить комментарий')
                    }

                    const data = await res.json()

                    prependComment(data)
                    bodyInput.value = ''
                    showSuccess('Комментарий успешно добавлен.')
                } catch (error) {
                    showError(error.message || 'Произошла ошибка при отправке комментария.')
                } finally {
                    submitBtn.disabled = false
                    submitBtn.textContent = 'Отправить комментарий'
                }
            })
        </script>
    @endpush
@endauth
