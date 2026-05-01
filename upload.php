<?php
/**
 * Upload Recipe Page
 * 
 * Only accessible to logged-in users.
 * Image upload: MIME validation, extension whitelist, unique filename.
 */
session_start();
require_once 'config/database.php';

// Redirect visitors to login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $ingredients = trim($_POST['ingredients'] ?? '');
    $instructions = trim($_POST['instructions'] ?? '');
    $imagePath = null;

    if (empty($title) || empty($ingredients) || empty($instructions)) {
        $error = 'Title, ingredients and instructions are required.';
    } else {
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($_FILES['image']['tmp_name']);

            if (!in_array($mime, $allowed)) {
                $error = 'Invalid image type. Allowed: JPEG, PNG, GIF, WebP.';
            } else {
                $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
                $filename = uniqid('recipe_') . '.' . $ext[$mime];
                $destination = 'uploads/' . $filename;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                    $imagePath = $destination;
                } else {
                    $error = 'Failed to upload image.';
                }
            }
        }

        if (empty($error)) {
            $stmt = $pdo->prepare("INSERT INTO recipes (user_id, title, description, ingredients, instructions, image_path) 
                                   VALUES (:user_id, :title, :description, :ingredients, :instructions, :image_path)");
            $stmt->execute([
                ':user_id' => $_SESSION['user_id'],
                ':title' => $title,
                ':description' => $description,
                ':ingredients' => $ingredients,
                ':instructions' => $instructions,
                ':image_path' => $imagePath
            ]);
            $success = 'Recipe uploaded successfully!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Recipe - Recipe Network</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="logo">🍳 Recipe Network</a>
        <div class="nav-links">
            <a href="index.php">Recipes</a>
            <a href="upload.php" class="active">Upload</a>
            <a href="profile.php"><?= htmlspecialchars($_SESSION['username']) ?></a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <main class="container">
        <div class="form-card form-card-wide">
            <h1>Upload Recipe</h1>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form id="upload-form" method="POST" action="upload.php" enctype="multipart/form-data" novalidate>
                <div class="form-group">
                    <label for="title">Recipe Title</label>
                    <input type="text" id="title" name="title" required
                           value="<?= htmlspecialchars($title ?? '') ?>">
                    <span class="error-msg" id="title-error"></span>
                </div>

                <div class="form-group">
                    <label for="description">Description (optional)</label>
                    <textarea id="description" name="description" rows="3"><?= htmlspecialchars($description ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="ingredients">Ingredients (one per line)</label>
                    <textarea id="ingredients" name="ingredients" rows="6" required><?= htmlspecialchars($ingredients ?? '') ?></textarea>
                    <span class="error-msg" id="ingredients-error"></span>
                </div>

                <div class="form-group">
                    <label for="instructions">Instructions</label>
                    <textarea id="instructions" name="instructions" rows="6" required><?= htmlspecialchars($instructions ?? '') ?></textarea>
                    <span class="error-msg" id="instructions-error"></span>
                </div>

                <div class="form-group">
                    <label for="image">Recipe Image (optional)</label>
                    <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
                    <span class="error-msg" id="image-error"></span>
                </div>

                <button type="submit" class="btn btn-primary">Upload Recipe</button>
            </form>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="js/validation.js"></script>
</body>
</html>
