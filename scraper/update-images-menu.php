<?php
/**
 * Image Update Menu - Multiple Options
 * Provides different ways to update room images
 */
require_once __DIR__ . '/../db.php';

$action = $_GET['action'] ?? 'menu';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Room Images - RoomFinder</title>
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
        .option {
            background: #f8f9fa;
            padding: 20px;
            margin: 15px 0;
            border-radius: 8px;
            border-left: 4px solid #4A90E2;
        }
        .option h3 {
            margin-top: 0;
            color: #333;
        }
        .btn {
            background: #4A90E2;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 10px;
        }
        .btn:hover {
            background: #357ABD;
        }
        .btn-danger {
            background: #e74c3c;
        }
        .btn-danger:hover {
            background: #c0392b;
        }
        form {
            margin-top: 15px;
        }
        input[type="text"], input[type="url"] {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
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
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🖼️ Room Images Update Tool</h1>

        <?php
        // Handle actions
        if ($action == 'update_url') {
            $newUrl = $_POST['image_url'] ?? '';
            if (empty($newUrl)) {
                echo "<div class='error'>Please provide an image URL</div>";
            } else {
                $query = "UPDATE properties SET image_url = ? WHERE image_url LIKE '%placeholder%' OR image_url LIKE '%via.placeholder%' OR image_url = ''";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("s", $newUrl);
                if ($stmt->execute()) {
                    $count = $stmt->affected_rows;
                    echo "<div class='success'>✅ Successfully updated $count rooms with the new image URL!</div>";
                } else {
                    echo "<div class='error'>Error: " . $stmt->error . "</div>";
                }
                $stmt->close();
            }
            $action = 'menu';
        }
        
        if ($action == 'menu'):
        ?>
        
        <div class="info">
            <strong>Current Status:</strong><br>
            <?php
            $totalQuery = "SELECT COUNT(*) as total FROM properties";
            $result = $conn->query($totalQuery);
            $total = $result->fetch_assoc()['total'];
            
            $placeholderQuery = "SELECT COUNT(*) as count FROM properties WHERE image_url LIKE '%placeholder%' OR image_url LIKE '%via.placeholder%' OR image_url = ''";
            $result = $conn->query($placeholderQuery);
            $placeholderCount = $result->fetch_assoc()['count'];
            
            echo "Total rooms: <strong>$total</strong><br>";
            echo "Rooms with placeholder images: <strong>$placeholderCount</strong>";
            ?>
        </div>

        <div class="option">
            <h3>Option 1: Update All to Single Image URL</h3>
            <p>Update all placeholder images to use one image URL (from Unsplash, etc.)</p>
            <form method="POST" action="?action=update_url">
                <input type="url" name="image_url" placeholder="https://images.unsplash.com/photo-..." 
                       value="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&h=600&fit=crop" required>
                <button type="submit" class="btn">Update All Images</button>
            </form>
        </div>

        <div class="option">
            <h3>Option 2: Edit Individual Rooms</h3>
            <p>Edit rooms one by one through the website interface</p>
            <a href="../find-rooms.php" class="btn">Go to Find Rooms</a>
        </div>

        <div class="option">
            <h3>Option 3: Fix All Images - Assign Different to Each Room ⭐ (Recommended)</h3>
            <p>Update ALL rooms and assign different room/apartment images to each - fixes the "same image" issue!</p>
            <a href="fix-all-images.php" class="btn" style="background: #27ae60;">🔧 Fix All Images</a>
        </div>

        <div class="option">
            <h3>Option 4: Assign Different Images (Placeholder Only)</h3>
            <p>Only update rooms with placeholder images</p>
            <a href="assign-random-images-web.php" class="btn" style="background: #17a2b8;">✨ Assign to Placeholders</a>
        </div>

        <div class="option">
            <h3>Option 5: Use Bulk Update Script</h3>
            <p>Run the command-line script for more control</p>
            <a href="bulk-update-images.php" class="btn">Run Bulk Update Script</a>
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <a href="../find-rooms.php" class="btn" style="background: #6c757d;">← Back to Find Rooms</a>
        </div>

        <?php endif; ?>
    </div>
</body>
</html>
