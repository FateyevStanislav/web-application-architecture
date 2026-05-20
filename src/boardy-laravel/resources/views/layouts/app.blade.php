<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Boardy')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('posts.index') }}">Boardy</a>
        <div class="ms-auto d-flex align-items-center gap-3">
            @auth
                <a class="btn btn-sm btn-success" href="{{ route('posts.create') }}">Новый пост</a>
                <span class="text-white">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-sm btn-outline-light">Выйти</button>
                </form>
            @else
                <a class="btn btn-sm btn-outline-light" href="{{ route('login') }}">Вход</a>
                <a class="btn btn-sm btn-light" href="{{ route('register') }}">Регистрация</a>
            @endauth
        </div>
    </div>
</nav>

<main class="container">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @yield('content')
</main>

@yield('scripts')
</body>
</html>
