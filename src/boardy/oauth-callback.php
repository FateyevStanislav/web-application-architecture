<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => true,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

$current_code = $_GET['code'] ?? '';
if (empty($current_code)) {
    die('Нет кода авторизации. <a href="/oauth-github.php">Попробовать снова</a>');
}

// Защита от двойного использования кода
if (!empty($_SESSION['used_oauth_code']) && $_SESSION['used_oauth_code'] === $current_code) {
    if (!empty($_SESSION['user_id'])) {
        header('Location: /messages.php');
        exit;
    }
    die('Код уже использован. <a href="/oauth-github.php">Войти снова</a>');
}
$_SESSION['used_oauth_code'] = $current_code;

$client_id     = 'Ov23li9BaDX0cnrdgPuy';
$client_secret = '3981569a9b7f777946330aeeca946520577622d8';

// Шаг 1: получить access_token
$ch = curl_init('https://github.com/login/oauth/access_token');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => http_build_query([
        'client_id'     => $client_id,
        'client_secret' => $client_secret,
        'code'          => $current_code,
    ]),
    CURLOPT_HTTPHEADER     => ['Accept: application/json'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 10,
]);
$raw = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($err) die('cURL ошибка: ' . $err);
$tokenResponse = json_decode($raw, true);
$access_token  = $tokenResponse['access_token'] ?? null;
if (!$access_token) die('Ошибка токена: ' . $raw);

// Шаг 2: получить профиль
$ch = curl_init('https://api.github.com/user');
curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER     => [
        "Authorization: Bearer $access_token",
        'User-Agent: Boardy',
        'Accept: application/json',
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 10,
]);
$raw2    = curl_exec($ch);
$err2    = curl_error($ch);
curl_close($ch);

if ($err2) die('cURL профиль: ' . $err2);
$profile     = json_decode($raw2, true);
$github_id   = (string) ($profile['id']    ?? '');
$github_name = (string) ($profile['login'] ?? '');
if (empty($github_id)) die('Пустой профиль: ' . $raw2);

// Шаг 3: найти или создать пользователя
require_once __DIR__ . '/db.php';

$stmt = $pdo->prepare('SELECT id, name FROM users WHERE github_id = ?');
$stmt->execute([$github_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $stmt = $pdo->prepare('INSERT INTO users (name, github_id) VALUES (?, ?)');
    $stmt->execute([$github_name, $github_id]);
    $user = ['id' => $pdo->lastInsertId(), 'name' => $github_name];
}

$_SESSION['user_id']       = $user['id'];
$_SESSION['user_name']     = $user['name'];
$_SESSION['oauth_success'] = true;

header('Location: /messages.php');
exit;
