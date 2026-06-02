import React from 'react'
import ReactDOM from 'react-dom/client'
import Comments from './comments'

const rootElement = document.getElementById('comments-app')

if (rootElement) {
    const postId = rootElement.dataset.postId
    const isAuthenticated = JSON.parse(rootElement.dataset.userAuth || 'false')
    const userName = JSON.parse(rootElement.dataset.userName || '""')
    const initialComments = JSON.parse(rootElement.dataset.initialComments || '[]')

    ReactDOM.createRoot(rootElement).render(
        <Comments
            postId={postId}
            isAuthenticated={isAuthenticated}
            userName={userName}
            initialComments={initialComments}
        />
    )
}
