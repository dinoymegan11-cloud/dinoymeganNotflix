<?php
session_start();
require_once 'config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || strlen($password) < 8) {
        $error = 'Complete all fields. Password must be at least 8 characters.';
    } else {
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error = 'Email already registered. Please log in.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = mysqli_prepare($conn,
                "INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashedPassword);

            if (mysqli_stmt_execute($stmt)) {
                header('Location: login.php?registered=1');
                exit;
            } else {
                $error = 'Registration failed. Please try again.';
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_stmt_close($check);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join NOTFLIX</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="navbar">
    <a href="index.php" class="logo">NOTFLIX</a>
    <nav>
        <a href="index.php">Home</a>
        <a href="index.php#trending">Trending</a>
        <a href="index.php#discover">Explore</a>
    </nav>
    <div class="nav-right">
        <a href="login.php" class="login-link">Sign In</a>
    </div>
</header>

<main class="auth-container">
    <div class="auth-box">
        <p class="section-kicker">YOUR NEXT OBSESSION STARTS HERE</p>
        <h1>Create Account</h1>
        <p class="auth-subtitle">Join NOTFLIX and discover something different.</p>

        <?php if ($error !== ''): ?>
            <p class="error-message"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <label for="full_name">Full Name</label>
            <input id="full_name" type="text" name="full_name"
                   placeholder="Enter your name" maxlength="100" required>

            <label for="email">Email Address</label>
            <input id="email" type="email" name="email"
                   placeholder="Enter your email" maxlength="100" required>

            <label for="password">Password</label>
            <input id="password" type="password" name="password"
                   placeholder="At least 8 characters" minlength="8" required>

            <button class="btn btn-red auth-button" type="submit">
                Create Account
            </button>
        </form>

        <p class="auth-footer">
            Already have an account? <a href="login.php">Sign in</a>
        </p>
    </div>
</main>

</body>
</html>