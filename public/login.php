<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/init.php';

// if already logged in, redirect
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>

    </style>
</head>
<body>
    <div class="container">
        <h2> Login </h2>

        <form id="loginForm">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <div class="error" id="error"></div>

        <p>
            Don't have an account?
            <a href="register.php">Register</a>
        </p>

    </div>

    <script>
        document.getElementById("loginForm").addEventListener('submit', async function (e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);
            const errorBox = document.getElementById("error");

            errorBox.textContent = '';

            try {
                const response = await fetch('/friendReminder/api/login.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if(!response.ok){
                    errorBox.textContent = data.error || 'Login failed';
                    return;
                }

                // Success -> redirect na dashboard
                window.location.href = 'dashboard.php';
            } catch (err) {
                errorBox.textContent = "Network error. Try again.";
            }
        })
    </script>
</body>

</html>