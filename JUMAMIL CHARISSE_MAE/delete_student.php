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
if ($id <= 0) {
    http_response_code(400);
    $response['error'] = 'Invalid id';
    echo json_encode($response);
    exit;
}

try {
    $pdo = getDb();
    $stmt = $pdo->prepare('DELETE FROM students WHERE id = ?');
    $stmt->execute([$id]);
    if ($stmt->rowCount() > 0) {
        $response['success'] = true;
    } else {
        http_response_code(404);
        $response['error'] = 'Student not found';
    }
    echo json_encode($response);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    $response['error'] = 'Database error: ' . $e->getMessage();
    echo json_encode($response);
    exit;
}
