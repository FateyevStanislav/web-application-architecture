<x-guest-layout>
    <div class="p-6 text-center">
        <h1 class="text-lg font-semibold">OAuth callback</h1>
        <p class="mt-2 text-sm text-gray-600">Exchanging authorization code for token...</p>
        <pre id="token-output" class="mt-4 text-left text-xs whitespace-pre-wrap"></pre>
    </div>

    <script type="module">
        import { handleCallback } from '/js/auth.js'

        const output = document.getElementById('token-output')

        try {
            const token = await handleCallback()

            if (token) {
                output.textContent = `access_token: ${token}`
                console.log('access_token =', token)
                setTimeout(() => {
                    window.location.href = '/posts'
                }, 1000)
            } else {
                output.textContent = 'No code in callback URL'
            }
        } catch (e) {
            console.error('OAuth callback failed:', e)
            output.textContent = 'OAuth callback failed: ' + e.message
        }
    </script>
</x-guest-layout>
