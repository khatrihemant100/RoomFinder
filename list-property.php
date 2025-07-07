<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: user/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Note: Header for List Property page -->
    <meta charset="UTF-8">
    <title>List Property | RoomFinder</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Note: Small JS for demo login check -->
    <script>
        // Note: Demo login check
        document.addEventListener('DOMContentLoaded', function() {
            const loginSection = document.getElementById('login-section');
            const formSection = document.getElementById('form-section');
            const loginBtn = document.getElementById('login-btn');
            if (loginBtn) {
                loginBtn.addEventListener('click', function() {
                    loginSection.style.display = 'none';
                    formSection.style.display = 'block';
                });
            }
        });
    </script>
</head>
<style>
    /* Note: Unique section style for List Property page */
    .list-property-section {
        max-width: 520px;
        margin: 40px auto;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(74, 144, 226, 0.08);
        padding: 32px 24px;
        border: 1px solid #e5e7eb;
    }

    .list-property-section h1 {
        color: #4A90E2;
        font-size: 2rem;
        margin-bottom: 12px;
        text-align: center;
        font-family: 'Pacifico', cursive;
    }

    .list-property-section form label {
        font-weight: 500;
        color: #374151;
        display: block;
        margin-bottom: 4px;
    }

    .list-property-section form input,
    .list-property-section form select,
    .list-property-section form textarea {
        width: 100%;
        margin-bottom: 16px;
        padding: 10px 12px;
        border-radius: 8px;
        border: 1px solid #E2E8F0;
        font-size: 1rem;
        background: #f9fafb;
        transition: border 0.2s;
    }

    .list-property-section form input:focus,
    .list-property-section form select:focus,
    .list-property-section form textarea:focus {
        border-color: #4A90E2;
        outline: none;
        background: #fff;
    }

    .list-property-section form button {
        width: 100%;
        font-size: 1rem;
        border-radius: 8px;
        margin-top: 8px;
    }

    .list-property-section a {
        display: block;
        text-align: center;
        margin-top: 24px;
        color: #4A90E2;
        text-decoration: underline;
    }
</style>
<body>
    <section class="list-property-section">
        <!-- Note: Section shown when user is not logged in -->
        <div id="login-section">
            <h1>List Property</h1>
            <p>To use this feature, please <strong>Login</strong> first.</p>
            <button id="login-btn" class="px-4 py-2 bg-primary text-white rounded-button">Login</button>
        </div>

        <!-- Note: Form shown after login (hidden by default) -->
        <div id="form-section" style="display:none;">
            <h1>List Your Room</h1>
            <form>
                <label for="room-title">Room Title:</label>
                <input type="text" id="room-title" name="room-title" required>

                <label for="room-location">Location:</label>
                <input type="text" id="room-location" name="room-location" required>

                <label for="room-price">Price (per month):</label>
                <input type="number" id="room-price" name="room-price" required>

                <label for="room-type">Room Type:</label>
                <select id="room-type" name="room-type" required>
                    <option value="">Select</option>
                    <option value="single">Single</option>
                    <option value="double">Double</option>
                    <option value="flat">Flat</option>
                    <option value="hostel">Hostel</option>
                </select>

                <label for="room-description">Description:</label>
                <textarea id="room-description" name="room-description" rows="4" required></textarea>

                <label for="room-image">Room Photo:</label>
                <input type="file" id="room-image" name="room-image" accept="image/*">

                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-button">Upload Room</button>
            </form>
            <a href="index.html">Home</a>
        </div>
    </section>
</body>
</html>