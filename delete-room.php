<?php

session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: user/login.php");
    exit();
}
$conn = new mysqli("localhost", "root", "", "roomfinder");
if ($conn->connect_error) die("DB error");

$id = intval($_GET['id'] ?? 0);
$user_id = $_SESSION["user_id"];

// Check ownership
$stmt = $conn->prepare("SELECT image FROM rooms WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$stmt->bind_result($image);
if ($stmt->fetch()) {
    $stmt->close();
    // Delete room
    $del = $conn->prepare("DELETE FROM rooms WHERE id=? AND user_id=?");
    $del->bind_param("ii", $id, $user_id);
    $del->execute();
    // Optionally delete image file
    if ($image && file_exists(__DIR__ . '/' . $image)) unlink(__DIR__ . '/' . $image);
    header("Location: find-rooms.php?msg=deleted");
    exit();
} else {
    die("Room not found or you do not have permission.");
}
