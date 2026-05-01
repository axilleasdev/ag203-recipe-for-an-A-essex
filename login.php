<?php
/**
 * Login Page - Authenticate user
 * 
 * Security: password_verify() compares input with stored hash
 * Session: $_SESSION['user_id'] tracks logged-in user
 */
session_start();
require_once 'config/database.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } else {
        // Find user by email using prepared statement
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        // Verify password against stored hash
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Recipe Network</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="logo">🍳 Recipe Network</a>
        <div class="nav-links">
            <a href="index.php">Recipes</a>
            <a href="login.php" class="active">Login</a>
            <a href="register.php">Register</a>
        </div>
    </nav>

    <main class="container">
        <div class="form-card">
            <h1>Login</h1>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form id="login-form" method="POST" action="login.php" novalidate>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required
                           value="<?= htmlspecialchars($email ?? '') ?>">
                    <span class="error-msg" id="email-error"></span>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <span class="error-msg" id="password-error"></span>
                </div>

                <button type="submit" class="btn btn-primary">Login</button>
            </form>

            <p class="form-footer">Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="js/validation.js"></script>
</body>
</html>
