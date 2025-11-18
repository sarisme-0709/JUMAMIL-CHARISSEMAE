<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db.php';

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $response['error'] = 'Method not allowed';
    echo json_encode($response);
    exit;
}

$id = intval($_POST['id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');

if ($id <= 0 || $name === '' || $email === '') {
    http_response_code(400);
    $response['error'] = 'ID, name and email are required.';
    echo json_encode($response);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    $response['error'] = 'Invalid email address.';
    echo json_encode($response);
    exit;
}

try {
    $pdo = getDb();
    $stmt = $pdo->prepare('UPDATE students SET name = ?, email = ? WHERE id = ?');
    $stmt->execute([$name, $email, $id]);
    $response['success'] = true;
    $response['student'] = ['id' => $id, 'name' => $name, 'email' => $email];
    echo json_encode($response);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    $response['error'] = 'Database error: ' . $e->getMessage();
    echo json_encode($response);
    exit;
}
