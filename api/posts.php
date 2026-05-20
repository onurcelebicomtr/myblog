<?php
require_once __DIR__ . '/config.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $posts = read_json('posts.json');
    $slug = $_GET['slug'] ?? null;

    if ($slug) {
        $post = null;
        foreach ($posts as $p) {
            if ($p['slug'] === $slug && $p['status'] === 'published') {
                $post = $p;
                break;
            }
        }
        echo json_encode($post ?: ['error' => 'Yazı bulunamadı']);
    } else {
        $published_only = !isset($_GET['all']);
        if ($published_only) {
            $posts = array_values(array_filter($posts, fn($p) => $p['status'] === 'published'));
        }
        usort($posts, fn($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));
        echo json_encode($posts);
    }
    exit;
}

check_auth();

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $posts = read_json('posts.json');

    $post = [
        'id' => uniqid(),
        'title' => $input['title'] ?? '',
        'slug' => $input['slug'] ?: slug($input['title'] ?? ''),
        'content' => $input['content'] ?? '',
        'excerpt' => $input['excerpt'] ?? '',
        'featured_image' => $input['featured_image'] ?? '',
        'heading_tag' => $input['heading_tag'] ?? 'h1',
        'status' => $input['status'] ?? 'draft',
        'seo_title' => $input['seo_title'] ?? '',
        'seo_description' => $input['seo_description'] ?? '',
        'seo_keywords' => $input['seo_keywords'] ?? '',
        'category' => $input['category'] ?? '',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];

    $posts[] = $post;
    write_json('posts.json', $posts);
    echo json_encode(['success' => true, 'post' => $post]);
}

if ($method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    $posts = read_json('posts.json');
    $id = $input['id'] ?? '';

    foreach ($posts as &$post) {
        if ($post['id'] === $id) {
            $post['title'] = $input['title'] ?? $post['title'];
            $post['slug'] = $input['slug'] ?: slug($input['title'] ?? $post['title']);
            $post['content'] = $input['content'] ?? $post['content'];
            $post['excerpt'] = $input['excerpt'] ?? $post['excerpt'];
            $post['featured_image'] = $input['featured_image'] ?? $post['featured_image'];
            $post['heading_tag'] = $input['heading_tag'] ?? $post['heading_tag'];
            $post['status'] = $input['status'] ?? $post['status'];
            $post['seo_title'] = $input['seo_title'] ?? $post['seo_title'];
            $post['seo_description'] = $input['seo_description'] ?? $post['seo_description'];
            $post['seo_keywords'] = $input['seo_keywords'] ?? $post['seo_keywords'];
            $post['category'] = $input['category'] ?? $post['category'];
            $post['updated_at'] = date('Y-m-d H:i:s');
            break;
        }
    }

    write_json('posts.json', $posts);
    echo json_encode(['success' => true]);
}

if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $posts = read_json('posts.json');
    $id = $input['id'] ?? '';

    $posts = array_values(array_filter($posts, fn($p) => $p['id'] !== $id));
    write_json('posts.json', $posts);
    echo json_encode(['success' => true]);
}
