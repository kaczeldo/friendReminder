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
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$userId = (int)$_SESSION['user_id'];

// Read and save input
$name = trim($_POST['name'] ?? '');
$lastVisit = $_POST['last_visit'] ?? '';
$frequencyDays = (int)($_POST['frequency_days'] ?? '');

//optional fields
$note = trim($_POST['note'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');

// basic validation
if ($name === '' || $lastVisit === '' || $frequencyDays <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Name, last visit and frequency are required.']);
    exit;
}

// validate date format
$date = DateTime::createFromFormat('Y-m-d', $lastVisit);
if (!$date || $date->format('Y-m-d') !== $lastVisit){
    http_response_code(400);
    echo json_encode(['error' => 'Invalid date format']);
    exit;
}

// validate email
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)){
    http_response_code(400);
    echo json_encode(['error' => 'Email format is not valid.']);
    exit;
}

// Everything is good, insert friend into database
$stmt = $pdo->prepare(
    "INSERT INTO friends (
        user_id,
        name,
        last_visit,
        frequency_days,
        note,
        phone,
        email
    ) VALUES (?, ?, ?, ?, ?, ?, ?)"
);

$stmt->execute([
    $userId, 
    $name, 
    $lastVisit, 
    $frequencyDays, 
    $note ?: null,
    $phone ?: null, 
    $email ?: null
]);

echo json_encode([
    'success' => true,
    'friend_id' => (int)$pdo->lastInsertId()
]);
