<?php
define('DATA_DIR', __DIR__ . '/../data/');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'bankobet2025');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

function read_json($file) {
    $path = DATA_DIR . $file;
    if (!file_exists($path)) return [];
    return json_decode(file_get_contents($path), true) ?: [];
}

function write_json($file, $data) {
    $path = DATA_DIR . $file;
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function check_auth() {
    $headers = getallheaders();
    $auth = $headers['Authorization'] ?? '';
    if (!$auth) {
        http_response_code(401);
        echo json_encode(['error' => 'Yetkisiz erişim']);
        exit;
    }
    $token = str_replace('Bearer ', '', $auth);
    $expected = base64_encode(ADMIN_USER . ':' . ADMIN_PASS);
    if ($token !== $expected) {
        http_response_code(401);
        echo json_encode(['error' => 'Geçersiz kimlik']);
        exit;
    }
}

function slug($text) {
    $tr = ['ç'=>'c','ğ'=>'g','ı'=>'i','ö'=>'o','ş'=>'s','ü'=>'u','Ç'=>'c','Ğ'=>'g','İ'=>'i','Ö'=>'o','Ş'=>'s','Ü'=>'u'];
    $text = strtr($text, $tr);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}
