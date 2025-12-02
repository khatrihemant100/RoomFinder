<?php
session_start();
// Check if user is logged in and is an owner
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"]) || $_SESSION["role"] !== 'owner') {
    header("Location: user/login.php?error=owner_only");
    exit();
}

require_once 'db.php';

$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = $_POST['room-title'];
    $location = $_POST['room-location'];
    $price = $_POST['room-price'];
    $type = $_POST['room-type'];
    $desc = $_POST['room-description'];
    $train_station = $_POST['room-train-station'];
    $status = $_POST['room-status'];
    
    $user_id = $_SESSION["user_id"];

    $imgPath = "";
    if (isset($_FILES["room-image"]) && $_FILES["room-image"]["error"] == 0) {
        // File validation
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES["room-image"]["name"], PATHINFO_EXTENSION));
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($ext, $allowed)) {
            $msg = "Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.";
        } elseif ($_FILES["room-image"]["size"] > $maxSize) {
            $msg = "File size too large. Maximum size is 5MB.";
        } else {
            $imgPath = "uploads/room_" . time() . "_" . rand(1000,9999) . "." . $ext;
            if (!move_uploaded_file($_FILES["room-image"]["tmp_name"], $imgPath)) {
                $msg = "Error uploading image. Please try again.";
            }
        }
    } else {
        $msg = "Please select an image file.";
    }

    // Only proceed if no errors
    if (empty($msg) && !empty($imgPath)) {
        // Fixed: Correct parameter order matching SQL query
        $stmt = $conn->prepare("INSERT INTO properties (user_id, title, location, price, type, train_station, status, description, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ississsss", $user_id, $title, $location, $price, $type, $train_station, $status, $desc, $imgPath);

        if ($stmt->execute()) {
            $msg = "Room listed successfully!";
            // Clear form by redirecting
            header("Location: list-property.php?success=1");
            exit();
        } else {
            $msg = "Error: " . $conn->error;
        }
        $stmt->close();
    }
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List Property | RoomFinder</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <style>
        body {
            background: linear-gradient(135deg, #e0e7ff 0%, #f8fafc 100%);
            min-height: 100vh;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        /* Navbar (from index.php) */
        .rf-header {
            background: #fff;
            box-shadow: 0 2px 8px rgba(74,144,226,0.08);
            padding: 18px 0;
            margin-bottom: 30px;
        }
        .rf-header .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }
        .rf-header a.logo {
            font-family: 'Pacifico', cursive;
            font-size: 2rem;
            color: #4A90E2;
            text-decoration: none;
        }
        .rf-header .nav {
            display: flex;
            gap: 28px;
        }
        .rf-header .nav a {
            color: #333;
            font-weight: 500;
            text-decoration: none;
            transition: color 0.2s;
        }
        .rf-header .nav a:hover {
            color: #4A90E2;
        }
        .rf-header .user-area {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .rf-header .user-area span {
            color: #4A90E2;
            font-weight: 600;
            background: #eaf4fb;
            padding: 6px 16px;
            border-radius: 8px;
        }
        .rf-header .user-area a {
            background: #FF6B6B;
            color: #fff;
            padding: 8px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.2s;
        }
        .rf-header .user-area a:hover {
            background: #e74c3c;
        }
        /* Unique Form Card */
        .list-property-section {
            max-width: 480px;
            margin: 48px auto;
            background: rgba(255,255,255,0.98);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(74,144,226,0.13), 0 1.5px 8px rgba(67,198,172,0.08);
            padding: 36px 28px 28px 28px;
            border: 1px solid #e5e7eb;
            animation: fadeInUp 0.7s;
        }
        .list-property-section h1 {
            color: #4A90E2;
            font-size: 2rem;
            margin-bottom: 18px;
            text-align: center;
            font-family: 'Pacifico', cursive;
            letter-spacing: 1px;
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: flex;
            align-items: center;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
            font-size: 1rem;
        }
        .form-group label i {
            margin-right: 8px;
            color: #4A90E2;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1.5px solid #E2E8F0;
            font-size: 1rem;
            background: #f9fafb;
            transition: border 0.2s, box-shadow 0.2s;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #4A90E2;
            outline: none;
            background: #fff;
            box-shadow: 0 2px 12px #4A90E2a0;
        }
        .form-group input[type="file"] {
            padding: 6px 0;
            background: none;
        }
        #image-preview {
            margin-top: 10px;
            display: none;
        }
        #image-preview img {
            max-width: 100%;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(74,144,226,0.14);
        }
        .list-property-section button[type="submit"] {
            width: 100%;
            font-size: 1rem;
            border-radius: 10px;
            margin-top: 8px;
            background: linear-gradient(90deg, #4A90E2 60%, #43c6ac 100%);
            color: #fff;
            font-weight: 600;
            padding: 12px 0;
            box-shadow: 0 2px 8px #4A90E2a0;
            border: none;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .list-property-section button[type="submit"]:hover {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 6px 18px #4A90E2a0;
        }
        .list-property-section a {
            display: block;
            text-align: center;
            margin-top: 24px;
            color: #4A90E2;
            text-decoration: underline;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px);}
            to { opacity: 1; transform: translateY(0);}
        }




        /* Responsive */
        .room-listing {
    display: flex;
    gap: 24px;
    flex-wrap: wrap;
    margin-top: 40px;
}

.room-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 16px rgba(74,144,226,0.1);
    overflow: hidden;
    display: flex;
    width: 100%;
    max-width: 700px;
}

.room-card img {
    width: 220px;
    height: 180px;
    object-fit: cover;
}

.room-details {
    padding: 16px;
    flex: 1;
}

.room-details h3 {
    color: #4A90E2;
    margin-bottom: 8px;
}

.room-details p {
    margin: 4px 0;
    color: #374151;
}

    </style>
</head>
<body>
    <!-- Navbar/Header (from index.php) -->
    <header class="rf-header">
        <div class="container">
            <a href="index.php" class="logo">RoomFinder</a>
            <nav class="nav">
                <a href="index.php">Home</a>
                <a href="find-rooms.php">Find Rooms</a>
                <?php if(isset($_SESSION["role"]) && $_SESSION["role"] === 'owner'): ?>
                <a href="list-property.php">List Property</a>
                <?php endif; ?>
                <a href="about.php">About Us</a>
                <a href="contact.php">Contact</a>
                <?php if(isset($_SESSION["user_id"])): ?>
                <a href="messages.php" style="position:relative;">
                    Messages
                    <?php
                    require_once 'db.php';
                    $unreadStmt = $conn->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
                    $unreadStmt->bind_param("i", $_SESSION["user_id"]);
                    $unreadStmt->execute();
                    $unreadStmt->bind_result($unread_count);
                    $unreadStmt->fetch();
                    $unreadStmt->close();
                    if ($unread_count > 0) {
                        echo '<span style="position:absolute;top:0;right:0;background:#FF6B6B;color:white;border-radius:50%;width:20px;height:20px;min-width:20px;min-height:20px;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:bold;line-height:20px;transform:translate(50%,-50%);">' . $unread_count . '</span>';
                    }
                    ?>
                </a>
                <?php endif; ?>
            </nav>
  <div class="user-area" style="position:relative;">
    <?php if(isset($_SESSION["user_id"])): ?>
      <?php
      $user_name = $_SESSION["name"] ?? "User";
      $user_photo = $_SESSION["profile_photo"] ?? null;
      $user_initial = strtoupper(substr($user_name, 0, 1));
      ?>
      <button onclick="toggleUserDropdown()" style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:none;background:transparent;cursor:pointer;border-radius:8px;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'">
        <div style="width:40px;height:40px;border-radius:50%;<?php echo $user_photo ? '' : 'background:#10b981;'; ?>display:flex;align-items:center;justify-content:center;overflow:hidden;">
          <?php if ($user_photo): ?>
            <img src="uploads/<?php echo htmlspecialchars($user_photo); ?>" alt="<?php echo htmlspecialchars($user_name); ?>" style="width:100%;height:100%;object-fit:cover;">
          <?php else: ?>
            <span style="color:white;font-weight:bold;font-size:18px;"><?php echo $user_initial; ?></span>
          <?php endif; ?>
        </div>
        <span style="font-weight:600;color:#374151;"><?php echo htmlspecialchars(strtoupper($user_name)); ?></span>
        <i class="ri-arrow-down-s-line" style="color:#6b7280;"></i>
      </button>
      <div id="userDropdown" style="display:none;position:absolute;right:0;top:100%;margin-top:8px;width:192px;background:white;border-radius:8px;box-shadow:0 4px 6px rgba(0,0,0,0.1);border:1px solid #e5e7eb;padding:8px 0;z-index:50;">
        <a href="index.php" style="display:flex;align-items:center;gap:12px;padding:8px 16px;color:#374151;text-decoration:none;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'">
          <i class="ri-dashboard-line" style="color:#6b7280;"></i>
          <span>Dashboard</span>
        </a>
        <a href="user/profile.php" style="display:flex;align-items:center;gap:12px;padding:8px 16px;color:#374151;text-decoration:none;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'">
          <i class="ri-user-line" style="color:#6b7280;"></i>
          <span>Profile</span>
        </a>
        <hr style="margin:8px 0;border:none;border-top:1px solid #e5e7eb;">
        <a href="user/logout.php" style="display:flex;align-items:center;gap:12px;padding:8px 16px;color:#ef4444;text-decoration:none;" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='transparent'">
          <i class="ri-logout-box-r-line" style="color:#ef4444;"></i>
          <span>Logout</span>
        </a>
      </div>
    <?php else: ?>
                    <a href="user/login.php" style="background:#4A90E2;">Sign In</a>
                    <a href="user/createaccount.php">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Unique, modern form with real-time image preview -->
    <section class="list-property-section">
        <h1>List Your Room</h1>
        <form id="room-form" method="post" enctype="multipart/form-data" autocomplete="off">
            <div class="form-group">
                <label for="room-title"><i class="ri-home-4-line"></i> Room Title</label>
                <input type="text" id="room-title" name="room-title" required>
            </div>
            <div class="form-group">
                <label for="room-location"><i class="ri-map-pin-line"></i> Location</label>
                <input type="text" id="room-location" name="room-location" required>
            </div>
            <div class="form-group">
                <label for="room-price"><i class="ri-money-dollar-circle-line"></i> Price (per month)</label>
                <input type="number" id="room-price" name="room-price" required>
            </div>
            <div class="form-group">
                <label for="room-type"><i class="ri-hotel-bed-line"></i> Room Type</label>
                <select id="room-type" name="room-type" required>
                    <option value="">Select</option>
                    <option value="single">Single</option>
                    <option value="double">Double</option>
                    <option value="flat">Flat</option>
                    <option value="hostel">Hostel</option>
                </select>
            </div>
             <div class="form-group">
    <label for="room-train-station"><i class="ri-train-line"></i> Near Train Station</label>
    <input type="text" id="room-train-station" name="room-train-station" required>
        </div>
            <div class="form-group">
                <label for="room-description"><i class="ri-file-text-line"></i> Description</label>
                <textarea id="room-description" name="room-description" rows="4" required></textarea>
            </div>
             <div class="form-group">
            <label for="room-status"><i class="ri-checkbox-circle-line"></i> Room Status</label>
            <select id="room-status" name="room-status" required>
                <option value="">Select</option>
                <option value="available">Available</option>
                <option value="not_available">Not_Available</option>
                <option value="maintenance">Under_Maintenance</option>
                <option value="reserved">Reserved</option>
              
            </select>
        </div>
            <div class="form-group">
                <label for="room-image"><i class="ri-image-add-line"></i> Room Photo</label>
                <input type="file" id="room-image" name="room-image" accept="image/*" required>
                <div id="image-preview">
                    <img src="" alt="Preview">
                </div>
            </div>
     

       

            <button type="submit">Upload Room</button>
        </form>
        <?php if(isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div style="margin-bottom:16px; color:#219150;font-weight:bold;padding:12px;background:#d4edda;border-radius:8px;border:1px solid #c3e6cb;">
                Room listed successfully!
            </div>
        <?php elseif(!empty($msg)): ?>
            <div style="margin-bottom:16px; color:#c00;font-weight:bold;padding:12px;background:#f8d7da;border-radius:8px;border:1px solid #f5c6cb;">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>
        <a href="index.php">← Back to Home</a>
    </section>
    <script>
        // Real-time image preview
        document.getElementById('room-image').addEventListener('change', function(e) {
            const preview = document.getElementById('image-preview');
            const img = preview.querySelector('img');
            const file = this.files[0];
            if (file) {
                img.src = URL.createObjectURL(file);
                preview.style.display = 'block';
            } else {
                img.src = '';
                preview.style.display = 'none';
            }
        });
        // Optional: animated input focus
        document.querySelectorAll('.form-group input, .form-group select, .form-group textarea').forEach(el => {
            el.addEventListener('focus', function() {
                this.parentElement.style.boxShadow = '0 2px 12px #4A90E2a0';
            });
            el.addEventListener('blur', function() {
                this.parentElement.style.boxShadow = '';
            });
  });
  
  // Toggle user dropdown menu
  function toggleUserDropdown() {
    const dropdown = document.getElementById('userDropdown');
    if (dropdown) {
      dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
    }
  }
  
  // Close dropdown when clicking outside
  document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('userDropdown');
    const button = event.target.closest('[onclick="toggleUserDropdown()"]');
    if (dropdown && !dropdown.contains(event.target) && !button) {
      dropdown.style.display = 'none';
    }
  });
</script>
</body>
</html>