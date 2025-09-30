<?php
 session_start(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RoomFinder - Find Your Perfect Room</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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

<style>
  /* AI Chat Button & Chat Window */
  #ai-chat-btn {
    position: fixed;
    bottom: 32px;
    right: 32px;
    z-index: 9999;
    background: #4A90E2;
    color: #fff;
    border: 5px solid #4A90E2;
    border-radius: 50%;
    width: 64px;
    height: 64px;
    box-shadow: 0 4px 16px rgba(74,144,226,0.18);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.2s;
    overflow: visible;
  }
  #ai-chat-btn .ai-logo,
  #ai-chat-btn .ai-bot {
    position: relative;
    z-index: 1;
  }
  #ai-chat-window {
    position: fixed;
    bottom: 110px;
    right: 32px;
    width: 350px;
    max-width: 95vw;
    height: 480px;
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 8px 32px 0 rgba(74,144,226,0.18);
    z-index: 9999;
    display: none;
    flex-direction: column;
    overflow: hidden;
    border: 1.5px solid #4A90E2;
    animation: fadeIn 0.2s;
  }
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(30px);}
    to { opacity: 1; transform: translateY(0);}
  }
  #ai-chat-header {
    background: #4A90E2;
    color: #fff;
    padding: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  #ai-chat-header .ai-logo {
    font-size: 1.5rem;
    margin-right: 8px;
  }
  #ai-chat-header .ai-bot {
    font-size: 1.3rem;
    margin-left: 2px;
  }
  #ai-chat-header .close-btn {
    background: transparent;
    border: none;
    color: #fff;
    font-size: 1.5rem;
    cursor: pointer;
    margin-left: 8px;
  }
  #ai-chat-messages {
    flex: 1;
    padding: 16px;
    overflow-y: auto;
    background: #f8fafc;
    font-size: 1rem;
  }
  .ai-msg, .user-msg {
    margin-bottom: 12px;
    display: flex;
    align-items: flex-end;
  }
  .ai-msg .bubble {
    background: #eaf4fb;
    color: #222;
    border-radius: 12px 12px 12px 4px;
    padding: 10px 14px;
    max-width: 80%;
    font-size: 1rem;
    margin-right: auto;
    box-shadow: 0 2px 8px rgba(74,144,226,0.06);
  }
  .user-msg {
    justify-content: flex-end;
  }
  .user-msg .bubble {
    background: #4A90E2;
    color: #fff;
    border-radius: 12px 12px 4px 12px;
    padding: 10px 14px;
    max-width: 80%;
    font-size: 1rem;
    margin-left: auto;
    box-shadow: 0 2px 8px rgba(74,144,226,0.10);
  }
  #ai-chat-input-area {
    display: flex;
    border-top: 1px solid #e5e7eb;
    background: #fff;
    padding: 10px;
  }
  #ai-chat-input {
    flex: 1;
    border: none;
    border-radius: 8px;
    padding: 10px 12px;
    font-size: 1rem;
    background: #f3f4f6;
    margin-right: 8px;
    outline: none;
  }
  #ai-chat-send {
    background: #4A90E2;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 0 18px;
    font-size: 1.1rem;
    cursor: pointer;
    transition: background 0.2s;
  }
  #ai-chat-send:hover {
    background: #357ABD;
  }
</style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- AI Chat Button -->
<button id="ai-chat-btn" title="Chat with RoomFinder AI">
  <span class="neon-border"></span>
  <span class="ai-logo"><i class="ri-home-4-line"></i></span>
  <span class="ai-bot"><i class="ri-robot-2-line"></i></span>
</button>

<!-- AI Chat Window -->
<div id="ai-chat-window">
  <div id="ai-chat-header">
    <span>
      <span class="ai-logo"><i class="ri-home-4-line"></i></span>
      <span style="font-weight:bold;">RoomFinder AI</span>
      <span class="ai-bot"><i class="ri-robot-2-line"></i></span>
    </span>
    <button class="close-btn" id="ai-chat-close" title="Close">&times;</button>
  </div>
  <div id="ai-chat-messages">
    <div class="ai-msg">
      <div class="bubble">
        👋 Hi! I am RoomFinder AI.<br>
        Ask me anything about rooms, locations, prices, or how to use this site!
      </div>
    </div>
  </div>
  <form id="ai-chat-input-area" autocomplete="off">
    <input type="text" id="ai-chat-input" placeholder="Type your message..." required />
    <button type="submit" id="ai-chat-send"><i class="ri-send-plane-2-line"></i></button>
  </form>
</div>

    <!-- Header Start -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            <a href="index.php" class="text-2xl font-['Pacifico'] text-primary">RoomFinder</a>
            <div class="hidden md:flex items-center space-x-6">
                <a href="index.php" class="text-gray-700 hover:text-primary transition-colors">Home</a>
                <a href="find-rooms.php" class="text-gray-700 hover:text-primary transition-colors">Find Rooms</a>
                <a href="list-property.php" class="text-gray-700 hover:text-primary transition-colors">List Property</a>
                <a href="about.php" class="text-gray-700 hover:text-primary transition-colors">About Us</a>
                <a href="contact.php" class="text-gray-700 hover:text-primary transition-colors">Contact</a>
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
                    <span class="px-4 py-2 text-primary font-semibold rounded-button bg-primary/10">
                        <?php echo htmlspecialchars($_SESSION["name"]); ?>
                    </span>
                    <a href="user/logout.php" class="px-4 py-2 bg-secondary text-white rounded-button hover:bg-secondary/90 transition-colors whitespace-nowrap">Logout</a>
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

    <!-- नेपाली नोट: मुख्य हिरो सेसन (मुख्य शीर्षक र खोजी) -->
    <section class="hero-section w-full py-16 md:py-24">
        <div class="container mx-auto px-4">
            <div class="w-full max-w-xl">
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-4">Find Your Perfect Room</h1>
                <p class="text-lg text-gray-700 mb-8">Discover thousands of rooms and apartments for rent. Whether you're looking to list your property or find your next home, we've got you covered.</p>
                
                <div class="flex flex-col md:flex-row gap-4 mb-8">
                    <a href="#" class="px-6 py-3 bg-primary text-white rounded-button text-center hover:bg-primary/90 transition-colors whitespace-nowrap">Find a Room</a>
                    <a href="#" class="px-6 py-3 border border-primary text-primary rounded-button text-center hover:bg-gray-50 transition-colors whitespace-nowrap">List Your Property</a>
                </div>
                
                <div class="bg-white rounded-lg shadow-lg p-4">
                  <form id="location-search-form" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-grow">
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none w-10 h-10">
                          <i class="ri-map-pin-line text-gray-400"></i>
                        </div>
                        <input type="text" name="location" id="location-input" class="w-full pl-10 pr-4 py-3 border-none rounded bg-gray-50 custom-input" placeholder="Enter location" required>
                      </div>
                    </div>
                    <button type="submit" class="px-6 py-3 bg-primary text-white rounded-button hover:bg-primary/90 transition-colors whitespace-nowrap">Search</button>
                  </form>
                </div>
                
                <!-- Modal/Dialog for search results -->
                <div id="search-modal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;background:rgba(0,0,0,0.3);align-items:center;justify-content:center;">
                  <div style="background:#fff;max-width:600px;width:95vw;padding:24px 16px;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,0.18);position:relative;">
                    <button id="close-modal" style="position:absolute;top:12px;right:16px;font-size:1.5rem;background:none;border:none;cursor:pointer;">&times;</button>
                    <h3 style="font-size:1.3rem;font-weight:bold;margin-bottom:12px;">Search Results</h3>
                    <div id="modal-results"></div>
                  </div>
                </div>
            </div>
        </div>
    </section>

    <!-- नेपाली नोट: Role चयन सेसन (Owner/Seeker) -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">Choose Your Role</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Whether you're looking to rent out your property or find your next home, RoomFinder has the tools you need.</p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden transition-transform hover:transform hover:scale-105">
                    <div class="h-48 bg-gray-200" style="background-image: url('https://readdy.ai/api/search-image?query=A%20person%20holding%20keys%20to%20a%20new%20apartment%2C%20standing%20in%20a%20doorway%20with%20a%20welcoming%20smile.%20The%20scene%20shows%20a%20well-maintained%20property%20with%20good%20lighting%2C%20clean%20interiors%2C%20and%20a%20sense%20of%20ownership%20and%20pride.%20The%20image%20conveys%20the%20concept%20of%20property%20management%20and%20renting%20out%20spaces.&width=600&height=400&seq=2&orientation=landscape'); background-size: cover; background-position: center;"></div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2">Room Owner</h3>
                        <p class="text-gray-600 mb-4">List your property, manage bookings, and connect with potential tenants. Our platform makes property management simple and efficient.</p>
                        <ul class="mb-6 space-y-2">
                            <li class="flex items-start">
                                <div class="w-5 h-5 flex items-center justify-center text-primary mt-0.5">
                                    <i class="ri-check-line"></i>
                                </div>
                                <span class="ml-2">List multiple properties</span>
                            </li>
                            <li class="flex items-start">
                                <div class="w-5 h-5 flex items-center justify-center text-primary mt-0.5">
                                    <i class="ri-check-line"></i>
                                </div>
                                <span class="ml-2">Manage bookings and inquiries</span>
                            </li>
                            <li class="flex items-start">
                                <div class="w-5 h-5 flex items-center justify-center text-primary mt-0.5">
                                    <i class="ri-check-line"></i>
                                </div>
                                <span class="ml-2">Get verified owner badge</span>
                            </li>
                        </ul>
                        <a href="#" class="block w-full px-4 py-3 bg-primary text-white text-center rounded-button hover:bg-primary/90 transition-colors whitespace-nowrap">Register as Owner</a>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-lg overflow-hidden transition-transform hover:transform hover:scale-105">
                    <div class="h-48 bg-gray-200" style="background-image: url('https://readdy.ai/api/search-image?query=A%20person%20looking%20at%20apartment%20listings%20on%20a%20smartphone%20or%20tablet%2C%20sitting%20in%20a%20coffee%20shop%20or%20comfortable%20space.%20The%20individual%20appears%20focused%20and%20engaged%20in%20searching%20for%20accommodation.%20The%20scene%20conveys%20the%20modern%20approach%20to%20finding%20housing%20through%20digital%20platforms.&width=600&height=400&seq=3&orientation=landscape'); background-size: cover; background-position: center;"></div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2">Room Seeker</h3>
                        <p class="text-gray-600 mb-4">Find your perfect room with our advanced search tools. Filter by location, budget, amenities, and more to discover your ideal living space.</p>
                        <ul class="mb-6 space-y-2">
                            <li class="flex items-start">
                                <div class="w-5 h-5 flex items-center justify-center text-primary mt-0.5">
                                    <i class="ri-check-line"></i>
                                </div>
                                <span class="ml-2">Personalized search filters</span>
                            </li>
                            <li class="flex items-start">
                                <div class="w-5 h-5 flex items-center justify-center text-primary mt-0.5">
                                    <i class="ri-check-line"></i>
                                </div>
                                <span class="ml-2">Save favorite properties</span>
                            </li>
                            <li class="flex items-start">
                                <div class="w-5 h-5 flex items-center justify-center text-primary mt-0.5">
                                    <i class="ri-check-line"></i>
                                </div>
                                <span class="ml-2">Direct messaging with owners</span>
                            </li>
                        </ul>
                        <a href="#" class="block w-full px-4 py-3 bg-primary text-white text-center rounded-button hover:bg-primary/90 transition-colors whitespace-nowrap">Register as Seeker</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- नेपाली नोट: Features सेसन (मुख्य सुविधाहरू) -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">Powerful Features</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Our platform offers a range of tools to make finding or listing a room as simple as possible.</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-full mb-4">
                        <i class="ri-map-pin-line text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Interactive Map Search</h3>
                    <p class="text-gray-600">Find rooms in your preferred location with our interactive map interface powered by Google Maps.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-full mb-4">
                        <i class="ri-message-3-line text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">In-App Messaging</h3>
                    <p class="text-gray-600">Communicate directly with property owners or potential tenants through our secure messaging system.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-full mb-4">
                        <i class="ri-ai-generate text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">AI Recommendations</h3>
                    <p class="text-gray-600">Get personalized room suggestions based on your preferences and previous searches.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-full mb-4">
                        <i class="ri-shield-check-line text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Verified Owners</h3>
                    <p class="text-gray-600">Look for the verification badge to ensure you're dealing with trusted property owners.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-full mb-4">
                        <i class="ri-translate-2 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Multilingual Support</h3>
                    <p class="text-gray-600">Use our platform in multiple languages including English, Nepali, and Japanese.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-full mb-4">
                        <i class="ri-calculator-line text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Rent Calculator</h3>
                    <p class="text-gray-600">Calculate total costs including utilities and additional fees for better financial planning.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- नेपाली नोट: Featured Properties सेसन (प्रमुख कोठाहरू) -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold">Featured Properties</h2>
                <a href="#" class="text-primary hover:underline">View All</a>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Property Card 1 -->
                <div class="property-card bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="relative">
                        <img src="https://readdy.ai/api/search-image?query=A%20modern%20studio%20apartment%20with%20clean%20design%2C%20featuring%20a%20comfortable%20bed%2C%20small%20kitchen%20area%2C%20and%20living%20space.%20The%20room%20has%20good%20natural%20light%2C%20minimal%20but%20stylish%20furniture%2C%20and%20appears%20well-maintained%20and%20inviting.%20The%20space%20is%20efficiently%20organized%20to%20maximize%20the%20available%20area.&width=400&height=250&seq=4&orientation=landscape" alt="Studio Apartment" class="w-full h-48 object-cover object-top">
                        <div class="absolute top-3 left-3">
                            <span class="bg-primary text-white text-xs px-2 py-1 rounded">Featured</span>
                        </div>
                        <div class="absolute top-3 right-3">
                            <button class="w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-sm">
                                <i class="ri-heart-line text-gray-600"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-semibold">Modern Studio Apartment</h3>
                            <span class="text-primary font-bold">$750/mo</span>
                        </div>
                        <div class="flex items-center text-gray-500 text-sm mb-3">
                            <div class="w-4 h-4 flex items-center justify-center">
                                <i class="ri-map-pin-line"></i>
                            </div>
                            <span class="ml-1">Downtown, Seattle</span>
                        </div>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">Studio</span>
                            <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">Wi-Fi</span>
                            <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">AC</span>
                            <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">Furnished</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <img src="https://readdy.ai/api/search-image?query=Professional%20headshot%20of%20a%20middle-aged%20man%20with%20a%20friendly%20smile%2C%20wearing%20business%20casual%20attire.%20The%20image%20has%20a%20clean%2C%20neutral%20background%20and%20good%20lighting%20to%20highlight%20facial%20features%20clearly.%20The%20person%20appears%20approachable%20and%20trustworthy.&width=100&height=100&seq=5&orientation=squarish" alt="Owner" class="w-8 h-8 rounded-full object-cover">
                                <div class="ml-2 flex items-center">
                                    <span class="text-sm">Michael R.</span>
                                    <div class="w-4 h-4 flex items-center justify-center text-primary ml-1">
                                        <i class="ri-verified-badge-fill"></i>
                                    </div>
                                </div>
                            </div>
                            <a href="#" class="text-primary text-sm hover:underline">Details</a>
                        </div>
                    </div>
                </div>
                
                <!-- Property Card 2 -->
                <div class="property-card bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="relative">
                        <img src="https://readdy.ai/api/search-image?query=A%20spacious%20one-bedroom%20apartment%20with%20separate%20living%20room%20and%20bedroom%20areas.%20The%20apartment%20features%20modern%20furniture%2C%20good%20lighting%2C%20and%20a%20clean%20aesthetic.%20The%20space%20includes%20a%20visible%20kitchen%20area%2C%20comfortable%20seating%2C%20and%20appears%20well-maintained%20and%20inviting%20for%20potential%20renters.&width=400&height=250&seq=6&orientation=landscape" alt="1 Bedroom Apartment" class="w-full h-48 object-cover object-top">
                        <div class="absolute top-3 right-3">
                            <button class="w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-sm">
                                <i class="ri-heart-line text-gray-600"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-semibold">Spacious 1BHK Apartment</h3>
                            <span class="text-primary font-bold">$950/mo</span>
                        </div>
                        <div class="flex items-center text-gray-500 text-sm mb-3">
                            <div class="w-4 h-4 flex items-center justify-center">
                                <i class="ri-map-pin-line"></i>
                            </div>
                            <span class="ml-1">Capitol Hill, Seattle</span>
                        </div>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">1 Bedroom</span>
                            <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">Wi-Fi</span>
                            <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">Washing Machine</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <img src="https://readdy.ai/api/search-image?query=Professional%20headshot%20of%20a%20young%20woman%20with%20a%20confident%20smile%2C%20wearing%20business%20casual%20attire.%20The%20image%20has%20a%20clean%2C%20neutral%20background%20and%20good%20lighting%20to%20highlight%20facial%20features%20clearly.%20The%20person%20appears%20approachable%20and%20trustworthy.&width=100&height=100&seq=7&orientation=squarish" alt="Owner" class="w-8 h-8 rounded-full object-cover">
                                <div class="ml-2 flex items-center">
                                    <span class="text-sm">Sarah J.</span>
                                    <div class="w-4 h-4 flex items-center justify-center text-primary ml-1">
                                        <i class="ri-verified-badge-fill"></i>
                                    </div>
                                </div>
                            </div>
                            <a href="#" class="text-primary text-sm hover:underline">Details</a>
                        </div>
                    </div>
                </div>
                
                <!-- Property Card 3 -->
                <div class="property-card bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="relative">
                        <img src="https://readdy.ai/api/search-image?query=A%20shared%20room%20in%20a%20modern%20apartment%20with%20two%20beds%2C%20personal%20storage%20space%2C%20and%20common%20areas.%20The%20room%20appears%20clean%2C%20well-lit%2C%20and%20organized%20with%20enough%20space%20for%20two%20occupants.%20The%20design%20is%20functional%20yet%20comfortable%2C%20ideal%20for%20roommates%20or%20shared%20accommodation.&width=400&height=250&seq=8&orientation=landscape" alt="Shared Room" class="w-full h-48 object-cover object-top">
                        <div class="absolute top-3 right-3">
                            <button class="w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-sm">
                                <i class="ri-heart-line text-gray-600"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-semibold">Shared Room in Apartment</h3>
                            <span class="text-primary font-bold">$450/mo</span>
                        </div>
                        <div class="flex items-center text-gray-500 text-sm mb-3">
                            <div class="w-4 h-4 flex items-center justify-center">
                                <i class="ri-map-pin-line"></i>
                            </div>
                            <span class="ml-1">University District, Seattle</span>
                        </div>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">Shared</span>
                            <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">Wi-Fi</span>
                            <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">Student-friendly</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <img src="https://readdy.ai/api/search-image?query=Professional%20headshot%20of%20a%20young%20man%20in%20his%20twenties%20with%20a%20friendly%20expression%2C%20wearing%20casual%20attire.%20The%20image%20has%20a%20clean%2C%20neutral%20background%20and%20good%20lighting%20to%20highlight%20facial%20features%20clearly.%20The%20person%20appears%20approachable%20and%20relaxed.&width=100&height=100&seq=9&orientation=squarish" alt="Owner" class="w-8 h-8 rounded-full object-cover">
                                <span class="ml-2 text-sm">Jason T.</span>
                            </div>
                            <a href="#" class="text-primary text-sm hover:underline">Details</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 text-center">
                <a href="#" class="px-6 py-3 bg-primary text-white rounded-button hover:bg-primary/90 transition-colors inline-block whitespace-nowrap">Browse All Properties</a>
            </div>
        </div>
    </section>

    <!-- नेपाली नोट: How It Works सेसन (कसरी काम गर्छ) -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">How It Works</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Our platform makes finding or listing a room simple and efficient.</p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <!-- For Room Seekers -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-xl font-bold mb-4 text-center">For Room Seekers</h3>
                    
                    <div class="space-y-6">
                        <div class="flex">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-primary text-white rounded-full font-bold">1</div>
                            <div class="ml-4">
                                <h4 class="font-semibold">Create an account</h4>
                                <p class="text-gray-600 text-sm mt-1">Sign up and complete your profile with your preferences.</p>
                            </div>
                        </div>
                        
                        <div class="flex">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-primary text-white rounded-full font-bold">2</div>
                            <div class="ml-4">
                                <h4 class="font-semibold">Search for rooms</h4>
                                <p class="text-gray-600 text-sm mt-1">Use filters to find properties that match your requirements.</p>
                            </div>
                        </div>
                        
                        <div class="flex">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-primary text-white rounded-full font-bold">3</div>
                            <div class="ml-4">
                                <h4 class="font-semibold">Contact owners</h4>
                                <p class="text-gray-600 text-sm mt-1">Message property owners directly through our platform.</p>
                            </div>
                        </div>
                        
                        <div class="flex">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-primary text-white rounded-full font-bold">4</div>
                            <div class="ml-4">
                                <h4 class="font-semibold">Schedule viewings</h4>
                                <p class="text-gray-600 text-sm mt-1">Arrange to see the property and finalize your rental agreement.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- For Room Owners -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-xl font-bold mb-4 text-center">For Room Owners</h3>
                    
                    <div class="space-y-6">
                        <div class="flex">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-primary text-white rounded-full font-bold">1</div>
                            <div class="ml-4">
                                <h4 class="font-semibold">Create an account</h4>
                                <p class="text-gray-600 text-sm mt-1">Sign up as a property owner and verify your identity.</p>
                            </div>
                        </div>
                        
                        <div class="flex">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-primary text-white rounded-full font-bold">2</div>
                            <div class="ml-4">
                                <h4 class="font-semibold">List your property</h4>
                                <p class="text-gray-600 text-sm mt-1">Add details, photos, and set your rental terms.</p>
                            </div>
                        </div>
                        
                        <div class="flex">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-primary text-white rounded-full font-bold">3</div>
                            <div class="ml-4">
                                <h4 class="font-semibold">Manage inquiries</h4>
                                <p class="text-gray-600 text-sm mt-1">Respond to messages and arrange viewings with interested renters.</p>
                            </div>
                        </div>
                        
                        <div class="flex">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-primary text-white rounded-full font-bold">4</div>
                            <div class="ml-4">
                                <h4 class="font-semibold">Secure your tenant</h4>
                                <p class="text-gray-600 text-sm mt-1">Select your preferred tenant and finalize the rental agreement.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- नेपाली नोट: Map सेसन (नक्सा र फिल्टरहरू) -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">Find Rooms Near You</h2>
                <p class="text-gray-600 max-w-2xl mx-auto mb-4">Use our interactive map to discover available properties in your desired location.</p>
                
                <div class="flex justify-center gap-2">
                    <button class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 transition-colors whitespace-nowrap" id="show-map-btn">Show Map</button>
                    <button class="px-4 py-2 border border-primary text-primary rounded-button hover:bg-gray-50 transition-colors whitespace-nowrap" id="list-view-btn">List View</button>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="h-96 w-full" id="map-placeholder" style="background-image: url('https://public.readdy.ai/gen_page/map_placeholder_1280x720.png'); background-size: cover; background-position: center;"></div>
                
                <div class="p-4 bg-white border-t" id="room-list" style="display:none;">
                    <div class="flex flex-col gap-4">
                        <!-- Room items will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- नेपाली नोट: Testimonials सेसन (प्रयोगकर्ताहरूको प्रतिक्रिया) -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">What Our Users Say</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Hear from people who have found their perfect room or tenant through RoomFinder.</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex items-center mb-4">
                        <div class="text-primary">
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4">"I found my dream apartment in just two days using RoomFinder. The filters made it easy to narrow down exactly what I was looking for, and the messaging system made contacting the owner simple."</p>
                    <div class="flex items-center">
                        <img src="https://readdy.ai/api/search-image?query=Headshot%20of%20a%20young%20professional%20woman%20with%20shoulder-length%20hair%2C%20smiling%20naturally%20at%20the%20camera.%20The%20image%20has%20good%20lighting%2C%20a%20neutral%20background%2C%20and%20captures%20a%20genuine%2C%20approachable%20expression.&width=100&height=100&seq=10&orientation=squarish" alt="Testimonial" class="w-10 h-10 rounded-full object-cover">
                        <div class="ml-3">
                            <h4 class="font-semibold">Emily Watson</h4>
                            <p class="text-gray-500 text-sm">Room Seeker</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex items-center mb-4">
                        <div class="text-primary">
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4">"As a property owner, RoomFinder has made it incredibly easy to list my rooms and find reliable tenants. The verification system gives me peace of mind, and I've had great experiences with all my renters."</p>
                    <div class="flex items-center">
                        <img src="https://readdy.ai/api/search-image?query=Headshot%20of%20a%20middle-aged%20man%20with%20short%20hair%20and%20glasses%2C%20smiling%20confidently%20at%20the%20camera.%20The%20image%20has%20good%20lighting%2C%20a%20neutral%20background%2C%20and%20captures%20a%20professional%2C%20trustworthy%20expression.&width=100&height=100&seq=11&orientation=squarish" alt="Testimonial" class="w-10 h-10 rounded-full object-cover">
                        <div class="ml-3">
                            <h4 class="font-semibold">Robert Chen</h4>
                            <p class="text-gray-500 text-sm">Room Owner</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex items-center mb-4">
                        <div class="text-primary">
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-half-fill"></i>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4">"The AI recommendation system on RoomFinder is amazing! It suggested properties that perfectly matched what I was looking for, even before I had fully defined my search criteria. Found my roommate through here too!"</p>
                    <div class="flex items-center">
                        <img src="https://readdy.ai/api/search-image?query=Headshot%20of%20a%20young%20man%20in%20his%20twenties%20with%20a%20casual%20style%2C%20smiling%20naturally%20at%20the%20camera.%20The%20image%20has%20good%20lighting%2C%20a%20neutral%20background%2C%20and%20captures%20a%20friendly%2C%20approachable%20expression.&width=100&height=100&seq=12&orientation=squarish" alt="Testimonial" class="w-10 h-10 rounded-full object-cover">
                        <div class="ml-3">
                            <h4 class="font-semibold">David Patel</h4>
                            <p class="text-gray-500 text-sm">Room Seeker</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- नेपाली नोट: CTA सेसन (Call to Action) -->
    <section class="py-16 bg-primary">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Ready to Find Your Perfect Room?</h2>
            <p class="text-white/80 max-w-2xl mx-auto mb-8">Join thousands of satisfied users who have found their ideal living situation through RoomFinder.</p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="#" class="px-6 py-3 bg-white text-primary rounded-button hover:bg-gray-100 transition-colors whitespace-nowrap">Find a Room</a>
                <a href="#" class="px-6 py-3 border border-white text-white rounded-button hover:bg-primary/90 transition-colors whitespace-nowrap">List Your Property</a>
            </div>
        </div>
    </section>

    <!-- नेपाली नोट: Footer (पाद लेख) -->
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
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Home</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Find Rooms</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">List Property</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">How It Works</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">About Us</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Contact</a></li>
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
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors text-sm">Terms</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors text-sm">Privacy</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors text-sm">Cookies</a>
                </div>
            </div>
        </div>
    </footer>

<!-- नेपाली नोट: बाह्य JavaScript फाइल लिंक (main.js) -->
    <script src="main.js"></script>

<script>
// Location search modal (as before)
document.getElementById('location-search-form').onsubmit = async function(e) {
  e.preventDefault();
  const location = document.getElementById('location-input').value.trim();
  if (!location) return;
  const modal = document.getElementById('search-modal');
  const resultsDiv = document.getElementById('modal-results');
  resultsDiv.innerHTML = '<div style="text-align:center;padding:24px;">Searching...</div>';
  modal.style.display = 'flex';

  // Fetch rooms from API
  try {
    const res = await fetch('find-rooms.php?api=1&location=' + encodeURIComponent(location));
    const rooms = await res.json();
    if (rooms.length === 0) {
      resultsDiv.innerHTML = '<div style="text-align:center;padding:24px;">No rooms found for <b>' + location + '</b>.</div>';
    } else {
      resultsDiv.innerHTML = rooms.map(room => `
        <div style="border-bottom:1px solid #eee;padding:12px 0;display:flex;gap:12px;align-items:flex-start;">
          <img src="${room.image ? room.image : 'no-image.png'}" alt="Room Image" style="width:90px;height:70px;object-fit:cover;border-radius:8px;">
          <div style="flex:1;">
            <div style="font-weight:bold;font-size:1.1rem;">${room.title || ''}</div>
            <div style="color:#4A90E2;">${room.location || ''}</div>
            <div>Type: ${room.type || ''}</div>
            <div>Rent: ${room.price ? '¥' + room.price : ''}</div>
            <div style="margin-top:4px;">
              <a href="find-rooms.php?id=${room.id}" style="color:#4A90E2;text-decoration:underline;">View Details</a>
            </div>
          </div>
        </div>
      `).join('');
    }
  } catch (err) {
    resultsDiv.innerHTML = '<div style="color:red;">Error fetching rooms.</div>';
  }
};
document.getElementById('close-modal').onclick = function() {
  document.getElementById('search-modal').style.display = 'none';
};
document.getElementById('search-modal').onclick = function(e) {
  if (e.target === this) this.style.display = 'none';
};

// RoomFinder AI Chat (Gemini API)
const GEMINI_API_KEY = "AIzaSyAYoOAIrd7-WYQZzdYbsAjAatGEkKyB6oA";
async function askGeminiAI(userMessage) {
  if (!userMessage) return "Please enter a message.";
  // Use v1 endpoint, not v1beta
  const url = "https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=" + GEMINI_API_KEY;
  const body = {
    contents: [
      {
        role: "user",
        parts: [{ text: userMessage }]
      }
    ]
  };
  console.log("Sending to Gemini:", JSON.stringify(body));
  const res = await fetch(url, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(body)
  });
  const data = await res.json();
  console.log("Gemini response:", data);
  if (data.candidates && data.candidates[0] && data.candidates[0].content && data.candidates[0].content.parts && data.candidates[0].content.parts[0].text) {
    return data.candidates[0].content.parts[0].text;
  } else if (data.error && data.error.message) {
    return "AI Error: " + data.error.message;
  } else {
    return "Sorry, I couldn't understand.";
  }
}

// Show/hide chat window
const chatBtn = document.getElementById('ai-chat-btn');
const chatWin = document.getElementById('ai-chat-window');
const chatClose = document.getElementById('ai-chat-close');
chatBtn.onclick = () => chatWin.style.display = 'flex';
chatClose.onclick = () => chatWin.style.display = 'none';

// Chat logic (Gemini live)
const chatForm = document.getElementById('ai-chat-input-area');
const chatInput = document.getElementById('ai-chat-input');
const chatMessages = document.getElementById('ai-chat-messages');

chatForm.onsubmit = async function(e) {
  e.preventDefault();
  const msg = chatInput.value.trim();
  if (!msg) return;
  // Show user message
  const userDiv = document.createElement('div');
  userDiv.className = 'user-msg';
  userDiv.innerHTML = `<div class="bubble">${msg}</div>`;
  chatMessages.appendChild(userDiv);
  chatMessages.scrollTop = chatMessages.scrollHeight;
  chatInput.value = '';
  chatInput.disabled = true;

  // Show loading
  const aiDiv = document.createElement('div');
  aiDiv.className = 'ai-msg';
  aiDiv.innerHTML = `<div class="bubble">Thinking...</div>`;
  chatMessages.appendChild(aiDiv);
  chatMessages.scrollTop = chatMessages.scrollHeight;

  // Get Gemini AI response
  let aiReply;
  try {
    aiReply = await askGeminiAI(msg);
  } catch (err) {
    aiReply = "Sorry, AI service is not available right now.";
  }
  aiDiv.innerHTML = `<div class="bubble">${aiReply}</div>`;
  chatMessages.scrollTop = chatMessages.scrollHeight;
  chatInput.disabled = false;
  chatInput.focus();
};
</script>
</body>
</html>