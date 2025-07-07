<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: user/login.php");
    exit();
}

$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "roomfinder");
    if ($conn->connect_error) die("DB error");

    $title = $_POST['room-title'];
    $location = $_POST['room-location'];
    $price = $_POST['room-price'];
    $type = $_POST['room-type'];
    $desc = $_POST['room-description'];
    $user_id = $_SESSION["user_id"];

    // Handle image upload
    $imgPath = "";
    if (isset($_FILES["room-image"]) && $_FILES["room-image"]["error"] == 0) {
        $ext = pathinfo($_FILES["room-image"]["name"], PATHINFO_EXTENSION);
        $imgPath = "uploads/room_" . time() . "_" . rand(1000,9999) . "." . $ext;
        move_uploaded_file($_FILES["room-image"]["tmp_name"], $imgPath); // यो लाइन अनिवार्य!
    }

    $stmt = $conn->prepare("INSERT INTO rooms (user_id, title, location, price, type, description, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ississs", $user_id, $title, $location, $price, $type, $desc, $imgPath);

    if ($stmt->execute()) {
        $msg = "Room listed successfully!";
    } else {
        $msg = "Error. Please try again.";
    }
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
                <a href="list-property.php">List Property</a>
                <a href="about.html">About Us</a>
                <a href="contact.php">Contact</a>
            </nav>
            <div class="user-area">
                <?php if(isset($_SESSION["user_id"])): ?>
                    <span><?php echo htmlspecialchars($_SESSION["name"]); ?></span>
                    <a href="user/logout.php">Logout</a>
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
                <label for="room-description"><i class="ri-file-text-line"></i> Description</label>
                <textarea id="room-description" name="room-description" rows="4" required></textarea>
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
        <?php if(!empty($msg)): ?>
            <div style="margin-bottom:16px; color:<?php echo ($msg=='Room listed successfully!')?'#219150':'#c00'; ?>;font-weight:bold;">
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
    </script>
</body>
</html>