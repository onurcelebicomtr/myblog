<?php
require_once __DIR__ . '/config.php';
check_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Sadece POST']);
    exit;
}

if (!isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Dosya bulunamadı']);
    exit;
}

$file = $_FILES['file'];
$allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

if (!in_array($file['type'], $allowed)) {
    http_response_code(400);
    echo json_encode(['error' => 'Sadece resim dosyaları (JPG, PNG, WebP, GIF)']);
    exit;
}

if ($file['size'] > 5 * 1024 * 1024) {
    http_response_code(400);
    echo json_encode(['error' => 'Dosya 5MB dan büyük olamaz']);
    exit;
}

if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$name = uniqid() . '.' . $ext;
$path = UPLOAD_DIR . $name;

if (move_uploaded_file($file['tmp_name'], $path)) {
    echo json_encode(['success' => true, 'url' => 'uploads/' . $name]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Yükleme başarısız']);
}
