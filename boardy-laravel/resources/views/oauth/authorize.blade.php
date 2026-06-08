<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>OAuth Authorization</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f6f7fb;
            color: #222;
            margin: 0;
            padding: 40px 16px;
        }
        .card {
            max-width: 520px;
            margin: 0 auto;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0,0,0,.08);
            padding: 28px;
        }
        h1 {
            margin-top: 0;
            font-size: 24px;
        }
        p {
            line-height: 1.6;
            margin: 12px 0;
        }
        .actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }
        button, a.button-link {
            appearance: none;
            border: none;
            border-radius: 10px;
            padding: 12px 18px;
            font-size: 15px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .approve {
            background: #2563eb;
            color: white;
        }
        .deny {
            background: #e5e7eb;
            color: #111827;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Разрешить доступ приложению?</h1>

        <p>
            Приложение <strong>{{ $client->name ?? 'Unknown client' }}</strong>
            запрашивает доступ к вашему аккаунту.
        </p>

        @if (!empty($scopes))
            <p>Запрашиваемые права:</p>
            <ul>
                @foreach ($scopes as $scope)
                    <li>{{ $scope->description ?: $scope->id }}</li>
                @endforeach
            </ul>
        @endif

        <div class="actions">
            <form method="post" action="{{ route('passport.authorizations.approve') }}">
                @csrf
                <input type="hidden" name="state" value="{{ $request->state }}">
                <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
                <input type="hidden" name="auth_token" value="{{ $authToken }}">
                <button type="submit" class="approve">Разрешить</button>
            </form>

            <form method="post" action="{{ route('passport.authorizations.deny') }}">
                @csrf
                @method('DELETE')
                <input type="hidden" name="state" value="{{ $request->state }}">
                <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
                <input type="hidden" name="auth_token" value="{{ $authToken }}">
                <button type="submit" class="deny">Отмена</button>
            </form>
        </div>
    </div>
</body>
</html>
