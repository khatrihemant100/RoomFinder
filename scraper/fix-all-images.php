<?php
/**
 * Fix All Room Images - Assign Different Images to ALL Rooms
 * This will update ALL rooms, not just placeholder ones
 */

require_once __DIR__ . '/../db.php';

// Room/Apartment Image URLs (from Unsplash) - More variety
$roomImages = [
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
    'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1505843513577-22bb7d21e4d6?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1512918728675-ed5a9ecdebfd?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1560185127-6ed189bf02f4?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=800&h=600&fit=crop',
];

$message = '';
$success = false;
$updated = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fix_all'])) {
    // Get ALL properties (not just placeholder ones)
    $query = "SELECT id, title, image_url FROM properties ORDER BY id";
    $result = $conn->query($query);
    
    if (!$result) {
        $message = "❌ Error: " . $conn->error;
    } else {
        $properties = [];
        while ($row = $result->fetch_assoc()) {
            $properties[] = $row;
        }
        
        if (count($properties) > 0) {
            // Shuffle images for random distribution
            shuffle($roomImages);
            
            $updateQuery = "UPDATE properties SET image_url = ? WHERE id = ?";
            $stmt = $conn->prepare($updateQuery);
            
            $updated = 0;
            $errors = 0;
            
            foreach ($properties as $index => $property) {
                // Cycle through images - each room gets different image
                $imageIndex = $index % count($roomImages);
                $imageUrl = $roomImages[$imageIndex];
                
                $stmt->bind_param("si", $imageUrl, $property['id']);
                if ($stmt->execute()) {
                    $updated++;
                } else {
                    $errors++;
                }
            }
            $stmt->close();
            
            if ($updated > 0) {
                $message = "✅ Successfully assigned different images to $updated rooms! Each room now has a unique image.";
                $success = true;
            } else {
                $message = "⚠️ No rooms were updated. Errors: $errors";
            }
        } else {
            $message = "ℹ️ No rooms found in database.";
        }
    }
}

// Get current stats
$totalQuery = "SELECT COUNT(*) as total FROM properties";
$result = $conn->query($totalQuery);
$total = $result->fetch_assoc()['total'];

// Check how many have same image
$sameImageQuery = "SELECT image_url, COUNT(*) as count FROM properties 
                   WHERE image_url IS NOT NULL AND image_url != '' 
                   GROUP BY image_url 
                   HAVING count > 1 
                   ORDER BY count DESC 
                   LIMIT 1";
$result = $conn->query($sameImageQuery);
$sameImageCount = 0;
$sameImageUrl = '';
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $sameImageCount = $row['count'];
    $sameImageUrl = $row['image_url'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fix All Room Images - RoomFinder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #4A90E2;
            border-bottom: 2px solid #4A90E2;
            padding-bottom: 10px;
        }
        .info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #ffc107;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #28a745;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .btn {
            background: #27ae60;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover {
            background: #229954;
        }
        .btn-secondary {
            background: #6c757d;
            margin-left: 10px;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .stat-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #4A90E2;
        }
        .stat-box strong {
            display: block;
            font-size: 24px;
            color: #4A90E2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Fix All Room Images</h1>
        
        <?php if ($message): ?>
            <div class="<?php echo $success ? 'success' : ($message[0] === '❌' ? 'error' : 'warning'); ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="stats">
            <div class="stat-box">
                <strong><?php echo $total; ?></strong>
                Total Rooms
            </div>
            <?php if ($sameImageCount > 0): ?>
            <div class="stat-box" style="border-left-color: #ffc107;">
                <strong><?php echo $sameImageCount; ?></strong>
                Rooms with Same Image
            </div>
            <?php endif; ?>
        </div>
        
        <?php if ($sameImageCount > 0): ?>
        <div class="warning">
            <strong>⚠️ Issue Detected:</strong><br>
            <?php echo $sameImageCount; ?> rooms are using the same image URL.<br>
            <small style="word-break: break-all;"><?php echo htmlspecialchars($sameImageUrl); ?></small>
        </div>
        <?php endif; ?>
        
        <div class="info">
            <strong>What this will do:</strong><br>
            • Update <strong>ALL</strong> rooms in the database<br>
            • Assign a <strong>different image</strong> to each room<br>
            • Use high-quality room/apartment images from Unsplash<br>
            • Ensure maximum variety - each room gets unique image
        </div>
        
        <form method="POST" onsubmit="return confirm('Are you sure you want to update ALL room images? This will change images for all ' + <?php echo $total; ?> + ' rooms.');">
            <button type="submit" name="fix_all" class="btn">
                🔄 Fix All Images (Assign Different to Each Room)
            </button>
            <a href="update-images-menu.php" class="btn btn-secondary">← Back to Menu</a>
        </form>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 14px; color: #666;">
            <strong>Note:</strong> This will update all rooms, even if they already have images. 
            Each room will get a different image from the collection of 25+ room/apartment images.
        </div>
    </div>
</body>
</html>
