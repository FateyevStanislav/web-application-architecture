<?php
session_start();
session_destroy();

setcookie('PHPSESSID', '', [
    'expires' => time() - 3600,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);

header('Location: /messages.php');
exit;
