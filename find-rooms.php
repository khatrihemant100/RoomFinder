<?php
session_start();
if (isset($_GET['api']) && $_GET['api'] == '1') {
    $conn = new mysqli("localhost", "root", "", "roomfinder");
    $location = isset($_GET['location']) ? trim($_GET['location']) : '';
    $rooms = [];
    if ($location !== '') {
        $stmt = $conn->prepare("SELECT * FROM rooms WHERE location LIKE ? ORDER BY created_at DESC");
        $like = '%' . $location . '%';
        $stmt->bind_param("s", $like);
        $stmt->execute();
        $res = $stmt->get_result();
        while($row = $res->fetch_assoc()) $rooms[] = $row;
        $stmt->close();
    }
    header('Content-Type: application/json');
    echo json_encode($rooms);
    exit;
}

// Fetch all rooms from the database
$conn = new mysqli("localhost", "root", "", "roomfinder");
$rooms = [];
$res = $conn->query("SELECT * FROM rooms ORDER BY created_at DESC");
while($row = $res->fetch_assoc()) $rooms[] = $row;
?>
<script>
  // Pass PHP rooms to JS
  const dbRooms = <?php echo json_encode($rooms); ?>;
  const currentUserId = <?php echo isset($_SESSION["user_id"]) ? intval($_SESSION["user_id"]) : 'null'; ?>;
</script>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Find Rooms - RoomFinder</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <script src="https://cdn.tailwindcss.com/3.4.16"></script>
  <style>
    body {
      background: #fff !important;
      font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #333;
      min-height: 100vh;
    }
    .container {
      max-width: 1200px;
      margin: 0 auto;
    }
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
    main {
      background: #fff;
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      margin-bottom: 30px;
    }
    .results {
      margin-top: 30px;
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 25px;
    }
    .card {
      background: white;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      display: flex;
      flex-direction: column;
    }
    .card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    }
    .image {
      height: 200px;
      background-size: cover;
      background-position: center;
      position: relative;
    }
    .rent-badge {
      position: absolute;
      top: 15px;
      right: 15px;
      background: linear-gradient(to right, #e74c3c, #c0392b);
      color: white;
      padding: 8px 15px;
      border-radius: 20px;
      font-weight: 600;
      font-size: 1.1rem;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }
    .card-content {
      padding: 20px;
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    .card h4 {
      color: #2c3e50;
      margin-bottom: 10px;
      font-size: 1.3rem;
    }
    .card p {
      color: #7f8c8d;
      margin: 8px 0;
      display: flex;
      align-items: center;
    }
    .card p i {
      margin-right: 10px;
      color: #3498db;
      width: 20px;
      text-align: center;
    }
    .details-btn {
      display: block;
      width: 100%;
      text-align: center;
      background: #3498db;
      margin-top: 15px;
      padding: 10px;
      font-size: 1rem;
      color: #fff;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.2s;
    }
    .details-btn:hover {
      background: #217dbb;
    }
    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 999;
      left: 0; top: 0;
      width: 100%; height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.6);
    }
    .modal-content {
      background-color: #fff;
      margin: 10% auto;
      padding: 30px;
      border-radius: 12px;
      width: 90%;
      max-width: 500px;
      position: relative;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
      animation: fadeIn 0.3s ease;
    }
    .close-btn {
      position: absolute;
      top: 15px; right: 20px;
      font-size: 28px;
      cursor: pointer;
      color: #aaa;
    }
    .close-btn:hover {
      color: #333;
    }
    .modal-content img {
      width: 100%;
      border-radius: 8px;
      margin-bottom: 15px;
    }
    @keyframes fadeIn {
      from {opacity: 0; transform: scale(0.95);}
      to {opacity: 1; transform: scale(1);}
    }
    @media (max-width: 900px) {
      .container { padding: 0 10px; }
      main { padding: 15px; }
    }
    @media (max-width: 600px) {
      .rf-header .container { flex-direction: column; gap: 10px; }
      .rf-header .nav { gap: 12px; }
      main { padding: 5px; }
    }
  </style>
</head>
<body>
  <!-- RoomFinder Navbar/Header -->
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

  <div class="container">
    <main>
      <h2 style="font-family:'Pacifico',cursive;color:#4A90E2;text-align:center;font-size:2rem;">Find the Perfect Room</h2>
      <form id="searchForm" style="max-width:600px;margin:0 auto 30px auto;">
        <div class="form-group">
          <label id="label-rent">Rent</label>
          <input type="number" placeholder="¥" id="rent" class="w-full border rounded px-3 py-2" />
        </div>
        <div class="form-group">
          <label id="label-location">Location</label>
          <input type="text" id="station" placeholder="e.g. Tokyo" class="w-full border rounded px-3 py-2" />
        </div>
        <div class="form-group">
          <label id="label-type">Room Type</label>
          <input type="text" id="roomType" placeholder="e.g. Single, Double, Flat, Hostel" class="w-full border rounded px-3 py-2" />
        </div>
        <button type="submit" id="search-btn" class="bg-primary text-white px-6 py-2 rounded mt-2"><i class="fas fa-search"></i> Search</button>
      </form>
      <h3 id="results-title" style="font-size:1.3rem;font-weight:600;margin-bottom:10px;">Search Results</h3>
      <div id="results" class="results"></div>
    </main>

    <!-- Room Detail Modal -->
    <div id="roomModal" class="modal">
      <div class="modal-content">
        <span id="modalClose" class="close-btn">&times;</span>
        <h3 id="modalTitle"></h3>
        <img id="modalImage" src="" alt="Room Image" style="width:100%; max-height:300px; object-fit:cover;" />
        <p><strong>Rent:</strong> <span id="modalRent"></span></p>
        <p><strong>Location:</strong> <span id="modalStation"></span></p>
        <p><strong>Type:</strong> <span id="modalType"></span></p>
        <p id="modalDescription"></p>
      </div>
    </div>
  </div>
  <script>
    // Use dbRooms from PHP
    const rooms = typeof dbRooms !== "undefined" ? dbRooms : [];

    // DOM Elements
    const searchForm = document.getElementById('searchForm');
    const resultsContainer = document.getElementById('results');

    // Display rooms in the UI
    function displayRooms(roomsArray) {
      resultsContainer.innerHTML = '';
      if (roomsArray.length === 0) {
        resultsContainer.innerHTML = `
          <div class="no-results">
            <h4>No rooms found</h4>
            <p>Please try different search criteria</p>
          </div>
        `;
        return;
      }
      roomsArray.forEach(room => {
        const isOwner = currentUserId && room.user_id == currentUserId;
        const card = document.createElement('div');
        card.className = 'card';
        card.innerHTML = `
          <div class="image">
            <img src="${room.image}" alt="Room Image" style="width:100%;height:200px;object-fit:cover;border-radius:8px 8px 0 0;">
            <div class="rent-badge">¥${room.price ? room.price.toLocaleString() : ''}</div>
          </div>
          <div class="card-content">
            <h4>${room.title || ''}</h4>
            <p><i class="fas fa-map-marker-alt"></i> ${room.location || ''}</p>
            <p><i class="fas fa-home"></i> ${room.type || ''}</p>
            <p>${room.description || ''}</p>
            <button class="details-btn" data-id="${room.id}">
              View Details <i class="fas fa-arrow-right"></i>
            </button>
            ${isOwner ? `
              <button class="edit-btn" data-id="${room.id}" style="margin-top:8px;background:#f1c40f;color:#222;">Edit</button>
              <button class="delete-btn" data-id="${room.id}" style="margin-top:8px;background:#e74c3c;color:#fff;">Delete</button>
            ` : ''}
          </div>
        `;
        resultsContainer.appendChild(card);
      });

      // Add event listeners to detail buttons
      document.querySelectorAll('.details-btn').forEach(button => {
        button.addEventListener('click', function() {
          const roomId = parseInt(this.getAttribute('data-id'));
          const room = rooms.find(r => r.id == roomId);
          if (room) {
            document.getElementById('modalTitle').textContent = room.title || '';
            document.getElementById('modalImage').src = room.image || '';
            document.getElementById('modalRent').textContent = room.price ? `¥${room.price.toLocaleString()}` : '';
            document.getElementById('modalStation').textContent = room.location || '';
            document.getElementById('modalType').textContent = room.type || '';
            document.getElementById('modalDescription').textContent = room.description || '';
            document.getElementById('roomModal').style.display = 'block';
          }
        });
      });

      // ==== यी दुई handler थप्नुहोस् ====
      document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
          const roomId = this.getAttribute('data-id');
          window.location.href = 'edit-room.php?id=' + roomId;
        });
      });
      document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
          const roomId = this.getAttribute('data-id');
          if (confirm('Are you sure you want to delete this room?')) {
            window.location.href = 'delete-room.php?id=' + roomId;
          }
        });
      });
    }

    // Handle search form submission
    function handleSearch(e) {
      e.preventDefault();
      // Show loading state
      resultsContainer.innerHTML = `
        <div class="loading">
          <div class="spinner"></div>
          <p>Searching for rooms...</p>
        </div>
      `;
      setTimeout(() => {
        const rent = parseInt(document.getElementById('rent').value) || Number.MAX_VALUE;
        const station = document.getElementById('station').value.trim().toLowerCase();
        const roomType = document.getElementById('roomType').value.trim().toLowerCase();
        const filteredRooms = rooms.filter(room => {
          return (room.price <= rent) &&
                 (station === '' || (room.location && room.location.toLowerCase().includes(station))) &&
                 (roomType === '' || (room.type && room.type.toLowerCase().includes(roomType)));
        });
        displayRooms(filteredRooms);
      }, 500);
    }

    // Modal close logic
    document.getElementById('modalClose').onclick = function() {
      document.getElementById('roomModal').style.display = 'none';
    };
    window.onclick = function(event) {
      if (event.target == document.getElementById('roomModal')) {
        document.getElementById('roomModal').style.display = 'none';
      }
    };

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
      displayRooms(rooms);
      searchForm.addEventListener('submit', handleSearch);
    });
  </script>
 
</body>
</html>
<?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
  <div style="background:#e0ffe0;color:#207520;padding:10px 20px;margin:20px 0;border-radius:8px;text-align:center;">
    Room successfully deleted!
  </div>
<?php endif; ?>
