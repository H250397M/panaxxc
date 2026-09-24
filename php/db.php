<?php
/**
 * ICS 2102 — Web Development Semester Mini Project
 * Database Connection & Configuration File
 * File: php/db.php
 * 
 * Instructions:
 * - Update the database credentials below when deploying to InfinityFree, 000webhost,
 *   or your local XAMPP/WAMP environment.
 * - This file creates a persistent PDO connection with error handling.
 */

// Database Configuration Constants
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'innovate_club_db');
define('DB_USER', 'root');
define('DB_PASS', '');

function getDatabaseConnection() {
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    try {
        // Attempt MySQL PDO connection
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;

    } catch (PDOException $e) {
        // Fallback: If MySQL is not running on localhost, use an SQLite database file
        // so the project can be tested immediately in environments without MySQL running!
        try {
            $sqlitePath = __DIR__ . '/club_submissions.sqlite';
            $pdo = new PDO('sqlite:' . $sqlitePath);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Ensure SQLite table exists
            $pdo->exec("CREATE TABLE IF NOT EXISTS club_members (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                fullname TEXT NOT NULL,
                email TEXT NOT NULL,
                phone TEXT NOT NULL,
                course TEXT NOT NULL,
                year_of_study TEXT,
                interest TEXT,
                message TEXT,
                status TEXT DEFAULT 'pending',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )");

            return $pdo;
        } catch (Exception $sqliteEx) {
            // Log error or die gracefully
            error_log("Database Connection Error: " . $e->getMessage());
            return null;
        }
    }
}
