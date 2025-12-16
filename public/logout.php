<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';

// unset all session variables
$_SESSION = [];

// Destroy session
session_destroy();

// redirect to login
header('Location: login.php');
exit;