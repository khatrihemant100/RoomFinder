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
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.8s ease-in-out',
                        'slide-up': 'slideUp 0.6s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
    </style>
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

    <!-- Hero Section -->
    <section class="relative w-full py-20 bg-gradient-to-br from-primary via-blue-500 to-purple-600 text-white overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
        </div>
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <div class="mb-8">
                    <div class="inline-block mb-6">
                        <div class="w-24 h-24 mx-auto bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center mb-6 shadow-2xl">
                            <i class="ri-home-heart-line text-5xl text-white"></i>
                        </div>
                    </div>
                    <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-tight animate-fade-in">
                        About <span class="font-['Pacifico'] text-yellow-300">RoomFinder</span>
                    </h1>
                    <p class="text-xl md:text-2xl text-white/95 mb-10 leading-relaxed max-w-3xl mx-auto">
                        Your trusted platform to find the perfect room or tenant. Making renting and letting rooms simple, safe, and efficient for everyone.
                    </p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 max-w-3xl mx-auto">
                    <div class="bg-white/20 backdrop-blur-md px-6 py-5 rounded-2xl border border-white/30 hover:bg-white/30 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <div class="text-4xl font-bold mb-2">2025</div>
                        <div class="text-sm text-white/90 font-medium">Established</div>
                    </div>
                    <div class="bg-white/20 backdrop-blur-md px-6 py-5 rounded-2xl border border-white/30 hover:bg-white/30 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <div class="text-4xl font-bold mb-2">100%</div>
                        <div class="text-sm text-white/90 font-medium">User Focused</div>
                    </div>
                    <div class="bg-white/20 backdrop-blur-md px-6 py-5 rounded-2xl border border-white/30 hover:bg-white/30 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <div class="text-4xl font-bold mb-2">4</div>
                        <div class="text-sm text-white/90 font-medium">Team Members</div>
                    </div>
                    <div class="bg-white/20 backdrop-blur-md px-6 py-5 rounded-2xl border border-white/30 hover:bg-white/30 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <div class="text-4xl font-bold mb-2">24/7</div>
                        <div class="text-sm text-white/90 font-medium">Available</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <div class="inline-block mb-6">
                        <div class="w-20 h-20 mx-auto bg-gradient-to-br from-primary to-purple-600 rounded-full flex items-center justify-center shadow-xl float-animation">
                            <i class="ri-team-line text-3xl text-white"></i>
                        </div>
                    </div>
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Meet Our Team</h2>
                    <div class="w-32 h-1.5 bg-gradient-to-r from-primary via-purple-500 to-primary mx-auto rounded-full mb-6"></div>
                    <p class="text-lg md:text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        The talented developers behind RoomFinder, working together to create the best room finding experience. We're learning and growing together!
                    </p>
                </div>
                
                <div class="grid md:grid-cols-2 gap-8">
                <!-- Team Member 1: Hemant Khatri -->
<div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-blue-100">
    <div class="flex flex-col items-center text-center">

        <!-- Profile Photo -->
        <div class="w-32 h-32 rounded-full overflow-hidden shadow-lg mb-6 ring-4 ring-blue-200">
            <img src="uploads/hemant.png" class="w-full h-full object-cover" alt="Hemant Photo">
        </div>

        <h3 class="text-2xl font-bold text-gray-800 mb-2">Hemant Khatri</h3>
        <p class="text-lg text-blue-600 mb-4 font-semibold">Web Page Design</p>

        <p class="text-gray-600 leading-relaxed mb-6">
            Responsible for designing and developing web pages using HTML, JavaScript, and PHP. Working on creating user-friendly interfaces and implementing frontend functionality.
        </p>

        <div class="flex flex-wrap gap-2 justify-center">
            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">HTML</span>
            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">JavaScript</span>
            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">PHP</span>
            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">Web Design</span>
        </div>

    </div>
</div>


                    <!-- Team Member 2: Aakash Shrestha -->
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-green-100">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-32 h-32 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white text-4xl font-bold shadow-lg mb-6 ring-4 ring-green-200">
                                AS
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 mb-2">Aakash Shrestha</h3>
                            <p class="text-lg text-green-600 mb-4 font-semibold">Login Form & Room List Page</p>
                            <p class="text-gray-600 leading-relaxed mb-6">
                                Working on developing the login form, room list page, and CSS styling. Focused on creating clean and functional user interfaces for authentication and room browsing.
                            </p>
                            <div class="flex flex-wrap gap-2 justify-center">
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Login Form</span>
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Room List</span>
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">CSS</span>
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Frontend</span>
                            </div>
                        </div>
                    </div>

                    <!-- Team Member 3: Kan Tu -->
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-purple-100">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-32 h-32 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center text-white text-4xl font-bold shadow-lg mb-6 ring-4 ring-purple-200">
                                KT
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 mb-2">KHANT THU</h3>
                            <p class="text-lg text-purple-600 mb-4 font-semibold">Database Integration</p>
                            <p class="text-gray-600 leading-relaxed mb-6">
                                Responsible for database integration using MySQL and PHP. Working on connecting the application with the database, managing data storage, and ensuring smooth data flow.
                            </p>
                            <div class="flex flex-wrap gap-2 justify-center">
                                <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-semibold">MySQL</span>
                                <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-semibold">PHP</span>
                                <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-semibold">Database</span>
                                <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-semibold">Backend</span>
                            </div>
                        </div>
                    </div>

                    <!-- Team Member 4: Prabin Katwal -->
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-orange-100">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-32 h-32 rounded-full bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center text-white text-4xl font-bold shadow-lg mb-6 ring-4 ring-orange-200">
                                PK
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 mb-2">Prabin Katwal</h3>
                            <p class="text-lg text-orange-600 mb-4 font-semibold">UI/UX Design & PHP</p>
                            <p class="text-gray-600 leading-relaxed mb-6">
                                Working on UI/UX design confirmation using Canva and PHP development. Focused on ensuring the design meets user experience standards and implementing design elements in the application.
                            </p>
                            <div class="flex flex-wrap gap-2 justify-center">
                                <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-semibold">UI/UX</span>
                                <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-semibold">Canva</span>
                                <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-semibold">PHP</span>
                                <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-semibold">Design</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Team Collaboration Note -->
                <div class="mt-16 bg-gradient-to-br from-primary/10 via-purple-50 to-blue-50 rounded-3xl p-10 md:p-12 text-center border-2 border-primary/20 shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 w-64 h-64 bg-purple-500/5 rounded-full blur-3xl"></div>
                    <div class="relative z-10">
                        <div class="inline-block mb-6">
                            <div class="w-16 h-16 mx-auto bg-gradient-to-br from-primary to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                                <i class="ri-lightbulb-flash-line text-2xl text-white"></i>
                            </div>
                        </div>
                        <h3 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">Learning Together</h3>
                        <div class="w-24 h-1 bg-gradient-to-r from-primary to-purple-500 mx-auto rounded-full mb-6"></div>
                        <p class="text-lg md:text-xl text-gray-700 leading-relaxed max-w-4xl mx-auto mb-8">
                            RoomFinder is a collaborative project developed by our team during our learning phase. Each team member contributed their skills in web design, frontend development, database integration, and UI/UX design. Through this project, we're learning and growing together, applying our knowledge to create a functional room finding platform. This project represents our journey in web development and our commitment to continuous learning.
                        </p>
                        <div class="flex flex-wrap justify-center gap-4 mt-8">
                            <div class="px-6 py-3 bg-white/80 backdrop-blur-sm rounded-full shadow-lg hover:shadow-xl transition-all">
                                <i class="ri-code-s-slash-line text-primary text-xl mr-2"></i>
                                <span class="text-gray-700 font-semibold">Web Development</span>
                            </div>
                            <div class="px-6 py-3 bg-white/80 backdrop-blur-sm rounded-full shadow-lg hover:shadow-xl transition-all">
                                <i class="ri-team-line text-primary text-xl mr-2"></i>
                                <span class="text-gray-700 font-semibold">Team Collaboration</span>
                            </div>
                            <div class="px-6 py-3 bg-white/80 backdrop-blur-sm rounded-full shadow-lg hover:shadow-xl transition-all">
                                <i class="ri-book-open-line text-primary text-xl mr-2"></i>
                                <span class="text-gray-700 font-semibold">Continuous Learning</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Project Idea Section -->
    <section class="py-16 bg-gradient-to-br from-gray-50 to-blue-50">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-bold text-gray-800 mb-4">The Idea Behind RoomFinder</h2>
                    <div class="w-24 h-1 bg-primary mx-auto rounded-full mb-4"></div>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Understanding the challenges faced by both property owners and seekers in the rental market
                    </p>
                </div>
                <div class="grid md:grid-cols-2 gap-8">
                    <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-6">
                            <i class="ri-lightbulb-line text-3xl text-red-500"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">The Problem</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Finding the right room or tenant is often a time-consuming and frustrating process. Traditional methods lack transparency, real-time communication, and user-friendly interfaces. Property owners struggle to reach the right audience, while seekers waste time browsing through irrelevant listings.
                        </p>
                    </div>
                    <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-6">
                            <i class="ri-rocket-line text-3xl text-green-500"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">The Solution</h3>
                        <p class="text-gray-600 leading-relaxed">
                            RoomFinder was created to revolutionize the rental market by providing a comprehensive, user-friendly platform that connects property owners and seekers efficiently. With features like AI-powered recommendations, interactive maps, in-app messaging, and multilingual support, we make the entire process seamless and enjoyable.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- System Architecture Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-bold text-gray-800 mb-4">System Architecture</h2>
                    <div class="w-24 h-1 bg-primary mx-auto rounded-full mb-4"></div>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Built with modern technologies and best practices for scalability, security, and performance
                    </p>
                </div>
                
                <!-- Architecture Diagram -->
                <div class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-2xl p-8 md:p-12 mb-12 shadow-xl">
                    <div class="grid md:grid-cols-3 gap-8">
                        <!-- Frontend -->
                        <div class="bg-white p-6 rounded-xl shadow-lg">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="ri-computer-line text-2xl text-blue-600"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800">Frontend</h3>
                            </div>
                            <ul class="space-y-2 text-gray-600">
                                <li class="flex items-center gap-2">
                                    <i class="ri-checkbox-circle-fill text-green-500"></i>
                                    <span>HTML5 & CSS3</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="ri-checkbox-circle-fill text-green-500"></i>
                                    <span>Tailwind CSS</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="ri-checkbox-circle-fill text-green-500"></i>
                                    <span>JavaScript (Vanilla)</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="ri-checkbox-circle-fill text-green-500"></i>
                                    <span>RemixIcon</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="ri-checkbox-circle-fill text-green-500"></i>
                                    <span>Google Maps API</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Backend -->
                        <div class="bg-white p-6 rounded-xl shadow-lg">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="ri-server-line text-2xl text-purple-600"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800">Backend</h3>
                            </div>
                            <ul class="space-y-2 text-gray-600">
                                <li class="flex items-center gap-2">
                                    <i class="ri-checkbox-circle-fill text-green-500"></i>
                                    <span>PHP 7.4+</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="ri-checkbox-circle-fill text-green-500"></i>
                                    <span>MySQL Database</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="ri-checkbox-circle-fill text-green-500"></i>
                                    <span>Session Management</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="ri-checkbox-circle-fill text-green-500"></i>
                                    <span>RESTful API</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="ri-checkbox-circle-fill text-green-500"></i>
                                    <span>PHPMailer</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Additional Features -->
                        <div class="bg-white p-6 rounded-xl shadow-lg">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="ri-stack-line text-2xl text-green-600"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800">Features</h3>
                            </div>
                            <ul class="space-y-2 text-gray-600">
                                <li class="flex items-center gap-2">
                                    <i class="ri-checkbox-circle-fill text-green-500"></i>
                                    <span>AI Chat (Gemini API)</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="ri-checkbox-circle-fill text-green-500"></i>
                                    <span>Multi-language Support</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="ri-checkbox-circle-fill text-green-500"></i>
                                    <span>Email Notifications</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="ri-checkbox-circle-fill text-green-500"></i>
                                    <span>Admin Panel</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="ri-checkbox-circle-fill text-green-500"></i>
                                    <span>Python Scraper</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Database Structure -->
                <div class="bg-gradient-to-br from-primary/5 to-purple-50 rounded-2xl p-8 md:p-12 shadow-xl">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">Database Structure</h3>
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="bg-white p-4 rounded-lg shadow">
                            <div class="font-semibold text-primary mb-2">Users</div>
                            <div class="text-sm text-gray-600">Authentication, profiles, roles</div>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow">
                            <div class="font-semibold text-primary mb-2">Properties</div>
                            <div class="text-sm text-gray-600">Room listings, details, images</div>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow">
                            <div class="font-semibold text-primary mb-2">Messages</div>
                            <div class="text-sm text-gray-600">In-app messaging system</div>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow">
                            <div class="font-semibold text-primary mb-2">Inquiries</div>
                            <div class="text-sm text-gray-600">Property inquiries & requests</div>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow">
                            <div class="font-semibold text-primary mb-2">Admin Logs</div>
                            <div class="text-sm text-gray-600">Activity tracking & audit</div>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow">
                            <div class="font-semibold text-primary mb-2">Settings</div>
                            <div class="text-sm text-gray-600">System configuration</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-gradient-to-br from-gray-50 via-white to-blue-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Why Choose RoomFinder?</h2>
                <div class="w-24 h-1 bg-primary mx-auto rounded-full mb-4"></div>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">We offer a complete set of features to make your room search or listing experience smooth and successful.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col items-center border border-gray-100">
                    <div class="w-16 h-16 flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-2xl mb-6 shadow-lg">
                        <i class="ri-map-pin-line text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-800">Interactive Map Search</h3>
                    <p class="text-gray-600 text-center leading-relaxed">Find rooms in your preferred location with our interactive map interface powered by Google Maps. Visualize properties and their surroundings instantly.</p>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col items-center border border-gray-100">
                    <div class="w-16 h-16 flex items-center justify-center bg-gradient-to-br from-green-500 to-green-600 text-white rounded-2xl mb-6 shadow-lg">
                        <i class="ri-message-3-line text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-800">In-App Messaging</h3>
                    <p class="text-gray-600 text-center leading-relaxed">Communicate directly with property owners or tenants through our secure, real-time messaging system. No need to share personal contact information.</p>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col items-center border border-gray-100">
                    <div class="w-16 h-16 flex items-center justify-center bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-2xl mb-6 shadow-lg">
                        <i class="ri-ai-generate text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-800">AI Recommendations</h3>
                    <p class="text-gray-600 text-center leading-relaxed">Get personalized room suggestions powered by Google Gemini AI based on your preferences, budget, and previous searches.</p>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col items-center border border-gray-100">
                    <div class="w-16 h-16 flex items-center justify-center bg-gradient-to-br from-yellow-500 to-orange-500 text-white rounded-2xl mb-6 shadow-lg">
                        <i class="ri-shield-check-line text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-800">Verified Owners</h3>
                    <p class="text-gray-600 text-center leading-relaxed">Look for the verification badge to ensure you're dealing with trusted property owners. Enhanced security and peace of mind.</p>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col items-center border border-gray-100">
                    <div class="w-16 h-16 flex items-center justify-center bg-gradient-to-br from-pink-500 to-red-500 text-white rounded-2xl mb-6 shadow-lg">
                        <i class="ri-translate-2 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-800">Multilingual Support</h3>
                    <p class="text-gray-600 text-center leading-relaxed">Use our platform in multiple languages including English, Nepali, Japanese, and Myanmar. Breaking language barriers for global users.</p>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col items-center border border-gray-100">
                    <div class="w-16 h-16 flex items-center justify-center bg-gradient-to-br from-indigo-500 to-indigo-600 text-white rounded-2xl mb-6 shadow-lg">
                        <i class="ri-calculator-line text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-800">Rent Calculator</h3>
                    <p class="text-gray-600 text-center leading-relaxed">Calculate total costs including utilities, management fees, deposit, and key money for better financial planning and transparency.</p>
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