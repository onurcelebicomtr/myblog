<?php
require_once __DIR__ . '/config.php';

$input = json_decode(file_get_contents('php://input'), true);
$user = $input['username'] ?? '';
$pass = $input['password'] ?? '';

if ($user === ADMIN_USER && $pass === ADMIN_PASS) {
    echo json_encode([
        'success' => true,
        'token' => base64_encode($user . ':' . $pass)
    ]);
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Kullanıcı adı veya şifre hatalı']);
}
