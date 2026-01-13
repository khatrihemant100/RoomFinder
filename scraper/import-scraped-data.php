<?php
/**
 * Import scraped room data from JSON files to database
 * Supports multiple JSON files in the data folder
 */

// Use main database connection
require_once __DIR__ . '/../db.php';

// ============================================
// Configuration
// ============================================
$dataFolder = __DIR__ . '/../data/';
$defaultUserId = 1; // Default user ID for scraped properties
$defaultStatus = 'available';

// Placeholder image - using a reliable placeholder service
// You can also use a local default image: 'uploads/default-room.jpg'
$placeholderImage = 'https://via.placeholder.com/400x300/4A90E2/FFFFFF?text=Room+Image';

// ============================================
// Find all JSON files in data folder
// ============================================
$jsonFiles = glob($dataFolder . 'suumo_*.json');

if (empty($jsonFiles)) {
    die("❌ No JSON files found in: $dataFolder\n");
}

echo "📁 Found " . count($jsonFiles) . " JSON file(s)\n\n";

// ============================================
// Process each JSON file
// ============================================
$totalInserted = 0;
$totalSkipped = 0;

foreach ($jsonFiles as $jsonFile) {
    echo "📄 Processing: " . basename($jsonFile) . "\n";
    
    if (!file_exists($jsonFile)) {
        echo "   ⚠️  File not found, skipping...\n";
        continue;
    }

    $jsonData = file_get_contents($jsonFile);
    $properties = json_decode($jsonData, true);

    if ($properties === null || !is_array($properties)) {
        echo "   ⚠️  Invalid JSON, skipping...\n";
        continue;
    }

    echo "   📊 Found " . count($properties) . " properties\n";

    // ============================================
    // Prepare SQL statement
    // ============================================
    $sql = "INSERT INTO properties 
            (user_id, title, location, train_station, price, status, description, 
             image_url, type, management_fee, deposit, key_money) 
            VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("❌ SQL Prepare Error: " . $conn->error . "\n");
    }

    $inserted = 0;
    $skipped = 0;

    foreach ($properties as $p) {
        // Skip if essential data is missing
        if (empty($p['title']) || empty($p['address']) || empty($p['rent'])) {
            $skipped++;
            continue;
        }

        // Build description from available data
        $descriptionParts = [];
        if (!empty($p['layout'])) {
            $descriptionParts[] = "Layout: " . $p['layout'];
        }
        if (!empty($p['area'])) {
            $descriptionParts[] = "Area: " . $p['area'] . "m²";
        }
        if (isset($p['age']) && $p['age'] !== null) {
            if ($p['age'] == 0) {
                $descriptionParts[] = "Building: New construction";
            } else {
                $descriptionParts[] = "Building age: " . $p['age'] . " years";
            }
        }
        if (!empty($p['access'])) {
            $descriptionParts[] = "Access: " . $p['access'];
        }
        
        $description = !empty($descriptionParts) ? implode(" | ", $descriptionParts) : "Scraped property from Suumo";

        // Prepare values - clean and validate
        $user_id = $defaultUserId;
        $title = trim($p['title'] ?? '');
        $location = trim($p['address'] ?? '');
        $train_station = trim($p['access'] ?? '');
        $price = floatval($p['rent'] ?? 0);
        $status = $defaultStatus;
        $image_url = $placeholderImage; // Use placeholder for now
        $type = trim($p['layout'] ?? '');
        $management_fee = (!empty($p['management_fee']) && $p['management_fee'] !== null) ? floatval($p['management_fee']) : 0.00;
        $deposit = (!empty($p['deposit']) && $p['deposit'] !== null) ? floatval($p['deposit']) : 0.00;
        $key_money = (!empty($p['key_money']) && $p['key_money'] !== null) ? floatval($p['key_money']) : 0.00;

        // Bind parameters - using 'd' for decimal/float values
        $stmt->bind_param(
            "isssdssssddd",
            $user_id,
            $title,
            $location,
            $train_station,
            $price,
            $status,
            $description,
            $image_url,
            $type,
            $management_fee,
            $deposit,
            $key_money
        );

        if ($stmt->execute()) {
            $inserted++;
        } else {
            echo "   ⚠️  Error inserting: " . $stmt->error . "\n";
            $skipped++;
        }
    }

    $stmt->close();
    
    echo "   ✅ Inserted: $inserted | ⚠️  Skipped: $skipped\n\n";
    
    $totalInserted += $inserted;
    $totalSkipped += $skipped;
}

// ============================================
// Summary
// ============================================
echo "═══════════════════════════════════════\n";
echo "📊 SUMMARY\n";
echo "═══════════════════════════════════════\n";
echo "✅ Total inserted: $totalInserted\n";
echo "⚠️  Total skipped: $totalSkipped\n";
echo "═══════════════════════════════════════\n";

$conn->close();
?>
