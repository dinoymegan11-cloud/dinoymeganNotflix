<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$photosResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM movies");
$usersResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users");
$categoriesResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM categories");

$totalPhotos = mysqli_fetch_assoc($photosResult)['total'];
$totalUsers = mysqli_fetch_assoc($usersResult)['total'];
$totalCategories = mysqli_fetch_assoc($categoriesResult)['total'];
?><!DOCTYPE html><html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOTFLIX Admin Dashboard</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 35px;
            background: #111;
            color: white;
            font-family: Arial, sans-serif;
        }
        .container { max-width: 1000px; margin: auto; }
        .logo { color: #e50914; font-size: 30px; font-weight: bold; }
        .cards { display: flex; gap: 20px; flex-wrap: wrap; margin: 30px 0; }
        .card { flex: 1; min-width: 180px; padding: 25px; background: #222; border-radius: 10px; }
        .card h2 { color: #e50914; font-size: 32px; }
        a {
            display: inline-block;
            padding: 13px 18px;
            margin: 5px;
            border-radius: 5px;
            background: #e50914;
            color: white;
            text-decoration: none;
        }
        .logout { background: #333; }
    </style>
</head>
<body>
<div class="container">
    <div class="logo">NOTFLIX <small style="color:white;font-size:14px">ADMIN</small></div>
    <h1>Dashboard</h1>
    <p>Welcome, Administrator!</p><div class="cards">
    <div class="card"><h2><?= $totalPhotos ?></h2><p>Total Movies / Photos</p></div>
    <div class="card"><h2><?= $totalCategories ?></h2><p>Total Categories</p></div>
    <div class="card"><h2><?= $totalUsers ?></h2><p>Registered Users</p></div>
</div>

<a href="add_movie.php">+ Add Movie / Photo</a>
<a href="../index.php">View Website</a>
<a href="logout.php" class="logout">Admin Logout</a>

</div>
</body>
</html>