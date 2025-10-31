<?php
session_start();
$conn = new mysqli("localhost", "root", "", "roomfinder");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// API fetch for search (optional)
if (isset($_GET['api']) && $_GET['api'] == '1') {
    $location = isset($_GET['location']) ? trim($_GET['location']) : '';
    $rooms = [];
    if ($location !== '') {
        $stmt = $conn->prepare("SELECT * FROM properties WHERE location LIKE ? ORDER BY created_at DESC");
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

// Fetch all rooms
$rooms = [];
$res = $conn->query("SELECT * FROM properties ORDER BY created_at DESC");
while($row = $res->fetch_assoc()) $rooms[] = $row;
?>
<script>
  const dbRooms = <?php echo json_encode($rooms); ?>;
  const currentUserId = <?php echo isset($_SESSION["user_id"]) ? intval($_SESSION["user_id"]) : 'null'; ?>;
</script>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Find Rooms - RoomFinder</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<script src="https://cdn.tailwindcss.com/3.4.16"></script>
<style>
body { background: #fff !important; font-family: 'Inter', sans-serif; color: #333; min-height:100vh; }
.container { max-width:1200px; margin:0 auto; }
.rf-header { background:#fff; box-shadow:0 2px 8px rgba(74,144,226,0.08); padding:18px 0; margin-bottom:30px; }
.rf-header .container { display:flex; align-items:center; justify-content:space-between; }
.rf-header a.logo { font-family:'Pacifico',cursive; font-size:2rem; color:#4A90E2; text-decoration:none; }
.rf-header .nav { display:flex; gap:28px; }
.rf-header .nav a { color:#333; font-weight:500; text-decoration:none; transition:color 0.2s; }
.rf-header .nav a:hover { color:#4A90E2; }
.rf-header .user-area { display:flex; align-items:center; gap:12px; }
.rf-header .user-area span { color:#4A90E2; font-weight:600; background:#eaf4fb; padding:6px 16px; border-radius:8px; }
.rf-header .user-area a { background:#FF6B6B; color:#fff; padding:8px 18px; border-radius:8px; font-weight:500; text-decoration:none; transition:background 0.2s; }
.rf-header .user-area a:hover { background:#e74c3c; }
main { background:#fff; border-radius:12px; padding:30px; box-shadow:0 10px 30px rgba(0,0,0,0.08); margin-bottom:30px; }
.results { margin-top:30px; display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:25px; }
.card { background:white; border-radius:12px; overflow:hidden; box-shadow:0 5px 15px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; display:flex; flex-direction:column; }
.card:hover { transform:translateY(-10px); box-shadow:0 15px 30px rgba(0,0,0,0.15); }
.image { height:200px; background-size:cover; background-position:center; position:relative; }
.rent-badge { position:absolute; top:15px; right:15px; background:linear-gradient(to right,#e74c3c,#c0392b); color:white; padding:8px 15px; border-radius:20px; font-weight:600; font-size:1.1rem; box-shadow:0 4px 10px rgba(0,0,0,0.2); }

/* ==== Room Status Badge ==== */
.status-badge {
  position:absolute;
  top:15px;
  left:15px;
  padding:6px 12px;
  border-radius:20px;
  color:white;
  font-weight:600;
  font-size:0.9rem;
  box-shadow:0 3px 6px rgba(0,0,0,0.2);
}
.status-Available { background:#2ecc71; }      /* Green */
.status-Not_available { background:#e74c3c; }  /* Red */
.status-Under_Maintenance { background:#f39c12; }    /* Orange */
.status-Reserved { background:#3498db; }       /* Blue */
.status- { background:#7f8c8d; }        /* Grey */

.card-content { padding:20px; flex:1; display:flex; flex-direction:column; }
.card h4 { color:#2c3e50; margin-bottom:10px; font-size:1.3rem; }
.card p { color:#7f8c8d; margin:8px 0; display:flex; align-items:center; }
.card p i { margin-right:10px; color:#3498db; width:20px; text-align:center; }
.details-btn { display:block; width:100%; text-align:center; background:#3498db; margin-top:15px; padding:10px; font-size:1rem; color:#fff; border:none; border-radius:8px; cursor:pointer; transition:background 0.2s; }
.details-btn:hover { background:#217dbb; }

/* Modal Styles */
.modal { display:none; position:fixed; z-index:999; left:0; top:0; width:100%; height:100%; overflow:auto; background-color:rgba(0,0,0,0.6); }
.modal-content { background-color:#fff; margin:10% auto; padding:30px; border-radius:12px; width:90%; max-width:500px; position:relative; box-shadow:0 8px 30px rgba(0,0,0,0.2); animation:fadeIn 0.3s ease; }
.close-btn { position:absolute; top:15px; right:20px; font-size:28px; cursor:pointer; color:#aaa; }
.close-btn:hover { color:#333; }
.modal-content img { width:100%; border-radius:8px; margin-bottom:15px; }
@keyframes fadeIn { from {opacity:0; transform:scale(0.95);} to {opacity:1; transform:scale(1);} }
/* Inquiry Modal Enhancement */
#inquiryModal .modal-content {
  background: linear-gradient(135deg, #6a11cb, #2575fc);
  color: #fff;
  border: 2px solid #fff;
}

#inquiryModal h3 {
  text-align: center;
  font-size: 1.8rem;
  margin-bottom: 20px;
  font-family: 'times new roman', cursive;
  text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
}

#inquiryModal input {
  background: rgba(255,255,255,0.9);
  border: none;
  color: #333;
  transition: transform 0.2s, box-shadow 0.2s;
}

#inquiryModal input:focus {
  transform: scale(1.02);
  box-shadow: 0 4px 15px rgba(0,0,0,0.3);
  outline: none;
}

#inquiryModal button {
  background: linear-gradient(to right, #ff416c, #ff4b2b);
  font-weight: 600;
  font-size: 1rem;
  box-shadow: 0 5px 15px rgba(0,0,0,0.3);
  transition: all 0.3s ease;
}

#inquiryModal button:hover {
  transform: scale(1.05);
  box-shadow: 0 8px 20px rgba(0,0,0,0.4);
  background: linear-gradient(to right, #ff4b2b, #ff416c);
}


</style>
</head>
<body>
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
<h2 style="font-family:'Pacifico',cursive;color:#4A90E2;text-align:center;font-size:2rem;margin-bottom:20px;">Find the Perfect Room</h2>

<form id="searchForm" class="bg-white shadow-lg rounded-xl p-6 mb-10 border border-gray-100 max-w-2xl mx-auto">
  <div class="grid md:grid-cols-3 gap-6">
    <div class="form-group">
      <label class="block mb-2 font-medium text-gray-600"><i class="fas fa-yen-sign text-blue-500 mr-1"></i> Rent</label>
      <input type="number" placeholder="¥" id="rent" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 outline-none" />
    </div>
    <div class="form-group">
      <label class="block mb-2 font-medium text-gray-600"><i class="fas fa-map-marker-alt text-red-500 mr-1"></i> Location</label>
      <input type="text" id="station" placeholder="e.g. Tokyo" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 outline-none" />
    </div>
    <div class="form-group">
      <label class="block mb-2 font-medium text-gray-600"><i class="fas fa-home text-green-500 mr-1"></i> Room Type</label>
      <input type="text" id="roomType" placeholder="e.g. Single, Flat" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 outline-none" />
    </div>
  </div>
  <div class="text-center mt-6">
    <button type="submit" id="search-btn" class="bg-blue-500 hover:bg-blue-600 text-white px-8 py-2.5 rounded-lg shadow font-medium transition"><i class="fas fa-search"></i> Search</button>
  </div>
</form>

<h3 id="results-title" class="text-xl font-semibold mb-4">Search Results</h3>
<div id="results" class="results"></div>
</main>

<!-- Inquiry Modal -->
<div id="inquiryModal" class="modal">
  <div class="modal-content">
    <span id="inquiryModalClose" class="close-btn">&times;</span>
    <h3>Room Inquiry</h3>
    <form id="inquiryForm">
      <input type="hidden" id="inquiryRoomId" name="room_id" />
      <div style="margin-bottom:12px;"><label><strong>Name:</strong></label><input type="text" id="inqName" name="name" required class="w-full border px-3 py-2 rounded" /></div>
      <div style="margin-bottom:12px;"><label><strong>Email:</strong></label><input type="email" id="inqEmail" name="email" required class="w-full border px-3 py-2 rounded" /></div>
      <div style="margin-bottom:12px;"><label><strong>Phone:</strong></label><input type="text" id="inqPhone" name="phone" required class="w-full border px-3 py-2 rounded" /></div>
      <div style="margin-bottom:12px;"><label><strong>Date to Visit:</strong></label><input type="date" id="inqDate" name="visit_date" required class="w-full border px-3 py-2 rounded" /></div>
      <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded">Submit Inquiry</button>
    </form>
  </div>
</div>

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
    <p><strong>Train Station:</strong> <span id="modalTrainStation"></span></p>
    <p><strong>Status:</strong> <span id="modalStatus"></span></p>
  </div>
</div>
</div>

<script>
const rooms = typeof dbRooms !== "undefined" ? dbRooms : [];
const searchForm = document.getElementById('searchForm');
const resultsContainer = document.getElementById('results');

function displayRooms(roomsArray) {
  resultsContainer.innerHTML = '';
  if(roomsArray.length === 0){
    resultsContainer.innerHTML = `<div class="no-results"><h4>No rooms found</h4><p>Please try different search criteria</p></div>`;
    return;
  }

  roomsArray.forEach(room=>{
    const isOwner = currentUserId && room.user_id == currentUserId;
    const statusValue = room.status ? room.status.replace('_',' ') : 'Unknown';
    const statusClass = room.status ? `status-${room.status}` : 'status-unknown';
    const card = document.createElement('div');
    card.className = 'card';
    card.innerHTML = `
<div class="image">
  <img src="${room.image_url}" alt="Room Image" style="width:100%;height:200px;object-fit:cover;border-radius:8px 8px 0 0;">
  <div class="rent-badge">¥${room.price ? room.price.toLocaleString() : ''}</div>
  <div class="status-badge ${statusClass}">${statusValue}</div>
</div>
<div class="card-content">
  <h4>${room.title || ''}</h4>
  <p><i class="fas fa-map-marker-alt"></i> ${room.location || ''}</p>
  <p><i class="fas fa-train"></i> ${room.train_station || ''}</p>
  <p><i class="fas fa-home"></i> ${room.type || ''}</p>
  <p>${room.description || ''}</p>
  <button class="details-btn" data-id="${room.id}">View Details <i class="fas fa-arrow-right"></i></button>
  <button class="inquiry-btn" data-id="${room.id}" style="margin-top:8px;background:#2ecc71;color:#fff;border:none;padding:10px;border-radius:8px;cursor:pointer;">Inquiry</button>
  ${isOwner ? `<button class="edit-btn" data-id="${room.id}" style="margin-top:8px;background:#f1c40f;color:#222;">Edit</button>
  <button class="delete-btn" data-id="${room.id}" style="margin-top:8px;background:#e74c3c;color:#fff;">Delete</button>` : ''}
</div>`;
    resultsContainer.appendChild(card);
  });

  // Details buttons
  document.querySelectorAll('.details-btn').forEach(btn=>{
    btn.addEventListener('click',function(){
      const roomId = parseInt(this.getAttribute('data-id'));
      const room = rooms.find(r=>r.id == roomId);
      if(room){
        document.getElementById('modalTitle').textContent = room.title||'';
        document.getElementById('modalImage').src = room.image_url||'';
        document.getElementById('modalRent').textContent = room.price ? `¥${room.price.toLocaleString()}`:'';
        document.getElementById('modalStation').textContent = room.location||'';
        document.getElementById('modalType').textContent = room.type||'';
        document.getElementById('modalDescription').textContent = room.description||'';
        document.getElementById('modalTrainStation').textContent = room.train_station||'';
        document.getElementById('modalStatus').textContent = room.status || 'Unknown';
        document.getElementById('roomModal').style.display = 'block';
      }
    });
  });

  // Inquiry buttons
  document.querySelectorAll('.inquiry-btn').forEach(btn=>{
    btn.addEventListener('click',function(){
      const roomId = this.getAttribute('data-id');
      document.getElementById('inquiryRoomId').value = roomId;
      document.getElementById('inquiryModal').style.display = 'block';
    });
  });

  // Edit/Delete
  document.querySelectorAll('.edit-btn').forEach(btn=>{
    btn.addEventListener('click',()=>{ window.location.href='edit-room.php?id='+btn.getAttribute('data-id'); });
  });
  document.querySelectorAll('.delete-btn').forEach(btn=>{
    btn.addEventListener('click',()=>{
      if(confirm('Are you sure you want to delete this room?')) window.location.href='delete-room.php?id='+btn.getAttribute('data-id');
    });
  });
}

// Search
function handleSearch(e){
  e.preventDefault();
  const rent = parseInt(document.getElementById('rent').value) || Number.MAX_VALUE;
  const station = document.getElementById('station').value.trim().toLowerCase();
  const roomType = document.getElementById('roomType').value.trim().toLowerCase();
  const filteredRooms = rooms.filter(room=>{
    return (room.price <= rent) &&
           (station === '' || (room.location && room.location.toLowerCase().includes(station))) &&
           (roomType === '' || (room.type && room.type.toLowerCase().includes(roomType)));
  });
  displayRooms(filteredRooms);
}

// Modal Close
document.getElementById('modalClose').onclick = ()=>{ document.getElementById('roomModal').style.display='none'; };
document.getElementById('inquiryModalClose').onclick = ()=>{ document.getElementById('inquiryModal').style.display='none'; };

searchForm.addEventListener('submit', handleSearch);
window.onload = ()=>{ displayRooms(rooms); };
</script>
</body>
</html>
