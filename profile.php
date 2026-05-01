<?php
/**
 * User Profile Page
 * 
 * Shows user's own recipes with edit/delete options.
 * Personalized content via $_SESSION.
 */
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

// Fetch user's recipes
$stmt = $pdo->prepare("
    SELECT r.*,
           (SELECT COUNT(*) FROM likes WHERE recipe_id = r.id) AS like_count,
           (SELECT COUNT(*) FROM comments WHERE recipe_id = r.id) AS comment_count
    FROM recipes r
    WHERE r.user_id = :uid
    ORDER BY r.created_at DESC
");
$stmt->execute([':uid' => $_SESSION['user_id']]);
$recipes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Recipe Network</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="logo">🍳 Recipe Network</a>
        <div class="nav-links">
            <a href="index.php">Recipes</a>
            <a href="upload.php">Upload</a>
            <a href="profile.php" class="active"><?= htmlspecialchars($_SESSION['username']) ?></a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <main class="container">
        <div class="profile-header">
            <h1>👤 <?= htmlspecialchars($user['username']) ?></h1>
            <p>Email: <?= htmlspecialchars($user['email']) ?></p>
            <p>Member since: <?= date('M j, Y', strtotime($user['created_at'])) ?></p>
            <p>Recipes: <?= count($recipes) ?></p>
        </div>

        <h2>My Recipes</h2>

        <?php if (empty($recipes)): ?>
            <p class="empty-state">You haven't uploaded any recipes yet. <a href="upload.php">Upload one!</a></p>
        <?php else: ?>
            <div class="recipe-grid">
                <?php foreach ($recipes as $recipe): ?>
                    <article class="recipe-card">
                        <?php if ($recipe['image_path']): ?>
                            <img src="<?= htmlspecialchars($recipe['image_path']) ?>" 
                                 alt="<?= htmlspecialchars($recipe['title']) ?>" class="recipe-image">
                        <?php else: ?>
                            <div class="recipe-image-placeholder">🍽️</div>
                        <?php endif; ?>

                        <div class="recipe-card-body">
                            <h2><a href="recipe.php?id=<?= $recipe['id'] ?>"><?= htmlspecialchars($recipe['title']) ?></a></h2>
                            <div class="recipe-stats">
                                <span>❤️ <?= $recipe['like_count'] ?></span>
                                <span>💬 <?= $recipe['comment_count'] ?></span>
                            </div>
                            <div class="owner-actions">
                                <a href="edit_recipe.php?id=<?= $recipe['id'] ?>" class="btn btn-small">Edit</a>
                                <a href="delete_recipe.php?id=<?= $recipe['id'] ?>" class="btn btn-small btn-danger"
                                   onclick="return confirm('Delete this recipe?')">Delete</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
