import { generateVerifier, generateChallenge, generateState } from './pkce.js'

const CLIENT_ID = '019e84bc-2b41-714e-9c03-abf49e050408'
const REDIRECT_URI = window.location.origin + '/oauth/callback'

let accessToken = null

export function setAccessToken(token) {
    accessToken = token
}

export function getAccessToken() {
    return accessToken
}

export async function startLogin() {
    const verifier = generateVerifier()
    const challenge = await generateChallenge(verifier)
    const state = generateState()

    sessionStorage.setItem('pkce_verifier', verifier)
    sessionStorage.setItem('oauth_state', state)

    const params = new URLSearchParams({
        client_id: CLIENT_ID,
        response_type: 'code',
        redirect_uri: REDIRECT_URI,
        code_challenge: challenge,
        code_challenge_method: 'S256',
        state: state,
        scope: '*',
    })

    window.location = '/oauth/authorize?' + params.toString()
}

export async function handleCallback() {
    const params = new URLSearchParams(window.location.search)
    const code = params.get('code')
    const state = params.get('state')

    if (!code) return null

    const savedState = sessionStorage.getItem('oauth_state')
    if (state !== savedState) {
        throw new Error('Invalid state — CSRF attack?')
    }

    const verifier = sessionStorage.getItem('pkce_verifier')
    if (!verifier) {
        throw new Error('No verifier')
    }

    const res = await fetch('/oauth/token', {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            grant_type: 'authorization_code',
            client_id: CLIENT_ID,
            code,
            code_verifier: verifier,
            redirect_uri: REDIRECT_URI,
        })
    })

    if (!res.ok) {
        throw new Error('Token exchange failed')
    }

    const data = await res.json()

    sessionStorage.removeItem('pkce_verifier')
    sessionStorage.removeItem('oauth_state')

    accessToken = data.access_token ?? null
    return accessToken
}

export async function refreshToken() {
    const res = await fetch('/oauth/token', {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            grant_type: 'refresh_token',
            client_id: CLIENT_ID,
        })
    })

    if (!res.ok) {
        await startLogin()
        return null
    }

    const data = await res.json()
    accessToken = data.access_token ?? null
    return accessToken
}
