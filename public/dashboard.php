<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../config/auth.php';

// get user id
$userId = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <div class="container">
        <h2> Dashboard </h2>
        <a href="logout.php">Logout</a>
    </div>

    <p>
        You are logged in as user:
        <strong><?= htmlspecialchars((string)$userId) ?></strong>
    </p>

    <div>
        coming next
    </div>
</body>
</html>