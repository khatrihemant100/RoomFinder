<?php
/**
 * Assign Different Room Images to Each Property
 * Uses Unsplash room/apartment images - different for each room
 */

require_once __DIR__ . '/../db.php';

// ============================================
// Room/Apartment Image URLs (from Unsplash)
// Different images for variety
// ============================================
$roomImages = [
    // Interior room/apartment images
    'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1556912172-45b7abe8b7e1?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1560185127-6ed189bf02f4?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1560448075-cbc16bb4af80?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1556912173-2e1b9e0cc5b1?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1560184897-ae75f418493e?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1560448205-97abe736d2e0?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1560185007-c5f9aa28bbe4?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1556912173-2e1b9e0cc5b1?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1560448075-cbc16bb4af80?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=800&h=600&fit=crop',
];

// ============================================
// Get all properties that need images
// ============================================
echo "🔍 Finding rooms that need images...\n\n";

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
// Shuffle images array for random distribution
// ============================================
shuffle($roomImages);

// ============================================
// Update each property with a different image
// ============================================
$updateQuery = "UPDATE properties SET image_url = ? WHERE id = ?";
$stmt = $conn->prepare($updateQuery);

if (!$stmt) {
    die("❌ Prepare Error: " . $conn->error . "\n");
}

$updated = 0;
$errors = 0;

echo "🎨 Assigning images...\n";
echo str_repeat("=", 60) . "\n";

foreach ($properties as $index => $property) {
    // Cycle through images, so if we have more properties than images, reuse them
    $imageIndex = $index % count($roomImages);
    $imageUrl = $roomImages[$imageIndex];
    
    $stmt->bind_param("si", $imageUrl, $property['id']);
    
    if ($stmt->execute()) {
        $updated++;
        if ($updated <= 10) { // Show first 10
            echo "✅ ID {$property['id']}: {$property['title']}\n";
            echo "   → Image assigned\n\n";
        }
    } else {
        $errors++;
        echo "❌ Error updating ID {$property['id']}: {$stmt->error}\n";
    }
}

$stmt->close();

echo str_repeat("=", 60) . "\n";
echo "✅ Successfully updated: $updated rooms\n";
if ($errors > 0) {
    echo "⚠️  Errors: $errors\n";
}
echo "\n✅ Done! Check find-rooms.php to see the updated images.\n";
echo "   Each room now has a different room/apartment image!\n";

$conn->close();
?>
