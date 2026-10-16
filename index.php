<?php

session_start();
include 'config/database.php';

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
$photos = mysqli_query($conn, "SELECT movies.*, categories.name AS category_name
    FROM movies
    LEFT JOIN categories ON movies.category_id = categories.id
    ORDER BY movies.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOTFLIX | Stream Something Different</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="navbar">
    <a href="index.php" class="logo">NOTFLIX</a>

    <nav>
        <a class="active" href="index.php">Home</a>
        <a href="#trending">Trending</a>
        <a href="#discover">Explore</a>
        <a href="<?= isset($_SESSION['user_id']) ? 
        'my_list.php':'login.php' ?>">My List</a>
    </nav>

    <div class="nav-right">
    <?php if (isset($_SESSION['user_id'])): ?>
        <span class="login-link">
            Hi, <?= htmlspecialchars($_SESSION['full_name']) ?>
        </span>
        <a href="logout.php" class="signup-btn">Logout</a>
    <?php else: ?>
        <a href="login.php" class="login-link">Log in</a>
        <a href="register.php" class="signup-btn">Join Now</a>
    <?php endif; ?>
</div>

</header>

<section class="hero">
    <div class="hero-content">
        <p class="eyebrow">NOTFLIX ORIGINAL</p>
        <h1>YOUR NEXT<br>OBSESSION.</h1>
        <p class="hero-description">
            Discover extraordinary worlds, iconic moments,
            and collections you'll want to come back to.
        </p>

        <div class="hero-buttons">
            <a href="#discover" class="btn btn-light">
                &#9654; Explore Now
            </a>
            <a href="#trending" class="btn btn-dark">
                More Info
            </a>
        </div>

        <p class="hero-note">A DIFFERENT KIND OF STREAMING EXPERIENCE</p>
    </div>
    <div class="hero-shade"></div>
</section>

<section class="content-section" id="trending">
    <div class="section-heading">
        <div>
            <p class="section-kicker">HANDPICKED FOR YOU</p>
            <h2>Trending Now <span>›</span></h2>
        </div>
        <a href="#discover" class="see-all">Explore All</a>
    </div>

    <div class="content-row">
        <?php
        $trending = mysqli_query($conn, "SELECT * FROM movies ORDER BY id DESC LIMIT 6");
        if ($trending && mysqli_num_rows($trending) > 0):
            while ($item = mysqli_fetch_assoc($trending)):
        ?>
            <a class="content-card" href="movie.php?id=<?= (int)$item['id'] ?>">
                <img src="assets/images/<?php echo htmlspecialchars($item['image']); ?>"
                     alt="<?php echo htmlspecialchars($item['title']); ?>">
                <div class="card-info">
                    <span class="card-label">NOTFLIX PICK</span>
                    <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                    <p><?php echo htmlspecialchars($item['description'] ?? 'Discover this collection'); ?></p>
                </div>
            </a>
        <?php
            endwhile;
        else:
            for ($i = 1; $i <= 5; $i++):
        ?>
            <div class="content-card placeholder-card">
                <div class="placeholder-art art-<?php echo $i; ?>">
                    <span>NOTFLIX</span>
                </div>
                <div class="card-info">
                    <span class="card-label">COMING SOON</span>
                    <h3><?php echo ['The Unknown', 'Beyond Limits', 'Hidden Worlds', 'After Hours', 'New Horizons'][$i - 1]; ?></h3>
                    <p>Your next favorite awaits.</p>
                </div>
            </div>
        <?php
            endfor;
        endif;
        ?>
    </div>
</section>

<section class="content-section" id="discover">
    <div class="section-heading">
        <div>
            <p class="section-kicker">FIND YOUR NEXT FAVORITE</p>
            <h2>Explore Collections <span>›</span></h2>
        </div>
    </div>

    <div class="category-row">
        <?php
        if ($categories && mysqli_num_rows($categories) > 0):
            while ($cat = mysqli_fetch_assoc($categories)):
        ?>
            <a class="category-card" href="category.php?id=<?= (int)$cat['id'] ?>">
                <span>✦</span>
                <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                <p>Discover something new</p>
            </a>
        <?php
            endwhile;
        else:
        ?>
            <a class="category-card" href="register.php">
                <span>✦</span><h3>Editor's Picks</h3><p>Selected for you</p>
            </a>
            <a class="category-card" href="register.php">
                <span>◈</span><h3>New Releases</h3><p>Fresh discoveries</p>
            </a>
            <a class="category-card" href="register.php">
                <span>✧</span><h3>Popular Now</h3><p>Worth your attention</p>
            </a>
            <a class="category-card" href="register.php">
                <span>◇</span><h3>Hidden Gems</h3><p>Unexpected favorites</p>
            </a>
        <?php endif; ?>
    </div>
</section>

<section class="join-banner">
    <div>
        <p class="section-kicker">YOUR ENTERTAINMENT STARTS HERE</p>
        <h2>There's always something new.</h2>
        <p>Create your account and build your own collection.</p>
    </div>
    <a href="register.php" class="btn btn-red">Get Started</a>
</section>

<footer>
    <a href="index.php" class="logo">NOTFLIX</a>
    <p>A world of discovery, all in one place.</p>
    <p class="copyright">© 2026 NOTFLIX. All rights reserved.</p>
</footer>

</body>
</html>