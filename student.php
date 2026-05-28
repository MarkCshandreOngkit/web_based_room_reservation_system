<?php 
session_start(); 

// Redirect to login if user session is not active
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: sign-in.php");
    exit;
}

// Set fallback profile display name
$display_name = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Student';

// Set department label based on user role
$display_dept = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') ? 'Administrative Office' : 'Computer Engineering Department';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PUP-CEA Room Reservation System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="index.css">
</head>
<body>

    <header class="navbar">
        <div class="nav-brand" id="navHomeAction" style="cursor: pointer;">
            <div class="home-icon">
                <img src="images/home.png" class="home" alt="Home">
            </div>
            <h1 class="navbar-title">PUP-CEA Room Reservation System</h1>
        </div>
        
        <div class="profile-menu-container" id="profileMenuContainer">
            <div class="profile-trigger" id="profileTrigger">
                <div class="profile-avatar-wrapper">
                    <img src="images/avatar.png" alt="User Avatar" class="profile-avatar-img" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'%23888888\'><path d=\'M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z\'/></svg>'">
                </div>
                <span class="profile-user-name"><?php echo $display_name; ?></span>
            </div>
            
            <div class="profile-dropdown-card" id="profileDropdownCard">
                <div class="dropdown-header">
                    <span class="user-display-name"><?php echo $display_name; ?></span>
                    <span class="user-display-dept"><?php echo $display_dept; ?></span>
                </div>
                <hr class="dropdown-divider">
                <ul class="dropdown-links-list">
                    <li>
                        <a href="history.php" class="dropdown-item">
                            <img src="images/reservations-icon.png" alt="" class="dropdown-item-icon" onerror="this.style.display='none'">
                            My Reservations
                        </a>
                    </li>
                    <li>
                        <a href="logout.php" class="dropdown-item action-signout" id="logoutActionButton">
                            <img src="images/logout.png" alt="" class="dropdown-item-icon" onerror="this.style.display='none'">
                            Sign Out
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="sign-in-overlay" id="heroTextContainer">   
            <h2>Welcome to PUP-CEA Room Reservation System</h2>
            <p>Easily book lecture and laboratory rooms for your academic needs</p>
        </div>
    </section>
        
    <div id="mainHomepageContent">
        <div class="room-titles">
            <div class="room-title">
                <div class="card-img-holder">
                    <img src="images/Lecture Room.png" alt="Lecture Rooms" class="card-img">
                </div>
                <div class="card-content">
                    <h3>Lecture Rooms</h3>
                    <p>Spaces for formal instruction where students attend lectures, discussions, and presentations for academic learning.</p>
                    <button type="button" class="view-details-btn" onclick="showDetails('lectures')">
                        View Details &gt;
                    </button>
                </div>
            </div>

            <div class="room-title">
                <div class="card-img-holder">
                    <img src="images/Laboratory Room.png" alt="Laboratory Rooms" class="card-img">
                </div>
                <div class="card-content">
                    <h3>Laboratory Rooms</h3>
                    <p>Facilities for practical learning where students conduct experiments and hands-on activities using specialized tools and equipment.</p>
                    <button type="button" class="view-details-btn" onclick="showDetails('laboratory')">
                        View Details &gt;
                    </button>
                </div>
            </div>

            <div class="room-title">
                <div class="card-img-holder">
                    <img src="images/Faculty Room.png" alt="Faculty Rooms" class="card-img">
                </div>
                <div class="card-content">
                    <h3>Faculty Rooms</h3>
                    <p>Offices for instructors used for consultations, meetings, lesson preparation, and other academic and administrative work.</p>
                    <button type="button" class="view-details-btn" onclick="showDetails('faculty')">
                        View Details &gt;
                    </button>
                </div>
            </div>
        </div>

        <div class="book-now-container">
            <button class="book-now-btn" id="mainBookNowBtn">
                <img src="images/Book Now.png" alt="Book Now" class="book-now-icon">
                Book a room
            </button>
        </div>
    </div>

    <div id="details-section" class="modal-overlay">
        <div class="modal-card">
            <button type="button" class="back-arrow-btn" onclick="hideDetails()" aria-label="Go back">
                &#8592;
            </button>

            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Room Code</th>
                        <th>Room Name</th>
                        <th>Floor</th>
                        <th>Capacity</th>
                        <th>Status</th>
                        <th>Features</th>
                    </tr>
                </thead>
                <tbody id="rooms-tbody"></tbody>
            </table>
        </div>
    </div>

    <script>
        // DOM Element Selectors
        const mainBookNowBtn = document.getElementById('mainBookNowBtn'); 
        const homeIconBtn = document.querySelector('.home-icon'); 
        const mainHomepageContent = document.getElementById('mainHomepageContent');
        const heroTextContainer = document.getElementById('heroTextContainer');
        const profileTrigger = document.getElementById('profileTrigger');
        const profileDropdownCard = document.getElementById('profileDropdownCard');

        // Toggle user dropdown card layout
        profileTrigger.addEventListener('click', function(event) {
            event.stopPropagation();
            profileDropdownCard.classList.toggle('active');
        });

        // Close user profile dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!document.getElementById('profileMenuContainer').contains(event.target)) {
                profileDropdownCard.classList.remove('active');
            }
        });

        // Trigger reservation creation panel flow
        mainBookNowBtn.addEventListener('click', function() {
            alert("Redirecting to room scheduling booking workflow panel...");
        });
        
        // Reload page when brand home button is clicked
        homeIconBtn.addEventListener('click', function() {
            window.location.reload();
        });

        // Temporary local data structure for room categorization types
        const roomData = {
            lectures: [{ code: 'No data yet', name: 'No data yet', floor: 'No data yet', capacity: 'N/A', status: 'N/A', features: [] }],
            laboratory: [{ code: 'No data yet', name: 'No data yet', floor: 'No data yet', capacity: 'N/A', status: 'N/A', features: [] }],
            faculty: [{ code: 'No data yet', name: 'No data yet', floor: 'No data yet', capacity: 'N/A', status: 'N/A', features: [] }]
        };

        // Render dynamic table data and display overlay modal
        function showDetails(type) {
            const tbody = document.getElementById('rooms-tbody');
            tbody.innerHTML = '';

            roomData[type].forEach(room => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${room.code}</td>
                    <td>${room.name}</td>
                    <td>${room.floor}</td>
                    <td>${room.capacity}</td>
                    <td>${room.status}</td>
                    <td>${room.features.join(', ') || 'None'}</td>
                `;
                tbody.appendChild(row);
            });
            document.getElementById('details-section').style.display = 'flex';
        }

        // Hide overlay modal and clear specifications table node
        function hideDetails() {
            document.getElementById('details-section').style.display = 'none';
            document.getElementById('rooms-tbody').innerHTML = '';
        }
    </script>
</body>
</html>