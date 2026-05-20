<?php
require_once __DIR__ . '/config.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    echo json_encode(read_json('settings.json'));
    exit;
}

check_auth();

if ($method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    $settings = read_json('settings.json');

    foreach ($input as $key => $value) {
        $settings[$key] = $value;
    }

    write_json('settings.json', $settings);
    echo json_encode(['success' => true, 'settings' => $settings]);
}
