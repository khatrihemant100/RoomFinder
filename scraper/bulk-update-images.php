<?php
/**
 * Bulk Update Room Images
 * This script updates all room images that use placeholder URLs
 * Options:
 * 1. Update all to a single default image URL
 * 2. Update all to a local default image file
 */

require_once __DIR__ . '/../db.php';

// ============================================
// Configuration
// ============================================
$updateMethod = 'url'; // 'url' or 'local'

// Option 1: Use a single image URL for all rooms
$defaultImageUrl = 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&h=600&fit=crop';

// Option 2: Use a local image file (copy to uploads folder first)
$defaultLocalImage = 'uploads/default-room.jpg';

// ============================================
// Get all properties with placeholder images
// ============================================
echo "🔍 Finding rooms with placeholder images...\n\n";

// Find properties with placeholder URLs
$query = "SELECT id, title, image_url FROM properties 
          WHERE image_url LIKE '%placeholder%' 
             OR image_url LIKE '%via.placeholder%'
             OR image_url = ''
          ORDER BY id";

$result = $conn->query($query);

if (!$result) {
    die("❌ Error: " . $conn->error . "\n");
}

$properties = [];
while ($row = $result->fetch_assoc()) {
    $properties[] = $row;
}

$totalCount = count($properties);

if ($totalCount == 0) {
    echo "✅ No rooms found with placeholder images.\n";
    exit;
}

echo "📊 Found $totalCount rooms to update\n\n";

// ============================================
// Preview what will be updated
// ============================================
echo "Preview (first 5):\n";
echo str_repeat("=", 60) . "\n";
foreach (array_slice($properties, 0, 5) as $prop) {
    echo "ID: {$prop['id']} | {$prop['title']}\n";
    echo "  Current: {$prop['image_url']}\n";
    echo "  New: " . ($updateMethod == 'url' ? $defaultImageUrl : $defaultLocalImage) . "\n\n";
}
if ($totalCount > 5) {
    echo "... and " . ($totalCount - 5) . " more\n\n";
}
echo str_repeat("=", 60) . "\n\n";

// ============================================
// Confirm update
// ============================================
if (php_sapi_name() === 'cli') {
    // Command line mode
    echo "⚠️  Ready to update $totalCount rooms.\n";
    echo "Press Enter to continue, or Ctrl+C to cancel...\n";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);
} else {
    // Web mode - show confirmation
    if (!isset($_GET['confirm']) || $_GET['confirm'] != 'yes') {
        echo "<h2>Bulk Update Room Images</h2>";
        echo "<p>Found <strong>$totalCount</strong> rooms to update.</p>";
        echo "<p>New image: <strong>" . ($updateMethod == 'url' ? $defaultImageUrl : $defaultLocalImage) . "</strong></p>";
        echo "<p><a href='?confirm=yes' style='background:#4A90E2;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Confirm Update</a></p>";
        exit;
    }
}

// ============================================
// Update images
// ============================================
$newImage = $updateMethod == 'url' ? $defaultImageUrl : $defaultLocalImage;

$updateQuery = "UPDATE properties SET image_url = ? 
                WHERE image_url LIKE '%placeholder%' 
                   OR image_url LIKE '%via.placeholder%'
                   OR image_url = ''";

$stmt = $conn->prepare($updateQuery);
if (!$stmt) {
    die("❌ Prepare Error: " . $conn->error . "\n");
}

$stmt->bind_param("s", $newImage);

if ($stmt->execute()) {
    $affectedRows = $stmt->affected_rows;
    echo "✅ Successfully updated $affectedRows rooms!\n";
    echo "✅ All rooms now use: $newImage\n";
} else {
    echo "❌ Error: " . $stmt->error . "\n";
}

$stmt->close();
$conn->close();

echo "\n✅ Done! Check find-rooms.php to see the updated images.\n";
?>
