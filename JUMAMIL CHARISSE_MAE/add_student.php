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

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');

if ($name === '' || $email === '') {
    http_response_code(400);
    $response['error'] = 'Name and email are required.';
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
    $stmt = $pdo->prepare('INSERT INTO students (name, email) VALUES (?, ?)');
    $stmt->execute([$name, $email]);
    $id = $pdo->lastInsertId();
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
