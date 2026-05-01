<?php
/**
 * Edit Recipe Page
 * Only the recipe owner can edit.
 */
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$recipeId = (int)($_GET['id'] ?? 0);

// Fetch recipe — only if owned by current user
$stmt = $pdo->prepare("SELECT * FROM recipes WHERE id = :id AND user_id = :uid");
$stmt->execute([':id' => $recipeId, ':uid' => $_SESSION['user_id']]);
$recipe = $stmt->fetch();

if (!$recipe) {
    header('Location: profile.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $ingredients = trim($_POST['ingredients'] ?? '');
    $instructions = trim($_POST['instructions'] ?? '');

    if (empty($title) || empty($ingredients) || empty($instructions)) {
        $error = 'Title, ingredients and instructions are required.';
    } else {
        $imagePath = $recipe['image_path'];

        // Handle new image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($_FILES['image']['tmp_name']);

            if (in_array($mime, $allowed)) {
                $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
                $filename = uniqid('recipe_') . '.' . $ext[$mime];
                $destination = 'uploads/' . $filename;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                    // Delete old image
                    if ($recipe['image_path'] && file_exists($recipe['image_path'])) {
                        unlink($recipe['image_path']);
                    }
                    $imagePath = $destination;
                }
            }
        }

        $stmt = $pdo->prepare("UPDATE recipes SET title = :title, description = :desc, 
                               ingredients = :ing, instructions = :inst, image_path = :img WHERE id = :id");
        $stmt->execute([
            ':title' => $title, ':desc' => $description,
            ':ing' => $ingredients, ':inst' => $instructions,
            ':img' => $imagePath, ':id' => $recipeId
        ]);
        $success = 'Recipe updated!';
        $recipe = array_merge($recipe, ['title' => $title, 'description' => $description,
                    'ingredients' => $ingredients, 'instructions' => $instructions, 'image_path' => $imagePath]);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Recipe - Recipe Network</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="logo">🍳 Recipe Network</a>
        <div class="nav-links">
            <a href="index.php">Recipes</a>
            <a href="upload.php">Upload</a>
            <a href="profile.php"><?= htmlspecialchars($_SESSION['username']) ?></a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <main class="container">
        <div class="form-card form-card-wide">
            <h1>Edit Recipe</h1>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Recipe Title</label>
                    <input type="text" id="title" name="title" required
                           value="<?= htmlspecialchars($recipe['title']) ?>">
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3"><?= htmlspecialchars($recipe['description']) ?></textarea>
                </div>
                <div class="form-group">
                    <label for="ingredients">Ingredients</label>
                    <textarea id="ingredients" name="ingredients" rows="6" required><?= htmlspecialchars($recipe['ingredients']) ?></textarea>
                </div>
                <div class="form-group">
                    <label for="instructions">Instructions</label>
                    <textarea id="instructions" name="instructions" rows="6" required><?= htmlspecialchars($recipe['instructions']) ?></textarea>
                </div>
                <div class="form-group">
                    <label for="image">New Image (optional)</label>
                    <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
                    <?php if ($recipe['image_path']): ?>
                        <small>Current: <?= htmlspecialchars(basename($recipe['image_path'])) ?></small>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary">Update Recipe</button>
                <a href="recipe.php?id=<?= $recipeId ?>" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </main>
</body>
</html>
