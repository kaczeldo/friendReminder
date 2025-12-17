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
    WHERE user_id = ?"
);

$stmt->execute([$userId]);
$friends = $stmt->fetchAll();

$today = new DateTime('today');

foreach ($friends as &$friend) {
    $lastVisit = new DateTime($friend['last_visit']);
    $frequencyDays = (int)$friend['frequency_days'];

    // calculate next visit
    $nextVisit = (clone $lastVisit)->modify("+{$frequencyDays} days");

    // calculate days remaining
    $interval = $today->diff($nextVisit);
    $daysRemaining = (int)$interval->format('%r%a');

    // Determine status
    if ($daysRemaining < 0) {
        $status = 'overdue';
    } elseif ($daysRemaining <= 2) {
        $status = 'urgent';
    } elseif ($daysRemaining <= 7) {
        $status = 'soon';
    } else {
        $status = 'ok';
    }

    // attach computed fields
    $friend['next_visit'] = $nextVisit->format('Y-m-d');
    $friend['days_remaining'] = $daysRemaining;
    $friend['status'] = $status;
}

// sort by urgency
$priority = [
    'overdue' => 0,
    'urgent' => 1,
    'soon' => 2,
    'ok' => 3
];

usort($friends, function ($a, $b) use ($priority) {
    if ($priority[$a['status']] === $priority[$b['status']]) {
        return $a['days_remaining'] <=> $b['days_remaining'];
    }

    return $priority[$a['status']] <=> $priority[$b['status']];
});

echo json_encode($friends);