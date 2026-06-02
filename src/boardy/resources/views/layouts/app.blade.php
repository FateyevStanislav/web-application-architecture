<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Boardy')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900">
    @include('layouts.navigation')

    <main class="max-w-5xl mx-auto px-4 py-6">
        @if (session('success'))
            <div class="mb-4 rounded-lg border border-green-300 bg-green-100 px-4 py-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-300 bg-red-100 px-4 py-3 text-red-800">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
