<?php
// Admin Authentication Check
session_start();

if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require '../db.php';

// Verify admin status in database
$admin_id = $_SESSION['admin_id'];
$stmt = $conn->prepare("SELECT id, name, email, is_admin FROM users WHERE id = ? AND is_admin = 1");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$admin = $result->fetch_assoc();
$stmt->close();
?>

