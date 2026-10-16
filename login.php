<?php
session_start();
require_once 'config/database.php';

$error = '';

if (isset($_GET['registered'])) {
    $success = 'Account created successfully! You can now sign in.';
} else {
    $success = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = mysqli_prepare($conn, "SELECT id, full_name, password FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) === 1) {
        mysqli_stmt_bind_result($stmt, $id, $fullName, $hashedPassword);
        mysqli_stmt_fetch($stmt);

        if (password_verify($password, $hashedPassword)) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $id;
            $_SESSION['full_name'] = $fullName;

            header('Location: index.php');
            exit;
        }
    }

    $error = 'Invalid email or password.';
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | NOTFLIX</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="navbar">
    <a href="index.php" class="logo">NOTFLIX</a>
    <nav><a href="index.php">Home</a></nav>
    <div class="nav-right">
        <a href="register.php" class="signup-btn">Join Now</a>
    </div>
</header>

<main class="auth-container">
    <div class="auth-box">
        <p class="section-kicker">WELCOME BACK</p>
        <h1>Sign In</h1>
        <p class="auth-subtitle">Continue exploring your collection.</p>

        <?php if ($success): ?>
            <p class="success-message">
                <?= htmlspecialchars($success) ?>
            </p>
        <?php endif; ?>

        <?php if ($error): ?>
            <p class="error-message">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <label for="email">Email Address</label>
            <input id="email" type="email" name="email"
                   placeholder="Enter your email" required>

            <label for="password">Password</label>
            <input id="password" type="password" name="password"
                   placeholder="Enter your password" required>

            <button class="btn btn-red auth-button" type="submit">
                Sign In
            </button>
        </form>

        <p class="auth-footer">
            New to NOTFLIX? <a href="register.php">Create an account</a>
        </p>
    </div>
</main>

</body>
</html>