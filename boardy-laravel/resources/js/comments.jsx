import React, { useEffect, useMemo, useState } from 'react'
import { getAccessToken, refreshToken, startLogin } from './auth'

// В Docker Nginx будет проксировать /api и /ws на FastAPI
const API_BASE = '/api'

export default function Comments({ postId, isAuthenticated, userName, initialComments = [] }) {
    const [comments, setComments] = useState(initialComments)
    const [body, setBody] = useState('')
    const [error, setError] = useState('')
    const [success, setSuccess] = useState('')
    const [loading, setLoading] = useState(false)

    const commentsCount = useMemo(() => comments.length, [comments])

    useEffect(() => {
        // WebSocket будет проксироваться Nginx с /ws на fastapi:8000/ws
        const wsProtocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:'
        const wsHost = window.location.host
        const wsUrl = `${wsProtocol}//${wsHost}/ws`

        const ws = new WebSocket(wsUrl)

        ws.onmessage = (event) => {
            try {
                const data = JSON.parse(event.data)

                if (data.type === 'new_comment' && Number(data.comment?.post_id) === Number(postId)) {
                    setComments((prev) => {
                        if (prev.some((c) => c.id === data.comment.id)) return prev
                        return [data.comment, ...prev]
                    })
                }

                if (data.type === 'update_comment') {
                    setComments((prev) =>
                        prev.map((c) =>
                            c.id === data.comment.id ? { ...c, body: data.comment.body } : c
                        )
                    )
                }

                if (data.type === 'delete_comment') {
                    setComments((prev) => prev.filter((c) => c.id !== data.comment_id))
                }

                if (data.type === 'user_renamed') {
                    setComments((prev) =>
                        prev.map((c) =>
                            Number(c.author_id) === Number(data.user_id)
                                ? { ...c, author_name: data.new_name }
                                : c
                        )
                    )
                }
            } catch (e) {
                // игнорируем некорректные сообщения
            }
        }

        ws.onerror = () => {}

        return () => ws.close()
    }, [postId])

    async function authedFetch(url, options = {}) {
        const doFetch = async (token) => {
            const headers = new Headers(options.headers || {})
            if (token) headers.set('Authorization', `Bearer ${token}`)
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
            const newToken = await refreshToken()

            if (!newToken) {
                await startLogin()
                throw new Error('Сессия истекла. Выполняется повторный вход.')
            }

            res = await doFetch(newToken)
        }

        return res
    }

    async function addComment(e) {
        e.preventDefault()
        setError('')
        setSuccess('')

        if (!body.trim()) {
            setError('Комментарий не может быть пустым.')
            return
        }

        setLoading(true)

        try {
            if (!isAuthenticated) {
                await startLogin()
                return
            }

            const res = await authedFetch(
                `${API_BASE}/posts/${postId}/comments`,
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        body: body.trim(),
                        author_name: userName,
                    }),
                }
            )

            if (!res.ok) {
                const text = await res.text()
                throw new Error(text || 'Не удалось отправить комментарий')
            }

            const data = await res.json()
            setComments((prev) => {
                if (prev.some((c) => c.id === data.id)) return prev
                return [data, ...prev]
            })
            setBody('')
            setSuccess('Комментарий успешно добавлен.')
        } catch (e) {
            setError(e.message || 'Ошибка при отправке комментария.')
        } finally {
            setLoading(false)
        }
    }

    return (
        <div>
            <div className="d-flex justify-content-between align-items-center mb-3">
                <h2 className="h4 mb-0">Комментарии</h2>
                <span className="text-muted small">{commentsCount} шт.</span>
            </div>

            <div className="mb-4">
                {comments.length === 0 ? (
                    <div className="card border-0 shadow-sm">
                        <div className="card-body text-muted">Комментариев пока нет.</div>
                    </div>
                ) : (
                    comments.map((comment) => (
                        <div key={comment.id} className="card border-0 shadow-sm mb-3">
                            <div className="card-body">
                                <div className="d-flex justify-content-between flex-wrap gap-2 mb-2">
                                    <strong>{comment.author_name || 'Без имени'}</strong>
                                    <span className="text-muted small">
                                        {comment.created_at_human || 'Только что'}
                                    </span>
                                </div>
                                <div style={{ whiteSpace: 'pre-line' }}>{comment.body}</div>
                            </div>
                        </div>
                    ))
                )}
            </div>

            {isAuthenticated ? (
                <div className="card border-0 shadow-sm">
                    <div className="card-body">
                        <h3 className="h5 mb-3">Добавить комментарий</h3>

                        {success && <div className="alert alert-success">{success}</div>}
                        {error && <div className="alert alert-danger">{error}</div>}

                        <form onSubmit={addComment}>
                            <div className="mb-3">
                                <label className="form-label">Текст комментария</label>
                                <textarea
                                    className="form-control"
                                    rows="4"
                                    required
                                    value={body}
                                    onChange={(e) => setBody(e.target.value)}
                                />
                            </div>

                            <button className="btn btn-primary" type="submit" disabled={loading}>
                                {loading ? 'Отправка...' : 'Отправить комментарий'}
                            </button>
                        </form>
                    </div>
                </div>
            ) : (
                <div className="alert alert-light border shadow-sm">
                    Чтобы оставить комментарий,{' '}
                    <button
                        type="button"
                        className="btn btn-link p-0 align-baseline"
                        onClick={startLogin}
                    >
                        войдите
                    </button>.
                </div>
            )}
        </div>
    )
}
