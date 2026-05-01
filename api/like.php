<?php
/**
 * Like API Endpoint (AJAX)
 * 
 * Toggle like: if already liked → unlike (DELETE), else → like (INSERT).
 * Returns JSON: {"success": true, "liked": bool, "count": int}
 * UNIQUE constraint prevents duplicate likes at DB level.
 */
session_start();
header('Content-Type: application/json');
require_once '../config/database.php';

// Only logged-in users can like
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$recipeId = (int)($_POST['recipe_id'] ?? 0);
$userId = $_SESSION['user_id'];

if (!$recipeId) {
    echo json_encode(['success' => false, 'error' => 'Invalid recipe']);
    exit;
}

// Check if user already liked this recipe
$stmt = $pdo->prepare("SELECT id FROM likes WHERE user_id = :uid AND recipe_id = :rid");
$stmt->execute([':uid' => $userId, ':rid' => $recipeId]);

if ($stmt->fetch()) {
    // Already liked → unlike (DELETE)
    $stmt = $pdo->prepare("DELETE FROM likes WHERE user_id = :uid AND recipe_id = :rid");
    $stmt->execute([':uid' => $userId, ':rid' => $recipeId]);
    $liked = false;
} else {
    // Not liked → like (INSERT)
    $stmt = $pdo->prepare("INSERT INTO likes (user_id, recipe_id) VALUES (:uid, :rid)");
    $stmt->execute([':uid' => $userId, ':rid' => $recipeId]);
    $liked = true;
}

// Get updated count
$stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM likes WHERE recipe_id = :rid");
$stmt->execute([':rid' => $recipeId]);
$count = $stmt->fetch()['count'];

echo json_encode(['success' => true, 'liked' => $liked, 'count' => (int)$count]);
