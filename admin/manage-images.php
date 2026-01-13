<?php
require 'auth.php';

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
    'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1505843513577-22bb7d21e4d6?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1512918728675-ed5a9ecdebfd?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1560185127-6ed189bf02f4?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=800&h=600&fit=crop',
];

$message = '';
$success = false;
$updated = 0;

// Find scraper/system user ID (first user or user with name 'System')
$scraperUserId = null;
$systemUserQuery = "SELECT id FROM users WHERE name = 'System' OR email = 'system@roomfinder.com' LIMIT 1";
$systemResult = $conn->query($systemUserQuery);
if ($systemResult && $systemResult->num_rows > 0) {
    $scraperUserId = $systemResult->fetch_assoc()['id'];
} else {
    // Fallback to first user ID
    $firstUserQuery = "SELECT id FROM users ORDER BY id ASC LIMIT 1";
    $firstResult = $conn->query($firstUserQuery);
    if ($firstResult && $firstResult->num_rows > 0) {
        $scraperUserId = $firstResult->fetch_assoc()['id'];
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['assign_random'])) {
        // Get properties - ONLY update script-added images from scraper user
        // Skip user-uploaded images (in uploads/ folder)
        $query = "SELECT id, image_url, user_id FROM properties 
                  WHERE (image_url LIKE '%placeholder%' 
                     OR image_url LIKE '%via.placeholder%'
                     OR image_url LIKE '%unsplash%'
                     OR image_url = ''
                     OR image_url IS NULL)
                  AND (image_url NOT LIKE 'uploads/%' OR image_url IS NULL OR image_url = '')";
        
        // If scraper user ID found, only update that user's properties
        if ($scraperUserId !== null) {
            $query .= " AND user_id = " . intval($scraperUserId);
        }
        
        $result = $conn->query($query);
        $properties = [];
        while ($row = $result->fetch_assoc()) {
            $imgUrl = $row['image_url'] ?? '';
            // Double check: Skip user-uploaded images
            if (!empty($imgUrl) && strpos($imgUrl, 'uploads/') === 0) {
                continue; // Skip user-uploaded images
            }
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
            
            $message = "✅ Successfully assigned different images to $updated rooms! User-uploaded images were preserved.";
            $success = true;
        } else {
            $message = "ℹ️ No rooms found with script-added images to update.";
        }
    }
    
    if (isset($_POST['update_all_single'])) {
        $newUrl = trim($_POST['image_url'] ?? '');
        if (!empty($newUrl)) {
            // Build query - only update scraper user's properties
            $query = "UPDATE properties SET image_url = ? 
                      WHERE (image_url LIKE '%placeholder%' 
                         OR image_url LIKE '%via.placeholder%'
                         OR image_url LIKE '%unsplash%'
                         OR image_url = ''
                         OR image_url IS NULL)
                      AND (image_url NOT LIKE 'uploads/%' OR image_url IS NULL OR image_url = '')";
            
            // Add user_id filter if scraper user found
            if ($scraperUserId !== null) {
                $query .= " AND user_id = " . intval($scraperUserId);
            }
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $newUrl);
            if ($stmt->execute()) {
                $updated = $stmt->affected_rows;
                $message = "✅ Successfully updated $updated rooms with the new image URL!";
                $success = true;
            } else {
                $message = "❌ Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "⚠️ Please provide an image URL";
        }
    }
}

// Get current stats
$totalQuery = "SELECT COUNT(*) as total FROM properties";
$result = $conn->query($totalQuery);
$total = $result->fetch_assoc()['total'];

// Count script images (only from scraper user, not user-uploaded)
$scriptImagesQuery = "SELECT COUNT(*) as count FROM properties 
                      WHERE (image_url LIKE '%placeholder%' 
                         OR image_url LIKE '%via.placeholder%'
                         OR image_url LIKE '%unsplash%'
                         OR image_url = ''
                         OR image_url IS NULL)
                      AND (image_url NOT LIKE 'uploads/%' OR image_url IS NULL OR image_url = '')";
if ($scraperUserId !== null) {
    $scriptImagesQuery .= " AND user_id = " . intval($scraperUserId);
}
$result = $conn->query($scriptImagesQuery);
$scriptImageCount = $result->fetch_assoc()['count'];

$userImagesQuery = "SELECT COUNT(*) as count FROM properties 
                     WHERE image_url LIKE 'uploads/%'";
$result = $conn->query($userImagesQuery);
$userImageCount = $result->fetch_assoc()['count'];

// Check how many have same image
$sameImageQuery = "SELECT image_url, COUNT(*) as count FROM properties 
                   WHERE image_url IS NOT NULL 
                     AND image_url != '' 
                     AND (image_url LIKE '%unsplash%' 
                          OR image_url LIKE '%placeholder%'
                          OR image_url NOT LIKE 'uploads/%')
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Images - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <?php include 'sidebar.php'; ?>
    
    <div class="ml-64 p-8">
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Manage Room Images</h2>
            <p class="text-gray-600 mt-2">Update and manage images for all properties</p>
        </div>

        <?php if ($message): ?>
            <div class="mb-6 p-4 rounded-lg <?php echo $success ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-yellow-50 text-yellow-800 border border-yellow-200'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Rooms</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $total; ?></p>
                    </div>
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="ri-home-line text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Script Images</p>
                        <p class="text-3xl font-bold text-green-600 mt-2"><?php echo $scriptImageCount; ?></p>
                    </div>
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="ri-image-line text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">User Uploaded</p>
                        <p class="text-3xl font-bold text-purple-600 mt-2"><?php echo $userImageCount; ?></p>
                    </div>
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="ri-upload-cloud-line text-purple-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <?php if ($sameImageCount > 0): ?>
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Same Image</p>
                        <p class="text-3xl font-bold text-yellow-600 mt-2"><?php echo $sameImageCount; ?></p>
                    </div>
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="ri-alert-line text-yellow-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($sameImageCount > 0): ?>
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg mb-6">
            <div class="flex items-start">
                <i class="ri-alert-line text-yellow-600 text-xl mr-3 mt-1"></i>
                <div>
                    <p class="font-semibold text-yellow-800">⚠️ Issue Detected</p>
                    <p class="text-yellow-700 mt-1"><?php echo $sameImageCount; ?> rooms are using the same image URL.</p>
                    <p class="text-yellow-600 text-sm mt-1 break-all"><?php echo htmlspecialchars($sameImageUrl); ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Action Cards -->
        <div class="grid md:grid-cols-2 gap-6 mb-8">
            <!-- Assign Different Images -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-lg flex items-center justify-center text-white mr-4">
                        <i class="ri-image-edit-line text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Assign Different Images</h3>
                        <p class="text-gray-600 text-sm">Each room gets a unique image</p>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <p class="text-sm text-gray-700 mb-2"><strong>What this does:</strong></p>
                    <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                        <li>Assigns different room/apartment images to each room</li>
                        <li>Uses high-quality images from Unsplash</li>
                        <li>Preserves user-uploaded images</li>
                        <li>Only updates script-added images</li>
                    </ul>
                </div>
                <form method="POST" onsubmit="return confirm('This will update <?php echo $scriptImageCount; ?> rooms with script-added images. User-uploaded images will be preserved. Continue?');">
                    <button type="submit" name="assign_random" class="w-full px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-300 transform hover:scale-105 font-semibold">
                        <i class="ri-magic-line mr-2"></i> Assign Different Images
                    </button>
                </form>
            </div>

            <!-- Update All to Single Image -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center text-white mr-4">
                        <i class="ri-image-add-line text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Update All to Single Image</h3>
                        <p class="text-gray-600 text-sm">Use one image URL for all</p>
                    </div>
                </div>
                <form method="POST">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Image URL</label>
                        <input type="url" name="image_url" 
                               value="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&h=600&fit=crop"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="https://images.unsplash.com/..." required>
                    </div>
                    <button type="submit" name="update_all_single" class="w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 font-semibold">
                        <i class="ri-upload-line mr-2"></i> Update All Script Images
                    </button>
                </form>
            </div>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
            <div class="flex items-start">
                <i class="ri-information-line text-blue-600 text-xl mr-3 mt-1"></i>
                <div>
                    <p class="font-semibold text-blue-800">Important Notes:</p>
                    <ul class="text-blue-700 text-sm mt-2 space-y-1 list-disc list-inside">
                        <li>Only script-added images from scraper user (User ID: <?php echo $scraperUserId ?? 'N/A'; ?>) will be updated</li>
                        <li>User-uploaded images in the <code class="bg-blue-100 px-1 rounded">uploads/</code> folder will be preserved</li>
                        <li>All user-uploaded images from other users are protected and will NOT be changed</li>
                        <li>All changes are permanent - make sure to backup if needed</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
