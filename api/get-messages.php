<?php
session_start();
require '../db.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION["user_id"];
$other_user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

if (empty($other_user_id)) {
    echo json_encode(['success' => false, 'error' => 'User ID required']);
    exit();
}

// Get latest message timestamp
$last_message_time = isset($_GET['last_time']) ? $_GET['last_time'] : null;

// Get new messages
$query = "
    SELECT m.*, 
           s.name as sender_name, s.profile_photo as sender_photo
    FROM messages m
    JOIN users s ON m.sender_id = s.id
    WHERE ((m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?))
";

$params = [$user_id, $other_user_id, $other_user_id, $user_id];
$types = "iiii";

if ($last_message_time) {
    $query .= " AND m.created_at > ?";
    $params[] = $last_message_time;
    $types .= "s";
}

$query .= " ORDER BY m.created_at ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

$stmt->close();

// Mark messages as read
if (!empty($messages)) {
    $updateStmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE receiver_id = ? AND sender_id = ? AND is_read = 0");
    $updateStmt->bind_param("ii", $user_id, $other_user_id);
    $updateStmt->execute();
    $updateStmt->close();
}

echo json_encode([
    'success' => true,
    'new_messages' => !empty($messages),
    'messages' => $messages
]);
?>

