<?php
require 'auth.php';

// Handle settings update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST as $key => $value) {
        $stmt = $conn->prepare("INSERT INTO admin_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->bind_param("sss", $key, $value, $value);
        $stmt->execute();
        $stmt->close();
    }
    
    // Log action
    $conn->query("INSERT INTO admin_logs (admin_id, action, target_type, details) VALUES ({$_SESSION['admin_id']}, 'update_settings', 'settings', 'Settings updated')");
    
    $success = "Settings updated successfully!";
}

// Get current settings
$settings = [];
$result = $conn->query("SELECT setting_key, setting_value FROM admin_settings");
while ($row = $result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <?php include 'sidebar.php'; ?>
    
    <div class="ml-64 p-8">
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Settings</h2>
            <p class="text-gray-600 mt-2">Manage website settings and preferences</p>
        </div>

        <?php if (isset($success)): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                <i class="ri-check-line mr-2"></i><?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <!-- General Settings -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6">General Settings</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Site Name</label>
                        <input type="text" name="site_name" value="<?php echo htmlspecialchars($settings['site_name'] ?? 'RoomFinder'); ?>" 
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Site Email</label>
                        <input type="email" name="site_email" value="<?php echo htmlspecialchars($settings['site_email'] ?? 'admin@roomfinder.com'); ?>" 
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Items Per Page</label>
                        <input type="number" name="items_per_page" value="<?php echo htmlspecialchars($settings['items_per_page'] ?? '20'); ?>" 
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Property Settings -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6">Property Settings</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-1">Auto Approve Properties</label>
                            <p class="text-sm text-gray-500">Automatically approve new property listings</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="auto_approve_properties" value="1" 
                                   <?php echo ($settings['auto_approve_properties'] ?? '0') == '1' ? 'checked' : ''; ?>
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="px-8 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 font-semibold">
                <i class="ri-save-line mr-2"></i>Save Settings
            </button>
        </form>
    </div>
</body>
</html>

