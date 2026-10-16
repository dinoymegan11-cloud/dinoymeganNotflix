<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);

    if ($title === '' || $categoryId <= 0 || !isset($_FILES['image'])) {
        $error = 'Please complete all required fields.';
    } elseif ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please choose an image to upload.';
    } else {
        $tmpPath = $_FILES['image']['tmp_name'];
        $imageInfo = getimagesize($tmpPath);
        $imageType = $imageInfo['mime'] ?? '';

        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];

        if (!$imageInfo || !isset($allowedTypes[$imageType])) {
            $error = 'Use a JPG, PNG, or WEBP image.';
        } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            $error = 'Image must be 5 MB or smaller.';
        } else {
            $check = mysqli_prepare($conn, "SELECT id FROM categories WHERE id = ?");
            mysqli_stmt_bind_param($check, "i", $categoryId);
            mysqli_stmt_execute($check);
            mysqli_stmt_store_result($check);
            $categoryExists = mysqli_stmt_num_rows($check) === 1;
            mysqli_stmt_close($check);

            if (!$categoryExists) {
                $error = 'Please select a valid category.';
            } else {
                $uploadDir = __DIR__ . '/../assets/images/uploads/';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $filename = bin2hex(random_bytes(16)) . '.' . $allowedTypes[$imageType];
                $destination = $uploadDir . $filename;

                if (move_uploaded_file($tmpPath, $destination)) {
                    $imagePath = 'assets/images/uploads/' . $filename;

                    $stmt = mysqli_prepare(
                        $conn,
                        "INSERT INTO movies (title, description, image, category_id)
                         VALUES (?, ?, ?, ?)"
                    );

                    mysqli_stmt_bind_param(
                        $stmt, "sssi",
                        $title, $description, $imagePath, $categoryId
                    );

                    if (mysqli_stmt_execute($stmt)) {
                        $success = 'Movie added successfully!';
                    } else {
                        unlink($destination);
                        $error = 'Could not save the movie.';
                    }

                    mysqli_stmt_close($stmt);
                } else {
                    $error = 'Image upload failed. Please try again.';
                }
            }
        }
    }
}

$categories = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name");
?><!DOCTYPE html><html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Add Movie | NOTFLIX Admin</title>
<style>
*{box-sizing:border-box}
body{margin:0;padding:30px;background:#111;color:#fff;font-family:Arial,sans-serif}
.container{max-width:650px;margin:25px auto;background:#1d1d1d;padding:30px;border-radius:12px}
h1{color:#e50914}
label{display:block;margin:18px 0 8px}
input,textarea,select{width:100%;padding:12px;border:1px solid #444;border-radius:6px;background:#292929;color:white}
textarea{min-height:110px;resize:vertical}
button,.link{display:inline-block;margin-top:20px;padding:13px 18px;border:0;border-radius:6px;background:#e50914;color:white;text-decoration:none;cursor:pointer}
.message{padding:12px;background:#292929;border-radius:6px}
.error{color:#ff7777}.success{color:#8ee6a1}
</style>
</head>
<body>
<div class="container">
    <h1>Add Movie </h1>
    <p>Upload a new item to your NOTFLIX collection.</p><?php if ($error): ?>
    <p class="message error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p class="message success"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <label for="title">Title *</label>
    <input id="title" name="title" maxlength="150" required>

    <label for="description">Description</label>
    <textarea id="description" name="description"></textarea>

    <label for="category_id">Category *</label>
    <select id="category_id" name="category_id" required>
        <option value="">Choose a category</option>
        <?php while ($category = mysqli_fetch_assoc($categories)): ?>
            <option value="<?= (int)$category['id'] ?>">
                <?= htmlspecialchars($category['name']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label for="image">Cover Image * (JPG, PNG, WEBP; max 5 MB)</label>
    <input id="image" type="file" name="image" accept=".jpg,.jpeg,.png,.webp" required>

    <button type="submit">Upload Movie</button>
</form>

<a class="link" href="index.php">Back to Dashboard</a>

</div>
</body>
</html>