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

// ==============================================================================
// Database Configuration
// ==============================================================================
// For Local Testing (XAMPP):
//   DB_HOST = 'localhost', DB_PORT = '3307' (or '3306'), DB_USER = 'root', DB_PASS = ''
//
// For Free Web Hosting (e.g., InfinityFree / cPanel):
//   Copy the exact MySQL Details from your InfinityFree Client Area:
//   DB_HOST = 'sqlXXX.infinityfree.com' (e.g. sql100.infinityfree.com)
//   DB_PORT = '3306'
//   DB_NAME = 'if0_XXXXXXXX_innovate_db'
//   DB_USER = 'if0_XXXXXXXX'
//   DB_PASS = 'YourVpanelPassword'
// ==============================================================================

define('DB_HOST', 'localhost');
define('DB_PORT', '3307'); // Set to '3306' or leave empty for remote hosts like InfinityFree
define('DB_NAME', 'innovate_club_db');
define('DB_USER', 'root');
define('DB_PASS', '');

function getDatabaseConnection() {
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    try {
        // Build DSN - use custom port if specified, otherwise default MySQL 3306
        $dsn = "mysql:host=" . DB_HOST;
        if (defined('DB_PORT') && !empty(DB_PORT) && DB_PORT !== '3306') {
            $dsn .= ";port=" . DB_PORT;
        }
        $dsn .= ";dbname=" . DB_NAME . ";charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_TIMEOUT            => 5,
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

            // Ensure SQLite table exists with UNIQUE constraint on email
            $pdo->exec("CREATE TABLE IF NOT EXISTS club_members (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                fullname TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                phone TEXT NOT NULL,
                course TEXT NOT NULL,
                year_of_study TEXT,
                interest TEXT,
                message TEXT,
                status TEXT DEFAULT 'pending',
                ip_address TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )");

            // Populate sample data if SQLite table is empty
            $countStmt = $pdo->query("SELECT COUNT(*) FROM club_members");
            if ($countStmt && $countStmt->fetchColumn() == 0) {
                $pdo->exec("INSERT INTO club_members (fullname, email, phone, course, year_of_study, interest, message, status) VALUES
                    ('Alex Kiprono', 'alex.kip@strathmore.edu', '+254 712 345 678', 'Computer Science', 'Year 2', 'Web Development (HTML/CSS/JS/Fullstack)', 'Looking to collaborate on open-source web applications and participate in the upcoming campus hackathon.', 'approved'),
                    ('Faith Muthoni', 'faith.m@uonbi.ac.ke', '+254 723 456 789', 'Software Engineering', 'Year 3', 'AI & Machine Learning', 'Excited about the sustainable AI hackathon and wanting to contribute as an IoT designer.', 'approved'),
                    ('Brian Ochieng', 'brian.och@jkuat.ac.ke', '+254 734 567 890', 'Information Technology', 'Year 1', 'Cybersecurity & Cloud', 'First year IT student eager to learn from senior students and attend coding bootcamps.', 'pending')
                ");
            }

            return $pdo;
        } catch (Exception $sqliteEx) {
            // Log error or die gracefully
            error_log("Database Connection Error: " . $e->getMessage());
            return null;
        }
    }
}
