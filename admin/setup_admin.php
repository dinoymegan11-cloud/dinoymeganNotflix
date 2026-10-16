<?php
require_once '../config/database.php';

$email = 'admin@notflix.com';
$password = 'NotflixAdmin2026!';

$check = mysqli_prepare($conn, "SELECT id FROM admin_users WHERE email = ?");
mysqli_stmt_bind_param($check, "s", $email);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if (mysqli_stmt_num_rows($check) > 0) {
    exit('Admin account already exists. Delete this setup file.');
}
mysqli_stmt_close($check);

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO admin_users (email, password) VALUES (?, ?)"
);
mysqli_stmt_bind_param($stmt, "ss", $email, $hash);

if (mysqli_stmt_execute($stmt)) {
    echo 'Admin account created successfully!<br>';
    echo 'Email: admin@notflix.com<br>';
    echo 'Password: NotflixAdmin2026!<br>';
    echo 'IMPORTANT: Delete setup_admin.php now.';
} else {
    echo 'Could not create admin account.';
}

mysqli_stmt_close($stmt);
?>
