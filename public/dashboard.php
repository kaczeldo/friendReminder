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
        <div class="top-bar">
            <h2>Friends</h2>
            <a href="logout.php">Logout</a>
        </div>

        <div id="friends"></div>
    </div>
    <script>
        async function loadFriends() {
            const container = document.getElementById('friends');
            container.innerHTML = 'Loading...';

            try {
                // MOST IMPORTANT ROW BELOW
                const response = await fetch('/friendReminder/api/get_friends.php');
                const friends = await response.json();

                if (!response.ok){
                    container.innerHTML = "Failed to load friends.";
                    return;
                }

                if (friends.length === 0){
                    container.innerHTML = "There are no friends yet.";
                    return;
                }

                container.innerHTML = "";
                friends.forEach(friend => {
                    const dayPassed = friend.frequency_days - friend.days_remaining;
                    const progressPercent = Math.min(
                        100,
                        Math.max(0, (dayPassed / friend.frequency_days) * 100)
                    );

                    const card = document.createElement('div');
                    card.className =     `friend-card ${friend.status}`;

                    card.innerHTML = `
                        <div class="friend-name">${friend.name}</div>
                        <div>
                            ${
                                friend.days_remaining >= 0
                                ? `${friend.days_remaining} days remaining`
                                : `${Math.abs(friend.days_remaining)} days overdue`
                            }
                        </div>

                        <div class="progress-bar">
                            <div class="progress" style="width: ${progressPercent}%"></div>
                        </div>
                        `;
                    
                    container.appendChild(card);

                });
            } catch(err) {
                container.innerHTML = "Network error.";
            }
        }

        loadFriends();
    </script>

</body>
</html>