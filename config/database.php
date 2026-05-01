<?php
/**
 * Database Connection - PDO
 * 
 * Why PDO over mysqli:
 * - Object-oriented interface
 * - Database-agnostic (can switch to PostgreSQL without rewrite)
 * - Named parameters in prepared statements (:param vs ?)
 * - Built-in exception handling
 * 
 * Security: PDO::ERRMODE_EXCEPTION ensures errors are caught,
 * not silently ignored. Prepared statements prevent SQL injection.
 */

// Use environment variables (Docker) or fallback to defaults
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'recipe_network';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: 'root';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    // Don't expose DB details in production
    die('Database connection failed. Please try again later.');
}
