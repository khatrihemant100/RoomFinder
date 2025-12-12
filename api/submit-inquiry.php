<?php
// Handle room inquiry submissions with email notification
session_start();

// Set error handling - enable for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start output buffering to catch any errors
ob_start();

header('Content-Type: application/json');

// Handle CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// $conn = new mysqli("localhost", "root", "", "roomfinder");
require_once __DIR__ . '/../db.php';
if ($conn->connect_error) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => 'Database connection failed: ' . $conn->connect_error
    ]);
    exit;
}

// Get POST data (frontend uses room_id, but we map to property_id in database)
$property_id = intval($_POST['room_id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$visit_date = $_POST['visit_date'] ?? '';
$message = trim($_POST['message'] ?? '');

// Validation
if (empty($property_id) || empty($name) || empty($email) || empty($phone) || empty($visit_date)) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'All fields are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid email address']);
    exit;
}

// Check if room exists and get owner info (with proper error handling)
$checkStmt = $conn->prepare("SELECT p.id, p.title, p.location, p.price, p.user_id, u.name as owner_name, u.email as owner_email 
                              FROM properties p 
                              LEFT JOIN users u ON p.user_id = u.id 
                              WHERE p.id = ?");
if (!$checkStmt) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database query failed: ' . $conn->error
    ]);
    exit;
}

$checkStmt->bind_param("i", $property_id);
if (!$checkStmt->execute()) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch room information: ' . $checkStmt->error
    ]);
    $checkStmt->close();
    exit;
}

$result = $checkStmt->get_result();
if ($result->num_rows === 0) {
    ob_end_clean();
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Room not found']);
    $checkStmt->close();
    exit;
}

$room = $result->fetch_assoc();
$checkStmt->close();

// Debug: Log room owner info (remove in production)
error_log("Room Owner Email: " . ($room['owner_email'] ?? 'NOT FOUND'));
error_log("Room Owner Name: " . ($room['owner_name'] ?? 'NOT FOUND'));

// Check if inquiries table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'inquiries'");
if ($tableCheck->num_rows === 0) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database table "inquiries" not found. Please run docs/fix_database.sql in phpMyAdmin.'
    ]);
    exit;
}

// Insert inquiry
$stmt = $conn->prepare("INSERT INTO inquiries (property_id, name, email, phone, visit_date, message) VALUES (?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    http_response_code(500);
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'error' => 'Database prepare failed: ' . $conn->error
    ]);
    exit;
}

$stmt->bind_param("isssss", $property_id, $name, $email, $phone, $visit_date, $message);

if ($stmt->execute()) {
    // Inquiry saved successfully - Now send email to room owner
    $emailSent = false;
    $emailError = null;
    
    // Check if owner email exists
    if (empty($room['owner_email'])) {
        error_log("Warning: Room owner email not found for property ID: " . $property_id);
        $emailError = "Room owner email not found in database";
    } else {
        // Owner email found - send email
        try {
            $mailerPath = __DIR__ . '/InquiryMailer.php';
            if (!file_exists($mailerPath)) {
                $emailError = "Email system file not found";
                error_log("Error: InquiryMailer.php not found at: " . $mailerPath);
            } else {
                require_once $mailerPath;
                
                // Verify class exists
                if (!class_exists('InquiryMailer')) {
                    $emailError = "Email class not found";
                    error_log("Error: InquiryMailer class not found after require");
                } else {
                    $mailer = new InquiryMailer();
                    
                    // Prepare room data
                    $roomData = [
                        'title' => $room['title'] ?? 'Untitled Property',
                        'location' => $room['location'] ?? 'Location not specified',
                        'price' => $room['price'] ?? 0
                    ];
                    
                    // Prepare inquiry data
                    $inquiryData = [
                        'name' => $name,
                        'email' => $email,
                        'phone' => $phone,
                        'visit_date' => $visit_date,
                        'message' => $message ? $message : 'No additional message provided'
                    ];
                    
                    // Send email to room owner
                    error_log("Attempting to send email to: " . $room['owner_email']);
                    $result = $mailer->sendInquiryEmail(
                        $room['owner_email'],
                        $room['owner_name'] ?? 'Property Owner',
                        $roomData,
                        $inquiryData
                    );
                    
                    if ($result === true) {
                        $emailSent = true;
                        error_log("Email sent successfully to: " . $room['owner_email']);
                    } else {
                        $emailError = is_string($result) ? $result : "Email sending failed";
                        error_log("Email sending failed to " . $room['owner_email'] . ": " . $result);
                    }
                }
            }
        } catch (Exception $e) {
            $emailError = $e->getMessage();
            error_log("Email Exception: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
        } catch (Error $e) {
            $emailError = "Email system error: " . $e->getMessage();
            error_log("Email Fatal Error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
        }
    }
    
    $response = [
        'success' => true,
        'message' => 'Your inquiry has been submitted successfully!'
    ];
    
    if ($emailSent) {
        $response['email_sent'] = true;
        $response['message'] .= ' The property owner has been notified via email.';
    } elseif ($emailError) {
        // Don't fail the inquiry if email fails, just log it
        $response['email_sent'] = false;
        $response['email_note'] = 'Inquiry saved, but email notification failed.';
    }
    
    // Clear any output before sending JSON
    ob_end_clean();
    echo json_encode($response);
} else {
    // Clear output buffer
    ob_end_clean();
    http_response_code(500);
    $errorMsg = 'Failed to submit inquiry. Please try again.';
    $dbError = $stmt->error ?? $conn->error ?? 'Unknown error';
    error_log("Database error: " . $dbError);
    echo json_encode([
        'success' => false,
        'error' => $errorMsg,
        'debug' => $dbError  // Remove this in production
    ]);
}

$stmt->close();
$conn->close();
exit;
?>

