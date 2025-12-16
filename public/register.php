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
        <title>Register</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <style>

        </style>
    </head>
    <body>
        <div class="container">
            <h2> Create user account</h2>

            <form id="registerForm">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Register</button>
            </form>

            <div class="error" id="error"></div>

            <p>
                Already have an account?
                <a href="login.php">Login</a>
            </p>
        </div>

        <script>
            document.getElementById("registerForm").addEventListener('submit', async function (e) {
                e.preventDefault();

                const form = e.target;
                const formData = new FormData(form);
                const errorBox = document.getElementById("error");

                errorBox.textContent = '';

                try{
                    const response = await fetch('/friendReminder/api/register.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if(!response.ok){
                        errorBox.textContent = data.error || 'Registration failed';
                        return;
                    }

                    // Success -> redirect to dashboard
                    window.location.href = 'dashboard.php';

                }catch (err){
                    errorBox.textContent = 'Network error. Try again.';
                }                
            });
        </script>
    </body>
</html>