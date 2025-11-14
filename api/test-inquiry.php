<?php
// Test script to check if inquiry API is working
// Access this file directly in browser to test

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Testing Inquiry API</h2>";

// Test 1: Check database connection
echo "<h3>1. Database Connection:</h3>";
$conn = new mysqli("localhost", "root", "", "roomfinder");
if ($conn->connect_error) {
    echo "❌ Failed: " . $conn->connect_error . "<br>";
} else {
    echo "✅ Connected successfully<br>";
}

// Test 2: Check if inquiries table exists
echo "<h3>2. Check Inquiries Table:</h3>";
$tableCheck = $conn->query("SHOW TABLES LIKE 'inquiries'");
if ($tableCheck->num_rows === 0) {
    echo "❌ Table 'inquiries' does not exist. Please run fix_database.sql<br>";
} else {
    echo "✅ Table 'inquiries' exists<br>";
}

// Test 3: Check if InquiryMailer class can be loaded
echo "<h3>3. Check InquiryMailer Class:</h3>";
$mailerPath = __DIR__ . '/InquiryMailer.php';
if (file_exists($mailerPath)) {
    echo "✅ InquiryMailer.php file exists<br>";
    try {
        require_once $mailerPath;
        echo "✅ InquiryMailer class loaded successfully<br>";
    } catch (Exception $e) {
        echo "❌ Error loading class: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ InquiryMailer.php file not found at: " . $mailerPath . "<br>";
}

// Test 4: Check vendor autoload
echo "<h3>4. Check Vendor Autoload:</h3>";
$vendorPath = __DIR__ . '/../15_mail/vendor/autoload.php';
if (file_exists($vendorPath)) {
    echo "✅ Vendor autoload exists<br>";
    try {
        require_once $vendorPath;
        echo "✅ Vendor autoload loaded successfully<br>";
    } catch (Exception $e) {
        echo "❌ Error loading vendor: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Vendor autoload not found at: " . $vendorPath . "<br>";
    echo "💡 Run: cd 15_mail && composer install<br>";
}

// Test 5: Check .env file
echo "<h3>5. Check .env File:</h3>";
$envPath = __DIR__ . '/../15_mail/.env';
if (file_exists($envPath)) {
    echo "✅ .env file exists<br>";
} else {
    echo "⚠️ .env file not found. Email will use default settings.<br>";
}

// Test 6: Check properties table
echo "<h3>6. Check Properties Table:</h3>";
$propsCheck = $conn->query("SHOW TABLES LIKE 'properties'");
if ($propsCheck->num_rows === 0) {
    echo "❌ Table 'properties' does not exist<br>";
} else {
    echo "✅ Table 'properties' exists<br>";
    $count = $conn->query("SELECT COUNT(*) as count FROM properties")->fetch_assoc();
    echo "📊 Total properties: " . $count['count'] . "<br>";
}

$conn->close();
?>

