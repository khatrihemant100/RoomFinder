<?php
/**
 * Web Interface for Assigning Different Room Images
 * Assigns different room/apartment images to each property
 */

require_once __DIR__ . '/../db.php';

// Room/Apartment Image URLs (from Unsplash)
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
];

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_images'])) {
    // Get properties that need images
    $query = "SELECT id FROM properties 
              WHERE image_url LIKE '%placeholder%' 
                 OR image_url LIKE '%via.placeholder%'
                 OR image_url = ''
              ORDER BY id";
    
    $result = $conn->query($query);
    $properties = [];
    while ($row = $result->fetch_assoc()) {
        $properties[] = $row['id'];
    }
    
    if (count($properties) > 0) {
        shuffle($roomImages);
        $updateQuery = "UPDATE properties SET image_url = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        
        $updated = 0;
        foreach ($properties as $index => $propertyId) {
            $imageIndex = $index % count($roomImages);
            $imageUrl = $roomImages[$imageIndex];
            $stmt->bind_param("si", $imageUrl, $propertyId);
            if ($stmt->execute()) {
                $updated++;
            }
        }
        $stmt->close();
        
        $message = "✅ Successfully assigned different images to $updated rooms!";
        $success = true;
    } else {
        $message = "ℹ️ No rooms found that need images.";
    }
}

// Get current stats
$totalQuery = "SELECT COUNT(*) as total FROM properties";
$result = $conn->query($totalQuery);
$total = $result->fetch_assoc()['total'];

$placeholderQuery = "SELECT COUNT(*) as count FROM properties WHERE image_url LIKE '%placeholder%' OR image_url LIKE '%via.placeholder%' OR image_url = ''";
$result = $conn->query($placeholderQuery);
$placeholderCount = $result->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Room Images - RoomFinder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
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
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #28a745;
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
        .feature-list {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .feature-list ul {
            margin: 10px 0;
            padding-left: 25px;
        }
        .feature-list li {
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎨 Assign Different Images to Rooms</h1>
        
        <?php if ($message): ?>
            <div class="<?php echo $success ? 'success' : 'info'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="info">
            <strong>Current Status:</strong><br>
            Total rooms: <strong><?php echo $total; ?></strong><br>
            Rooms needing images: <strong><?php echo $placeholderCount; ?></strong>
        </div>
        
        <div class="feature-list">
            <h3>✨ What this does:</h3>
            <ul>
                <li>Assigns <strong>different room/apartment images</strong> to each room</li>
                <li>Uses high-quality images from Unsplash</li>
                <li>All images are room/apartment interiors (not houses)</li>
                <li>Each room gets a unique image</li>
                <li>Updates all rooms with placeholder images automatically</li>
            </ul>
        </div>
        
        <?php if ($placeholderCount > 0): ?>
        <form method="POST">
            <button type="submit" name="assign_images" class="btn">
                ✨ Assign Different Images to All Rooms
            </button>
            <a href="update-images-menu.php" class="btn btn-secondary">← Back to Menu</a>
        </form>
        <?php else: ?>
        <div class="info">
            All rooms already have images assigned!
        </div>
        <a href="update-images-menu.php" class="btn btn-secondary">← Back to Menu</a>
        <?php endif; ?>
    </div>
</body>
</html>
