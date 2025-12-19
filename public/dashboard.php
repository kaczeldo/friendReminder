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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Friends Dashboard</title>


    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <!-- Custom CSS -->
    <link href="/friendReminder/assets/css/style.css" rel="stylesheet">

</head>

<body class="d-flex justify-content-center">
    <div class="myWrapper d-flex justify-content-center flex-column align-items-center">
        <div class="top-bar d-flex flex-column align-items-center">
            <h2>My Friends</h2>
            <a href="logout.php">Logout</a>
            <button class="btn btn-success" id="addFriendBtn">Add Friend</button>
        </div>

        <div id="friends" class="d-flex flex-column justify-content-center">
        </div>
    </div>

    <!-- modal part -->
    <div class="modal fade" id="friendModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">

                <div class="modal-header">
                    <h5 class="modal-title" id="friendModalTitle">Add Friend</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-miss="moda"></button>
                </div>
                <div class="modal-body">
                    <form id="friendForm">
                        <input type="hidden" id="friendId">

                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Last Visit</label>
                            <input type="date" class="form-control" id="lastVisit" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Frequency (Days)</label>
                            <input type="number" class="form-control" min="1" id="frequency" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Note</label>
                            <textarea class="form-control" id="note"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="email">
                        </div>
                    </form>
                </div>


                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" id="saveFriendBtn">Save</button>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS (bundle includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>


    <script>
        async function loadFriends() {
            const container = document.getElementById('friends');
            container.innerHTML = 'Loading...';

            try {
                // MOST IMPORTANT ROW BELOW
                const response = await fetch('/friendReminder/api/get_friends.php');
                const friends = await response.json();

                if (!response.ok) {
                    container.innerHTML = "Failed to load friends.";
                    return;
                }

                if (friends.length === 0) {
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
                    card.className = `friend-card ${friend.status} d-flex flex-column align-items-center`;

                    card.innerHTML = `
                        <div class="card-top d-flex justify-content-between align-items-center">
                            <div class="friend-name">${friend.name}</div>
                            <div>
                                ${friend.days_remaining >= 0
                            ? `${friend.days_remaining} days remaining`
                            : `${Math.abs(friend.days_remaining)} days overdue`
                        }
                            </div>
                            <div>
                                <a href="#">T</a>
                            </div>
                        </div>

                        <div class="progress-bar">
                            <div class="progress" style="width: ${progressPercent}%"></div>
                        </div>
                        `;

                    container.appendChild(card);

                });
            } catch (err) {
                container.innerHTML = "Network error.";
            }
        }

        loadFriends();

        // modal part
        const friendModal = new bootstrap.Modal(document.getElementById('friendModal'));

        document.getElementById('addFriendBtn').addEventListener('click', () => {
            document.getElementById('friendModalTitle').textContent = 'Add Friend';
            document.getElementById('friendForm').reset();
            document.getElementById('friendId').value = '';
            friendModal.show();
        });

        document.getElementById('saveFriendBtn').addEventListener('click', async () => {
            const id = document.getElementById('friendId').value;

            const data = new FormData();
            data.append('name', document.getElementById('name').value);
            data.append('last_visit', document.getElementById('lastVisit').value);
            data.append('frequency_days', document.getElementById('frequency').value);
            data.append('note', document.getElementById('note').value);
            data.append('phone', document.getElementById('phone').value);
            data.append('email', document.getElementById('email').value);

            const url = id
                ? '/friendReminder/api/update_friend.php'
                : '/friendReminder/api/add_friend.php';

            if (id) data.append('id', id);

            const response = await fetch(url, {
                method: 'POST',
                body: data
            });

            const result = await response.json();

            if (!response.ok) {
                alert(result.error || 'Failed');
            }

            friendModal.hide();
            loadFriends();
        });
    </script>

</body>

</html>