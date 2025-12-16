<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';

header('Content-type: application/json');

// Auth check
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Allow only get
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$userId = (int)$_SESSION['user_id'];

$stmt = $pdo->prepare(
    "SELECT
        id,
        name,
        last_visit,
        frequency_days,
        note,
        phone,
        email
    FROM friends
    WHERE user_id = ?
    ORDER BY last_visit ASC"
);

$stmt->execute([$userId]);
$friends = $stmt->fetchAll();

echo json_encode($friends);