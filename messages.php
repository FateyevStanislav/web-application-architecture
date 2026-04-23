<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

require_once __DIR__ . '/db.php';

$stmt = $pdo->query('
    SELECT p.id, p.body, p.created_at,
           u.name AS author_name
    FROM posts p
    JOIN users u ON p.author_id = u.id
    ORDER BY p.created_at DESC
');
$posts = $stmt->fetchAll();
?>
<?php include __DIR__ . '/partials/head.php'; ?>
<?php include __DIR__ . '/partials/nav.php'; ?>
<main>
    <h1>Все посты</h1>
    <?php if (empty($posts)): ?>
        <div class="card">
            <p>Пока нет ни одного поста</p>
        </div>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="card post">
                <div class="post-body">
                    <?= htmlspecialchars($post['body']) ?>
                </div>
                <div class="post-meta">
                    Автор: <?= htmlspecialchars($post['author_name']) ?> | 
                    <?= date('d.m.Y H:i', strtotime($post['created_at'])) ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>
<?php include __DIR__ . '/partials/foot.php'; ?>
