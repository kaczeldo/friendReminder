<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';

header('Content-Type: application/json');

//Allow only post
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$email || !$password){
    http_response_code(400);
    echo json_encode(['error' => 'Email and password are required']);
    exit;
}

// Basic validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email format']);
    exit;
}

if (strlen($password) < 4) {
    http_response_code(400);
    echo json_encode(['error' => 'Password must be at least 4 characters long']);
    exit;
}

// check if email already exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);

if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['error' => 'Email already registered']);
    exit;
}

// Create user
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare(
    "INSERT INTO users (email, password_hash) VALUES (?, ?)"
);

$stmt->execute([$email, $passwordHash]);

// Auto-login after register
$_SESSION['user_id'] = (int)$pdo->lastInsertId();

echo json_encode(['success' => true]);