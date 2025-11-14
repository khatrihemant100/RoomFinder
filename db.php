<?php
// Database configuration
// TODO: Move to config file or environment variables for production
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "roomfinder";

// Enable exception mode for mysqli (to catch SQL errors as exceptions)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Create connection
try {
    $conn = new mysqli($host, $user, $pass, $dbname);
    
    // Set charset to utf8mb4 for proper Unicode support
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Connection failed: " . $e->getMessage());
}
?>