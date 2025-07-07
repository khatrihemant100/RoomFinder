<?php
include 'db.php';
$result = $conn->query("SELECT rooms.*, users.name as owner FROM rooms JOIN users ON rooms.user_id = users.id ORDER BY created_at DESC");
$rooms = [];
while ($row = $result->fetch_assoc()) {
    $rooms[] = $row;
}
header('Content-Type: application/json');
echo json_encode($rooms);
?>