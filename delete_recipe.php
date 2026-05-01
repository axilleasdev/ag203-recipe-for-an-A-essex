<?php
/**
 * Delete Recipe - Owner only
 */
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$recipeId = (int)($_GET['id'] ?? 0);

// Delete only if owned by current user
$stmt = $pdo->prepare("SELECT image_path FROM recipes WHERE id = :id AND user_id = :uid");
$stmt->execute([':id' => $recipeId, ':uid' => $_SESSION['user_id']]);
$recipe = $stmt->fetch();

if ($recipe) {
    // Delete image file
    if ($recipe['image_path'] && file_exists($recipe['image_path'])) {
        unlink($recipe['image_path']);
    }
    // CASCADE deletes likes and comments automatically
    $stmt = $pdo->prepare("DELETE FROM recipes WHERE id = :id");
    $stmt->execute([':id' => $recipeId]);
}

header('Location: profile.php');
exit;
