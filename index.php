<?php
/**
 * Home Page - Recipe Feed
 * 
 * Shows all recipes with like/comment counts.
 * Personalized nav: logged-in users see Upload/Profile/Logout,
 * visitors see Login/Register.
 */
session_start();
require_once 'config/database.php';

$isLoggedIn = isset($_SESSION['user_id']);

// Fetch all recipes with author name, like count, comment count
$stmt = $pdo->query("
    SELECT r.*, u.username,
           (SELECT COUNT(*) FROM likes WHERE recipe_id = r.id) AS like_count,
           (SELECT COUNT(*) FROM comments WHERE recipe_id = r.id) AS comment_count
    FROM recipes r
    JOIN users u ON r.user_id = u.id
    ORDER BY r.created_at DESC
");
$recipes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Network</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="logo">🍳 Recipe Network</a>
        <div class="nav-links">
            <a href="index.php" class="active">Recipes</a>
            <?php if ($isLoggedIn): ?>
                <a href="upload.php">Upload</a>
                <a href="profile.php"><?= htmlspecialchars($_SESSION['username']) ?></a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="container">
        <h1>All Recipes</h1>

        <?php if (empty($recipes)): ?>
            <p class="empty-state">No recipes yet. <?= $isLoggedIn ? '<a href="upload.php">Upload the first one!</a>' : 'Login to upload one!' ?></p>
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
                            <p class="recipe-meta">
                                by <strong><?= htmlspecialchars($recipe['username']) ?></strong>
                                · <?= date('M j, Y', strtotime($recipe['created_at'])) ?>
                            </p>
                            <?php if ($recipe['description']): ?>
                                <p class="recipe-desc"><?= htmlspecialchars(mb_substr($recipe['description'], 0, 120)) ?>...</p>
                            <?php endif; ?>
                            <div class="recipe-stats">
                                <span>❤️ <?= $recipe['like_count'] ?></span>
                                <span>💬 <?= $recipe['comment_count'] ?></span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</body>
</html>
