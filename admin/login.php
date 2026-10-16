<?php
session_start();
require_once '../config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = mysqli_prepare($conn, "SELECT id, email, password FROM admin_users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id, $adminEmail, $passwordHash);

    if (mysqli_stmt_fetch($stmt) && password_verify($password, $passwordHash)) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $id;
        $_SESSION['admin_email'] = $adminEmail;
        mysqli_stmt_close($stmt);
        header('Location: index.php');
        exit;
    }

    mysqli_stmt_close($stmt);
    $error = 'Incorrect admin email or password.';
}
?><!DOCTYPE html><html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>NOTFLIX Admin Login</title>
<style>
*{box-sizing:border-box}
body{margin:0;min-height:100vh;display:grid;place-items:center;background:#101010;color:white;font-family:Arial,sans-serif}
.box{width:min(390px,90%);padding:35px;background:#1b1b1b;border-radius:12px}
.logo{text-align:center;color:#e50914;font-size:32px;font-weight:900;letter-spacing:2px}
h2{text-align:center}
p{text-align:center;color:#aaa}
label{display:block;margin:18px 0 8px}
input{width:100%;padding:13px;background:#292929;border:1px solid #444;border-radius:5px;color:white}
button{width:100%;padding:13px;margin-top:24px;background:#e50914;color:white;border:0;border-radius:5px;font-weight:bold;cursor:pointer}
.error{color:#ff7777}
a{display:block;text-align:center;color:#aaa;margin-top:20px}
</style>
</head>
<body>
<div class="box">
    <div class="logo">NOTFLIX</div>
    <h2>Admin Portal</h2>
    <p>Authorized administrators only</p><?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST">
    <label>Email</label>
    <input type="email" name="email" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <button type="submit">ADMIN SIGN IN</button>
</form>
<a href="../index.php">Back to NOTFLIX</a>

</div>
</body>
</html>
