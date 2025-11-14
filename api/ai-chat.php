<?php
// Server-side AI Chat API endpoint
// This protects the API key from being exposed in client-side code

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// API Key - In production, store this in environment variables or config file
$GEMINI_API_KEY = "AIzaSyAYoOAIrd7-WYQZzdYbsAjAatGEkKyB6oA";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$userMessage = $input['message'] ?? '';

if (empty($userMessage)) {
    http_response_code(400);
    echo json_encode(['error' => 'Message is required']);
    exit;
}

// Call Gemini API
$url = "https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=" . $GEMINI_API_KEY;
$body = [
    'contents' => [
        [
            'role' => 'user',
            'parts' => [
                ['text' => $userMessage]
            ]
        ]
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    http_response_code(500);
    echo json_encode(['error' => 'AI service error', 'details' => $response]);
    exit;
}

$data = json_decode($response, true);

if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
    echo json_encode([
        'success' => true,
        'response' => $data['candidates'][0]['content']['parts'][0]['text']
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'error' => 'Invalid response from AI service',
        'details' => $data
    ]);
}
?>

