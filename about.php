<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- नेपाली नोट: About Us पेजको लागि हेडर -->
    <meta charset="UTF-8">
    <title>About Us | RoomFinder</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Fonts and Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: '#4A90E2', secondary: '#FF6B6B' },
                    borderRadius: {
                        'none': '0px', 'sm': '4px', DEFAULT: '8px', 'md': '12px', 'lg': '16px',
                        'xl': '20px', '2xl': '24px', '3xl': '32px', 'full': '9999px', 'button': '8px'
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header Start -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            <a href="index.php" class="text-2xl font-['Pacifico'] text-primary">RoomFinder</a>
            <div class="hidden md:flex items-center space-x-6">
                <a href="index.php" class="text-gray-700 hover:text-primary transition-colors">Home</a>
                <a href="find-rooms.php" class="text-gray-700 hover:text-primary transition-colors">Find Rooms</a>
                <?php if(isset($_SESSION["role"]) && $_SESSION["role"] === 'owner'): ?>
                <a href="list-property.php" class="text-gray-700 hover:text-primary transition-colors">List Property</a>
                <?php endif; ?>
                <a href="about.php" class="text-primary font-semibold transition-colors">About Us</a>
                <a href="contact.php" class="text-gray-700 hover:text-primary transition-colors">Contact</a>
                <?php if(isset($_SESSION["user_id"])): ?>
                <a href="messages.php" class="text-gray-700 hover:text-primary transition-colors relative">
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
                        echo '<span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold" style="min-width: 20px; min-height: 20px; line-height: 20px; transform: translate(50%, -50%);">' . $unread_count . '</span>';
                    }
                    ?>
                </a>
                <?php endif; ?>
            </div>
            <div class="flex items-center space-x-4">
                <div class="hidden md:flex items-center space-x-2">
                    <button class="flex items-center space-x-1 text-gray-700 hover:text-primary">
                        <span>EN</span>
                        <div class="w-4 h-4 flex items-center justify-center">
                            <i class="ri-arrow-down-s-line"></i>
                        </div>
                    </button>
                </div>
                <?php if(isset($_SESSION["user_id"])): ?>
                    <?php
                    $user_name = $_SESSION["name"] ?? "User";
                    $user_photo = $_SESSION["profile_photo"] ?? null;
                    $user_initial = strtoupper(substr($user_name, 0, 1));
                    ?>
                    <!-- User Profile Dropdown -->
                    <div class="relative">
                        <button onclick="toggleUserDropdown()" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="w-10 h-10 rounded-full <?php echo $user_photo ? '' : 'bg-green-500'; ?> flex items-center justify-center overflow-hidden">
                                <?php if ($user_photo): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($user_photo); ?>" alt="<?php echo htmlspecialchars($user_name); ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <span class="text-white font-bold text-lg"><?php echo $user_initial; ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="hidden md:block text-gray-700 font-semibold"><?php echo htmlspecialchars(strtoupper($user_name)); ?></span>
                            <i class="ri-arrow-down-s-line text-gray-600"></i>
                        </button>
                        <!-- Dropdown Menu -->
                        <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                            <a href="index.php" class="flex items-center space-x-3 px-4 py-2 hover:bg-gray-100 transition-colors">
                                <i class="ri-dashboard-line text-gray-600"></i>
                                <span class="text-gray-700">Dashboard</span>
                            </a>
                            <a href="user/profile.php" class="flex items-center space-x-3 px-4 py-2 hover:bg-gray-100 transition-colors">
                                <i class="ri-user-line text-gray-600"></i>
                                <span class="text-gray-700">Profile</span>
                            </a>
                            <hr class="my-2 border-gray-200">
                            <a href="user/logout.php" class="flex items-center space-x-3 px-4 py-2 hover:bg-red-50 transition-colors">
                                <i class="ri-logout-box-r-line text-red-500"></i>
                                <span class="text-red-500">Logout</span>
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="user/login.php" class="hidden md:block px-4 py-2 text-primary border border-primary rounded-button hover:bg-primary hover:text-white transition-colors whitespace-nowrap">Sign In</a>
                    <a href="user/createaccount.php" class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 transition-colors whitespace-nowrap">Sign Up</a>
                <?php endif; ?>
                <button class="md:hidden w-10 h-10 flex items-center justify-center text-gray-700">
                    <i class="ri-menu-line text-xl"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- About Us Section -->
    <section class="w-full py-16 bg-white">
        <div class="container mx-auto px-4 flex flex-col items-center">
            <h1 class="text-4xl md:text-5xl font-bold text-primary mb-4 text-center">About RoomFinder</h1>
            <p class="text-lg text-gray-700 max-w-2xl text-center mb-8">
                RoomFinder is your trusted platform to find the perfect room or tenant. Our mission is to make renting and letting rooms simple, safe, and efficient for everyone. Whether you are a student, a working professional, or a property owner, RoomFinder brings you the best tools and features to connect and succeed.
            </p>
            <img src="uploads/hemant.png" alt="About RoomFinder" class="rounded-lg shadow-lg mb-10 w-full max-w-3xl object-cover">
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">Why Choose RoomFinder?</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">We offer a complete set of features to make your room search or listing experience smooth and successful.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-sm flex flex-col items-center">
                    <div class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-full mb-4">
                        <i class="ri-map-pin-line text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Interactive Map Search</h3>
                    <p class="text-gray-600 text-center">Find rooms in your preferred location with our interactive map interface powered by Google Maps.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm flex flex-col items-center">
                    <div class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-full mb-4">
                        <i class="ri-message-3-line text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">In-App Messaging</h3>
                    <p class="text-gray-600 text-center">Communicate directly with property owners or tenants through our secure messaging system.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm flex flex-col items-center">
                    <div class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-full mb-4">
                        <i class="ri-ai-generate text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">AI Recommendations</h3>
                    <p class="text-gray-600 text-center">Get personalized room suggestions based on your preferences and previous searches.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm flex flex-col items-center">
                    <div class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-full mb-4">
                        <i class="ri-shield-check-line text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Verified Owners</h3>
                    <p class="text-gray-600 text-center">Look for the verification badge to ensure you're dealing with trusted property owners.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm flex flex-col items-center">
                    <div class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-full mb-4">
                        <i class="ri-translate-2 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Multilingual Support</h3>
                    <p class="text-gray-600 text-center">Use our platform in multiple languages including English, Nepali, and Japanese.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm flex flex-col items-center">
                    <div class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-full mb-4">
                        <i class="ri-calculator-line text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Rent Calculator</h3>
                    <p class="text-gray-600 text-center">Calculate total costs including utilities and additional fees for better financial planning.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-16 bg-primary">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Ready to Find or List a Room?</h2>
            <p class="text-white/80 max-w-2xl mx-auto mb-8">Join thousands of satisfied users who have found their ideal living situation through RoomFinder.</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="find-rooms.php" class="px-6 py-3 bg-white text-primary rounded-button hover:bg-gray-100 transition-colors whitespace-nowrap">Find a Room</a>
                <?php if(isset($_SESSION["role"]) && $_SESSION["role"] === 'owner'): ?>
                <a href="list-property.php" class="px-6 py-3 border border-white text-white rounded-button hover:bg-primary/90 transition-colors whitespace-nowrap">List Your Property</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-12 pb-6">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <a href="#" class="text-2xl font-['Pacifico'] text-white mb-4 inline-block">RoomFinder</a>
                    <p class="text-gray-400 mb-4">Find your perfect room or tenant with our easy-to-use platform.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 flex items-center justify-center bg-gray-800 rounded-full hover:bg-primary transition-colors">
                            <i class="ri-facebook-fill"></i>
                        </a>
                        <a href="#" class="w-10 h-10 flex items-center justify-center bg-gray-800 rounded-full hover:bg-primary transition-colors">
                            <i class="ri-twitter-x-fill"></i>
                        </a>
                        <a href="#" class="w-10 h-10 flex items-center justify-center bg-gray-800 rounded-full hover:bg-primary transition-colors">
                            <i class="ri-instagram-fill"></i>
                        </a>
                        <a href="#" class="w-10 h-10 flex items-center justify-center bg-gray-800 rounded-full hover:bg-primary transition-colors">
                            <i class="ri-linkedin-fill"></i>
                        </a>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-gray-400 hover:text-white transition-colors">Home</a></li>
                        <li><a href="find-rooms.php" class="text-gray-400 hover:text-white transition-colors">Find Rooms</a></li>
                        <?php if(isset($_SESSION["role"]) && $_SESSION["role"] === 'owner'): ?>
                        <li><a href="list-property.php" class="text-gray-400 hover:text-white transition-colors">List Property</a></li>
                        <?php endif; ?>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">How It Works</a></li>
                        <li><a href="about.php" class="text-gray-400 hover:text-white transition-colors">About Us</a></li>
                        <li><a href="contact.php" class="text-gray-400 hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Support</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Help Center</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Safety Center</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Community Guidelines</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Cookie Policy</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Subscribe</h3>
                    <p class="text-gray-400 mb-4">Stay updated with the latest properties and features.</p>
                    <form class="mb-4">
                        <div class="flex">
                            <input type="email" placeholder="Your email" class="px-4 py-2 w-full rounded-l-button border-none focus:outline-none text-gray-900">
                            <button type="submit" class="bg-primary text-white px-4 py-2 rounded-r-button hover:bg-primary/90 transition-colors whitespace-nowrap">Subscribe</button>
                        </div>
                    </form>
                    <div class="flex flex-wrap gap-2">
                        <div class="w-8 h-8 flex items-center justify-center">
                            <i class="ri-visa-fill text-2xl text-gray-400"></i>
                        </div>
                        <div class="w-8 h-8 flex items-center justify-center">
                            <i class="ri-mastercard-fill text-2xl text-gray-400"></i>
                        </div>
                        <div class="w-8 h-8 flex items-center justify-center">
                            <i class="ri-paypal-fill text-2xl text-gray-400"></i>
                        </div>
                        <div class="w-8 h-8 flex items-center justify-center">
                            <i class="ri-apple-fill text-2xl text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-6 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm mb-4 md:mb-0">© 2025 RoomFinder. All rights reserved.</p>
                <div class="flex space-x-4 items-center">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors text-sm">Terms</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors text-sm">Privacy</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors text-sm">Cookies</a>
                    <span class="text-gray-600">|</span>
                    <a href="admin/login.php" class="text-gray-500 hover:text-gray-300 transition-colors text-xs" title="Admin Login">
                        <i class="ri-admin-line"></i> Admin
                    </a>
                </div>
            </div>
        </div>
    </footer>
    <script src="main.js"></script>
    <script>
        // Toggle user dropdown menu
        function toggleUserDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('hidden');
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const button = event.target.closest('[onclick="toggleUserDropdown()"]');
            if (dropdown && !dropdown.contains(event.target) && !button) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>