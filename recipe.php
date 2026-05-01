<?php
/**
 * Recipe Detail Page
 * 
 * Shows full recipe with ingredients, instructions, likes, comments.
 * Logged-in users can like and comment (via AJAX).
 * Visitors can only view.
 */
session_start();
require_once 'config/database.php';

$isLoggedIn = isset($_SESSION['user_id']);
$recipeId = (int)($_GET['id'] ?? 0);

if (!$recipeId) {
    header('Location: index.php');
    exit;
}

// Fetch recipe with author info
$stmt = $pdo->prepare("
    SELECT r.*, u.username,
           (SELECT COUNT(*) FROM likes WHERE recipe_id = r.id) AS like_count
    FROM recipes r
    JOIN users u ON r.user_id = u.id
    WHERE r.id = :id
");
$stmt->execute([':id' => $recipeId]);
$recipe = $stmt->fetch();

if (!$recipe) {
    header('Location: index.php');
    exit;
}

// Check if current user has liked this recipe
$userLiked = false;
if ($isLoggedIn) {
    $stmt = $pdo->prepare("SELECT id FROM likes WHERE user_id = :uid AND recipe_id = :rid");
    $stmt->execute([':uid' => $_SESSION['user_id'], ':rid' => $recipeId]);
    $userLiked = (bool)$stmt->fetch();
}

// Fetch comments with usernames
$stmt = $pdo->prepare("
    SELECT c.*, u.username 
    FROM comments c 
    JOIN users u ON c.user_id = u.id 
    WHERE c.recipe_id = :rid 
    ORDER BY c.created_at ASC
");
$stmt->execute([':rid' => $recipeId]);
$comments = $stmt->fetchAll();

// Check if this is the owner's recipe
$isOwner = $isLoggedIn && $_SESSION['user_id'] == $recipe['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($recipe['title']) ?> - Recipe Network</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="logo">🍳 Recipe Network</a>
        <div class="nav-links">
            <a href="index.php">Recipes</a>
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
        <article class="recipe-detail">
            <?php if ($recipe['image_path']): ?>
                <img src="<?= htmlspecialchars($recipe['image_path']) ?>" 
                     alt="<?= htmlspecialchars($recipe['title']) ?>" class="recipe-detail-image">
            <?php endif; ?>

            <h1><?= htmlspecialchars($recipe['title']) ?></h1>
            <p class="recipe-meta">
                by <strong><?= htmlspecialchars($recipe['username']) ?></strong>
                · <?= date('M j, Y', strtotime($recipe['created_at'])) ?>
            </p>

            <?php if ($isOwner): ?>
                <div class="owner-actions">
                    <a href="edit_recipe.php?id=<?= $recipe['id'] ?>" class="btn btn-small">Edit</a>
                    <a href="delete_recipe.php?id=<?= $recipe['id'] ?>" class="btn btn-small btn-danger"
                       onclick="return confirm('Delete this recipe?')">Delete</a>
                </div>
            <?php endif; ?>

            <?php if ($recipe['description']): ?>
                <div class="recipe-section">
                    <h2>Description</h2>
                    <p><?= nl2br(htmlspecialchars($recipe['description'])) ?></p>
                </div>
            <?php endif; ?>

            <div class="recipe-section">
                <h2>Ingredients</h2>
                <p><?= nl2br(htmlspecialchars($recipe['ingredients'])) ?></p>
            </div>

            <div class="recipe-section">
                <h2>Instructions</h2>
                <p><?= nl2br(htmlspecialchars($recipe['instructions'])) ?></p>
            </div>

            <!-- Like Section -->
            <div class="like-section">
                <?php if ($isLoggedIn): ?>
                    <button class="like-btn <?= $userLiked ? 'liked' : '' ?>" 
                            data-recipe-id="<?= $recipe['id'] ?>">
                        <span class="like-icon"><?= $userLiked ? '❤️' : '🤍' ?></span>
                        <span class="like-count"><?= $recipe['like_count'] ?></span>
                    </button>
                <?php else: ?>
                    <span class="like-display">❤️ <span class="like-count"><?= $recipe['like_count'] ?></span> likes</span>
                    <small><a href="login.php">Login to like</a></small>
                <?php endif; ?>
            </div>

            <!-- Comments Section -->
            <div class="comments-section">
                <h2>Comments (<span id="comment-count"><?= count($comments) ?></span>)</h2>

                <div id="comments-list">
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment">
                            <strong><?= htmlspecialchars($comment['username']) ?></strong>
                            <small><?= date('M j, Y H:i', strtotime($comment['created_at'])) ?></small>
                            <p><?= nl2br(htmlspecialchars($comment['comment_text'])) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($isLoggedIn): ?>
                    <form id="comment-form" class="comment-form">
                        <input type="hidden" name="recipe_id" value="<?= $recipe['id'] ?>">
                        <div class="form-group">
                            <textarea name="comment_text" id="comment-text" rows="3" 
                                      placeholder="Write a comment..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Post Comment</button>
                    </form>
                <?php else: ?>
                    <p class="login-prompt"><a href="login.php">Login to comment</a></p>
                <?php endif; ?>
            </div>
        </article>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="js/ajax.js"></script>
</body>
</html>
