<?php
/**
 * Comment API Endpoint (AJAX)
 * 
 * POST comment, return JSON with comment data + username.
 * Frontend appends comment dynamically without page reload.
 */
session_start();
header('Content-Type: application/json');
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$recipeId = (int)($_POST['recipe_id'] ?? 0);
$commentText = trim($_POST['comment_text'] ?? '');
$userId = $_SESSION['user_id'];

if (!$recipeId || empty($commentText)) {
    echo json_encode(['success' => false, 'error' => 'Comment cannot be empty']);
    exit;
}

// Insert comment
$stmt = $pdo->prepare("INSERT INTO comments (user_id, recipe_id, comment_text) VALUES (:uid, :rid, :text)");
$stmt->execute([':uid' => $userId, ':rid' => $recipeId, ':text' => $commentText]);

echo json_encode([
    'success' => true,
    'comment' => [
        'username' => $_SESSION['username'],
        'comment_text' => $commentText,
        'created_at' => date('M j, Y H:i')
    ]
]);
