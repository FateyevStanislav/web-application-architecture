<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boardy</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; }
        nav { background: #1A5276; padding: 1rem 2rem; display: flex; align-items: center; gap: 2rem; flex-wrap: wrap; }
        nav .brand { font-size: 1.5rem; font-weight: bold; color: white; text-decoration: none; }
        nav a { color: #ecf0f1; text-decoration: none; padding: 0.5rem 1rem; border-radius: 4px; }
        nav a:hover { background: #2E86C1; }
        nav span { color: white; margin-left: auto; }
        main { max-width: 800px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; border-radius: 8px; padding: 1.5rem; margin-bottom: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .post { border-left: 4px solid #1A5276; }
        .post-body { font-size: 1.1rem; margin-bottom: 0.5rem; }
        .post-meta { color: #7f8c8d; font-size: 0.85rem; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: #2c3e50; }
        input, textarea { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem; }
        textarea { min-height: 100px; resize: vertical; }
        button { background: #1A5276; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 4px; font-size: 1rem; cursor: pointer; }
        button:hover { background: #2E86C1; }
        .error { background: #e74c3c; color: white; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; }
        .success { background: #27ae60; color: white; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; }
        .form-link { text-align: center; margin-top: 1rem; color: #7f8c8d; }
        .form-link a { color: #1A5276; text-decoration: none; }
        h1 { color: #2c3e50; margin-bottom: 1.5rem; }
    </style>
</head>
<body>
