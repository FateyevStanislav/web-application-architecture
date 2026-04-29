<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

require_once __DIR__ . '/db.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $body  = trim($_POST['body'] ?? '');

    if (empty($title)) {
        $error = 'Заголовок не может быть пустым';
    } elseif (empty($body)) {
        $error = 'Текст поста не может быть пустым';
    } else {
        $stmt = $pdo->prepare('INSERT INTO posts (title, body, author_id) VALUES (?, ?, ?)');
        $stmt->execute([$title, $body, $_SESSION['user_id']]);
        $success = 'Пост успешно добавлен';
        $_POST['title'] = '';
        $_POST['body']  = '';
    }
}
?>
<?php include __DIR__ . '/partials/head.php'; ?>
<?php include __DIR__ . '/partials/nav.php'; ?>
<main>
    <div class="card">
        <h1>Добавить пост</h1>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="title">Заголовок</label>
                <input type="text" id="title" name="title" required
                       value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="body">Текст поста</label>
                <textarea id="body" name="body" required><?= htmlspecialchars($_POST['body'] ?? '') ?></textarea>
            </div>
            <button type="submit">Опубликовать</button>
        </form>
    </div>
</main>
<?php include __DIR__ . '/partials/foot.php'; ?>
