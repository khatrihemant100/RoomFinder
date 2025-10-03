<?php
session_start();
require '../db.php';

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $role = $_POST["role"];

    // Landlord fields
    $room_capacity = isset($_POST["room_capacity"]) ? $_POST["room_capacity"] : null;
    $room_type = isset($_POST["room_type"]) ? $_POST["room_type"] : null;
    $location = isset($_POST["location"]) ? $_POST["location"] : null;
    $rent = isset($_POST["rent"]) ? $_POST["rent"] : null;

    // Tenant fields
    $budget_min = isset($_POST["budget_min"]) ? $_POST["budget_min"] : null;
    $budget_max = isset($_POST["budget_max"]) ? $_POST["budget_max"] : null;
    $preferred_location = isset($_POST["preferred_location"]) ? $_POST["preferred_location"] : null;
    $roommate_count = isset($_POST["roommate_count"]) ? $_POST["roommate_count"] : null;
    $preferred_room_type = isset($_POST["preferred_room_type"]) ? $_POST["preferred_room_type"] : null;

    if ($name && $email && $password && $role) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, room_capacity, room_type, location, rent, budget_min, budget_max, preferred_location, roommate_count, preferred_room_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssissiiisii", $name, $email, $hashed_password, $role, $room_capacity, $room_type, $location, $rent, $budget_min, $budget_max, $preferred_location, $roommate_count, $preferred_room_type);
        if ($stmt->execute()) {
            $message = "<div class='success'>User account created successfully. <a href='login.php'>Login here</a>.</div>";
        } else {
            $message = "<div class='error'>Email already exists or error occurred.</div>";
        }
        $stmt->close();
    } else {
        $message = "<div class='error'>All required fields must be filled.</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create User Account</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function toggleFields() {
            const role = document.getElementById("role").value;
            const landlordFields = document.getElementById("landlordFields");
            const tenantFields = document.getElementById("tenantFields");

            landlordFields.style.display = role === "landlord" ? "block" : "none";
            tenantFields.style.display = role === "tenant" ? "block" : "none";
        }
    </script>
</head>
<body class="bg-gray-50 font-sans">
<div class="form-container bg-white p-8 rounded-2xl shadow-lg max-w-lg mx-auto mt-16 border-2 border-gradient-to-r from-green-400 to-blue-500">
    <h2 class="text-3xl text-center text-green-500 mb-6 font-bold">Create New Account</h2>
    <?php echo $message; ?>
    <form method="POST" class="space-y-4">
        <input type="text" name="name" placeholder="Full Name" class="w-full p-3 border border-green-300 rounded-lg bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-400" required>
        <input type="email" name="email" placeholder="Email" class="w-full p-3 border border-green-300 rounded-lg bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-400" required>
        <input type="password" name="password" placeholder="Password" class="w-full p-3 border border-green-300 rounded-lg bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-400" required>

        <select name="role" id="role" onchange="toggleFields()" class="w-full p-3 border border-green-300 rounded-lg bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-400" required>
            <option value="">Select Role</option>
            <option value="tenant">Tenant (Room Liner)</option>
            <option value="landlord">Landlord (Room Provider)</option>
        </select>

        <!-- Landlord Fields -->
        <div id="landlordFields" class="space-y-3" style="display:none;">
            <input type="number" name="room_capacity" placeholder="Room Capacity" class="w-full p-3 border border-green-300 rounded-lg bg-gray-50">
            <select name="room_type" class="w-full p-3 border border-green-300 rounded-lg bg-gray-50">
                <option value="">Select Room Type</option>
                <option value="single">Single</option>
                <option value="double">Double</option>
                <option value="shared">Shared</option>
            </select>
            <input type="text" name="location" placeholder="Location" class="w-full p-3 border border-green-300 rounded-lg bg-gray-50">
            <input type="number" name="rent" placeholder="Rent per Month" class="w-full p-3 border border-green-300 rounded-lg bg-gray-50">
        </div>

        <!-- Tenant Fields -->
        <div id="tenantFields" class="space-y-3" style="display:none;">
            <input type="number" name="budget_min" placeholder="Minimum Budget" class="w-full p-3 border border-green-300 rounded-lg bg-gray-50">
            <input type="number" name="budget_max" placeholder="Maximum Budget" class="w-full p-3 border border-green-300 rounded-lg bg-gray-50">
            <input type="text" name="preferred_location" placeholder="Preferred Location" class="w-full p-3 border border-green-300 rounded-lg bg-gray-50">
            <input type="number" name="roommate_count" placeholder="Number of Roommates" class="w-full p-3 border border-green-300 rounded-lg bg-gray-50">
            <select name="preferred_room_type" class="w-full p-3 border border-green-300 rounded-lg bg-gray-50">
                <option value="">Preferred Room Type</option>
                <option value="single">Single</option>
                <option value="double">Double</option>
                <option value="shared">Shared</option>
            </select>
        </div>

        <button type="submit" class="w-full p-3 bg-green-400 text-white rounded-lg font-bold hover:bg-green-500 transition-colors">Create Account</button>
    </form>
    <a class="block text-center mt-4 text-green-500 hover:underline" href="login.php">Already have an account? Login</a>
</div>
</body>
</html>
