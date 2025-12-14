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

$stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
    exit;
}

// login success
$_SESSION['user_id'] = (int)$user['id'];

echo json_encode(['success' => true]);
