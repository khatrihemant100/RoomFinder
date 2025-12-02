<!-- Sidebar -->
<div class="fixed inset-y-0 left-0 w-64 bg-gray-900 text-white">
    <div class="p-6">
        <h1 class="text-2xl font-['Pacifico'] mb-8">RoomFinder</h1>
        <nav class="space-y-2">
            <a href="dashboard.php" class="flex items-center space-x-3 px-4 py-3 <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-blue-600' : 'hover:bg-gray-800'; ?> rounded-lg">
                <i class="ri-dashboard-line"></i>
                <span>Dashboard</span>
            </a>
            <a href="users.php" class="flex items-center space-x-3 px-4 py-3 <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'bg-blue-600' : 'hover:bg-gray-800'; ?> rounded-lg">
                <i class="ri-user-line"></i>
                <span>Users</span>
            </a>
            <a href="properties.php" class="flex items-center space-x-3 px-4 py-3 <?php echo basename($_SERVER['PHP_SELF']) == 'properties.php' ? 'bg-blue-600' : 'hover:bg-gray-800'; ?> rounded-lg">
                <i class="ri-home-line"></i>
                <span>Properties</span>
                <?php
                $pending = $conn->query("SELECT COUNT(*) as total FROM properties WHERE is_approved = 0")->fetch_assoc()['total'];
                if ($pending > 0):
                ?>
                    <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full"><?php echo $pending; ?></span>
                <?php endif; ?>
            </a>
            <a href="messages.php" class="flex items-center space-x-3 px-4 py-3 <?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'bg-blue-600' : 'hover:bg-gray-800'; ?> rounded-lg">
                <i class="ri-message-3-line"></i>
                <span>Messages</span>
            </a>
            <a href="settings.php" class="flex items-center space-x-3 px-4 py-3 <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'bg-blue-600' : 'hover:bg-gray-800'; ?> rounded-lg">
                <i class="ri-settings-line"></i>
                <span>Settings</span>
            </a>
            <hr class="my-4 border-gray-700">
            <a href="../index.php" class="flex items-center space-x-3 px-4 py-3 hover:bg-gray-800 rounded-lg transition-colors">
                <i class="ri-home-4-line"></i>
                <span>View Website</span>
            </a>
            <a href="logout.php" class="flex items-center space-x-3 px-4 py-3 hover:bg-red-600 rounded-lg transition-colors">
                <i class="ri-logout-box-line"></i>
                <span>Logout</span>
            </a>
        </nav>
    </div>
</div>

