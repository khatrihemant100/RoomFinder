<?php
session_start();
require '../db.php';

// Start output buffering to prevent any warnings/errors from corrupting JSON
ob_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit();
}

$sender_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $receiver_id = isset($_POST["receiver_id"]) ? (int)$_POST["receiver_id"] : 0;
    $message = isset($_POST["message"]) ? trim($_POST["message"]) : '';
    $property_id = isset($_POST["room_id"]) ? (int)$_POST["room_id"] : null; // Frontend uses room_id, map to property_id
    $subject = isset($_POST["subject"]) ? trim($_POST["subject"]) : null;
    
    if (empty($receiver_id) || empty($message)) {
        echo json_encode(['success' => false, 'error' => 'Receiver ID and message are required']);
        exit();
    }
    
    if ($sender_id == $receiver_id) {
        echo json_encode(['success' => false, 'error' => 'Cannot send message to yourself']);
        exit();
    }
    
    // Verify receiver exists
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $checkStmt->bind_param("i", $receiver_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows == 0) {
        echo json_encode(['success' => false, 'error' => 'Receiver not found']);
        $checkStmt->close();
        exit();
    }
    $checkStmt->close();
    
    // Insert message
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, room_id, subject, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $sender_id, $receiver_id, $property_id, $subject, $message);
    
    if ($stmt->execute()) {
        ob_end_clean(); // Clear any output before JSON
        echo json_encode(['success' => true, 'message_id' => $conn->insert_id]);
    } else {
        ob_end_clean(); // Clear any output before JSON
        error_log("Database error in send-message.php: " . $stmt->error);
        echo json_encode(['success' => false, 'error' => 'Failed to send message: ' . $stmt->error]);
    }
    
    $stmt->close();
} else {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>

