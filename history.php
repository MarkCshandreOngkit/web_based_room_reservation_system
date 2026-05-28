<?php
session_start(); 

// Redirect to login if user session is not active
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: sign-in.php");
    exit;
}

$current_user_id = $_SESSION['user_id'];

// Set fallback profile display name
$display_name = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Student';

// Set department label uniformly for the user view
$display_dept = 'Computer Engineering Department';

// Database connection setup
$conn = mysqli_connect("localhost", "root", "", "demo_his");
if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

// Query upcoming/ongoing reservations strictly filtering by the active user ID
$sql = "SELECT * FROM `reservations` WHERE `status`='Ongoing' AND `user_id` = " . intval($current_user_id);
$result = mysqli_query($conn, $sql);

// Query history records strictly filtering by the active user ID
$sql2 = "SELECT * FROM `reservations` WHERE `status` IN ('Completed', 'Cancelled') AND `user_id` = " . intval($current_user_id);
$result2 = mysqli_query($conn, $sql2);

/**
 * Maps database room names to local image paths
 * @param string $roomName
 * @return string Image asset file path
 */
function getRoomImageSrc($roomName) {
    $roomKey = trim($roomName);

    switch ($roomKey) {
        case 'EE Lab 1A':
            return 'images/EElab1A.jpg';
        case 'EE Lab 1B':
            return 'images/EElab1B.jpg';
        case 'EE Lab 2B':
            return 'images/EElab1A.jpg'; 
        case 'Seminar Room A':
            return 'images/classroom1.jpg';
        case 'Seminar Room B':
            return 'images/Faculty Room.png';
        default:
            return 'images/classroom1.jpg'; // Fallback asset
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservations | PUP-CEA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="index.css">
    <style>
        html, body {
            overflow: auto !important;
            height: auto;
        }
        /* Fallback UI styling for missing/broken images */
        .image-fallback-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f3f4f6;
            color: #6b7280;
            font-size: 0.85rem;
            font-weight: 500;
            text-align: center;
            border: 1px dashed #d1d5db;
            box-sizing: border-box;
        }
    </style>
</head>
<body class="history-body">

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

    <main class="history-container">
        <header class="page-main-header">
            <h2 class="history-main-title">My Reservations</h2>
            <p class="history-subtitle">View and manage your room reservations.</p>
        </header>

        <section class="reservation-group-section">
            <h3 class="section-block-title">Upcoming Reservations</h3>
            
            <?php
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    $imgSrc = getRoomImageSrc($row['room_name']);
                    
                    echo "
                    <div class='reservation-row-card'>
                        <div class='card-visual-frame'>";
                        if ($imgSrc === "PLACEHOLDER") {
                            echo "<div class='image-fallback-placeholder'>No Room Image</div>";
                        } else {
                            echo "<img src='" . $imgSrc . "' alt='Room Image' class='card-embedded-photo' onerror=\"this.style.display='none'; this.parentNode.innerHTML='<div class=\'image-fallback-placeholder\'>Image Unreachable</div>';\">";
                        }
                    echo "</div>
                        
                        <div class='card-details-grid'>
                            <div class='info-column title-group'>
                                <h4 class='room-display-name'>" . htmlspecialchars($row['room_name']) . "</h4>
                                <p class='floor-location-tag'>" . htmlspecialchars($row['floor_location']) . "</p>
                                <span class='status-badge ongoing'>Ongoing</span>
                            </div>
                            
                            <div class='info-column feature-group'>
                                <div class='meta-data-item'>
                                    <span class='meta-label'>Subject / Activity</span>
                                    <p class='meta-value'>" . htmlspecialchars($row['subject_activity']) . "</p>
                                </div>
                                <div class='meta-data-item'>
                                    <span class='meta-label'>Section / Organization</span>
                                    <p class='meta-value'>" . htmlspecialchars($row['section_organization']) . "</p>
                                </div>
                            </div>
                            
                            <div class='info-column schedule-group'>
                                <div class='meta-data-item'>
                                    <span class='meta-label'>Time</span>
                                    <p class='meta-value'>" . date("h:i A", strtotime($row['start_time'])) . " - " . date("h:i A", strtotime($row['end_time'])) . "</p>
                                </div>
                                <div class='meta-data-item'>
                                    <span class='meta-label'>Date</span>
                                    <p class='meta-value'>" . date("F d, Y (l)", strtotime($row['reservation_date'])) . "</p>
                                </div>
                            </div>
                            
                            <div class='card-action-utilities'>
                                <button class='action-btn-outline' onclick='openDetails(
                                    \"".addslashes($imgSrc)."\",
                                    \"".addslashes($row['room_name'])."\",
                                    \"".addslashes($row['floor_location'])."\",
                                    \"".addslashes($row['subject_activity'])."\",
                                    \"".date("F d, Y", strtotime($row['reservation_date']))."\",
                                    \"".date("h:i A", strtotime($row['start_time']))." - ".date("h:i A", strtotime($row['end_time']))."\",
                                    \"".addslashes($row['room_type'])."\",
                                    \"".addslashes($row['notes'])."\",
                                    \"".addslashes($row['status'])."\",
                                    \"".addslashes($row['section_organization'])."\",
                                    \"".addslashes($row['capacity'])."\",
                                    \"".addslashes($row['facilities'])."\",
                                    \"".addslashes($row['reservation_code'])."\"
                                )'>View Details</button>
                                <button class='action-btn-solid active-danger' onclick='openModal(".$row['reservation_id'].", \"".addslashes($row['room_name'])."\", \"".date("F d, Y", strtotime($row['reservation_date']))."\", \"".date("h:i A", strtotime($row['start_time']))." - ".date("h:i A", strtotime($row['end_time']))."\")'>Cancel</button>
                            </div>
                        </div>
                    </div>";
                }
            } else {
                echo "<p class='empty-records-placeholder'>No upcoming reservations found.</p>";
            }
            ?>
        </section>

        <section class="reservation-group-section">
            <h3 class="section-block-title">Past Reservations</h3>
            
            <?php
            if (mysqli_num_rows($result2) > 0) {
                while($row2 = mysqli_fetch_assoc($result2)) {
                    $statusClass = strtolower($row2['status']);
                    $imgSrc2 = getRoomImageSrc($row2['room_name']);
                    
                    echo "
                    <div class='reservation-row-card past-record'>
                        <div class='card-visual-frame'>";
                        if ($imgSrc2 === "PLACEHOLDER") {
                            echo "<div class='image-fallback-placeholder'>No Room Image</div>";
                        } else {
                            echo "<img src='" . $imgSrc2 . "' alt='Room Image' class='card-embedded-photo' onerror=\"this.style.display='none'; this.parentNode.innerHTML='<div class=\'image-fallback-placeholder\'>Image Unreachable</div>';\">";
                        }
                    echo "</div>
                        
                        <div class='card-details-grid'>
                            <div class='info-column title-group'>
                                <h4 class='room-display-name'>" . htmlspecialchars($row2['room_name']) . "</h4>
                                <p class='floor-location-tag'>" . htmlspecialchars($row2['floor_location']) . "</p>
                            </div>
                            
                            <div class='info-column feature-group'>
                                <div class='meta-data-item'>
                                    <span class='meta-label'>Subject / Activity</span>
                                    <p class='meta-value'>" . htmlspecialchars($row2['subject_activity']) . "</p>
                                </div>
                                <div class='meta-data-item'>
                                    <span class='meta-label'>Section / Organization</span>
                                    <p class='meta-value'>" . htmlspecialchars($row2['section_organization']) . "</p>
                                </div>
                            </div>
                            
                            <div class='info-column schedule-group'>
                                <div class='meta-data-item'>
                                    <span class='meta-label'>Date</span>
                                    <p class='meta-value'>" . date("F d, Y (l)", strtotime($row2['reservation_date'])) . "</p>
                                </div>
                                <div class='meta-data-item'>
                                    <span class='meta-label'>Time</span>
                                    <p class='meta-value'>" . date("h:i A", strtotime($row2['start_time'])) . " - " . date("h:i A", strtotime($row2['end_time'])) . "</p>
                                </div>
                            </div>
                            
                            <div class='card-action-utilities container-vertical-center'>
                                <span class='status-badge ".$statusClass."'>".$row2['status']."</span>
                                <button class='action-btn-outline' onclick='openDetails(
                                    \"".addslashes($imgSrc2)."\",
                                    \"".addslashes($row2['room_name'])."\",
                                    \"".addslashes($row2['floor_location'])."\",
                                    \"".addslashes($row2['subject_activity'])."\",
                                    \"".date("F d, Y", strtotime($row2['reservation_date']))."\",
                                    \"".addslashes(date("h:i A", strtotime($row2['start_time']))." - ".date("h:i A", strtotime($row2['end_time'])))."\",
                                    \"".addslashes($row2['room_type'])."\",
                                    \"".addslashes($row2['notes'])."\",
                                    \"".addslashes($row2['status'])."\",
                                    \"".addslashes($row2['section_organization'])."\",
                                    \"".addslashes($row2['capacity'])."\",
                                    \"".addslashes($row2['facilities'])."\",
                                    \"".addslashes($row2['reservation_code'])."\"
                                )'>View Details</button>
                            </div>
                        </div>
                    </div>";
                }
            } else {
                echo "<p class='empty-records-placeholder'>No reservation history found.</p>";
            }
            ?>
        </section>
    </main>

    <div id="cancelModal" class="custom-app-modal">
        <div class="modal-window-card content-card-small">
            <span class="modal-close-trigger close-btn">&times;</span>
            <h3 class="modal-title-text text-danger-color">Cancel Reservation</h3>
            
            <div class="modal-alert-banner">
                <div class="alert-icon-wrapper">⚠️</div>
                <div class="alert-message-content">
                    <p class="alert-main-heading">Are you sure you want to cancel this reservation?</p>
                    <p class="alert-sub-heading">This action cannot be undone.</p>
                </div>
            </div>
            
            <hr class="app-ui-divider">
            
            <div class="modal-summary-panel">
                <p><strong>Room:</strong> <span id="cancelRoomName"></span></p>
                <p><strong>Date:</strong> <span id="cancelDate"></span></p>
                <p><strong>Time:</strong> <span id="cancelTime"></span></p>
            </div>
            
            <div class="modal-action-footer">
                <button id="keepBtn" class="action-btn-neutral">No, Keep It</button>
                <a id="confirmCancel" href="#">
                    <button class="action-btn-solid active-danger">Yes, Cancel Reservation</button>
                </a>
            </div>
        </div>
    </div>

    <div id="detailsModal" class="custom-app-modal">
        <div class="modal-window-card content-card-large">
            <span class="modal-close-trigger close-details">&times;</span>
            <h3 class="modal-title-text">Reservation Details</h3>
            
            <div class="details-hero-block">
                <div id="modalVisualContainer" style="width:100%; height:100%; position:relative;">
                    <img id="modalImage" src="" alt="Room Showcase" class="hero-block-image" style="width:100%; height:100%; object-fit:cover;">
                </div>
                <div class="hero-block-caption">
                    <h4 id="modalRoom" class="caption-heading"></h4>
                    <p id="modalFloor" class="caption-subheading"></p>
                    <span id="modalStatus" class="status-badge"></span>
                </div>
            </div>
            
            <hr class="app-ui-divider">
            
            <h4 class="form-section-header">Reservation Information</h4>
            <div class="details-metadata-grid">
                <div>
                    <label class="metadata-grid-label">Subject / Activity</label>
                    <p id="modalSubject" class="metadata-grid-value"></p>
                </div>
                <div>
                    <label class="metadata-grid-label">Section / Organization</label>
                    <p id="modalSection" class="metadata-grid-value"></p>
                </div>
                <div>
                    <label class="metadata-grid-label">Date</label>
                    <p id="modalDate" class="metadata-grid-value"></p>
                </div>
                <div>
                    <label class="metadata-grid-label">Time Slot</label>
                    <p id="modalTime" class="metadata-grid-value"></p>
                </div>
            </div>
            
            <hr class="app-ui-divider">
            
            <h4 class="form-section-header">Additional Specification Details</h4>
            <div class="details-metadata-grid">
                <div>
                    <label class="metadata-grid-label">Room Capacity</label>
                    <p id="modalCapacity" class="metadata-grid-value"></p>
                </div>
                <div>
                    <label class="metadata-grid-label">Facilities</label>
                    <p id="modalFacilities" class="metadata-grid-value"></p>
                </div>
                <div>
                    <label class="metadata-grid-label">Room Type</label>
                    <p id="modalRoomType" class="metadata-grid-value"></p>
                </div>
                <div>
                    <label class="metadata-grid-label">Reservation Token Reference</label>
                    <p id="modalCode" class="metadata-grid-value code-highlight"></p>
                </div>
            </div>
            
            <hr class="app-ui-divider">
            
            <div class="notes-display-panel">
                <label class="metadata-grid-label">User Special Directives / Notes</label>
                <p id="modalNotes" class="metadata-grid-value italicized-placeholder"></p>
            </div>
            
            <div class="modal-action-footer">
                <button class="action-btn-solid close-modal-btn">Close Window</button>
            </div>
        </div>
    </div>

    <script>
    // Elements for Profile Menu Dropdown Controls
    const profileTrigger = document.getElementById('profileTrigger');
    const profileDropdownCard = document.getElementById('profileDropdownCard');
    const navHomeAction = document.getElementById('navHomeAction');

    // Toggle dropdown card on trigger click
    profileTrigger.addEventListener('click', function(event) {
        event.stopPropagation();
        profileDropdownCard.classList.toggle('active');
    });

    // Close user dropdown menu when clicking completely outside
    document.addEventListener('click', function(event) {
        if (!document.getElementById('profileMenuContainer').contains(event.target)) {
            profileDropdownCard.classList.remove('active');
        }
    });

    // Brand click landing redirection path
    navHomeAction.addEventListener('click', function() {
        window.location.href = 'student.php';
    });

    // Core Modal Control Elements
    const cancelModal = document.getElementById("cancelModal");
    const detailsModal = document.getElementById("detailsModal");
    const modalImage = document.getElementById("modalImage");
    const modalVisualContainer = document.getElementById("modalVisualContainer");

    // Initialize and show cancellation data modal
    function openModal(id, roomName, resDate, resTime) {
        cancelModal.style.display = "flex";
        document.getElementById("confirmCancel").href = "cancel.php?id=" + id;
        document.getElementById("cancelRoomName").innerText = roomName;
        document.getElementById("cancelDate").innerText = resDate;
        document.getElementById("cancelTime").innerText = resTime;
    }

    // Initialize and map visual field data inside deep detail viewer modal
    function openDetails(image, room, floor, subject, date, time, type, notes, status, section, capacity, facilities, code){
        detailsModal.style.display = "flex";
        
        // Handle broken or missing asset injections elegantly
        if(image === "PLACEHOLDER" || image.trim() === ""){
            modalImage.style.display = "none";
            let fallbackExists = modalVisualContainer.querySelector('.image-fallback-placeholder');
            if(!fallbackExists) {
                modalVisualContainer.insertAdjacentHTML('beforeend', '<div class="image-fallback-placeholder">No Image Available</div>');
            }
        } else {
            modalImage.style.display = "block";
            modalImage.src = image;
            let fallbackExists = modalVisualContainer.querySelector('.image-fallback-placeholder');
            if(fallbackExists) fallbackExists.remove();
            
            // Re-render to fallback format contextually if server responds with 404
            modalImage.onerror = function() {
                this.style.display = 'none';
                if(!modalVisualContainer.querySelector('.image-fallback-placeholder')) {
                    modalVisualContainer.insertAdjacentHTML('beforeend', '<div class="image-fallback-placeholder">Image Unreachable</div>');
                }
            };
        }

        // Write content data text elements to detail modal nodes
        document.getElementById("modalRoom").innerHTML = room;
        document.getElementById("modalFloor").innerHTML = floor;
        document.getElementById("modalSubject").innerHTML = subject;
        document.getElementById("modalDate").innerHTML = date;
        document.getElementById("modalTime").innerHTML = time;
        document.getElementById("modalNotes").innerHTML = notes || "No custom notes appended.";
        document.getElementById("modalStatus").innerHTML = status;
        document.getElementById("modalStatus").className = "status-badge " + status.toLowerCase();
        document.getElementById("modalSection").innerHTML = section;
        document.getElementById("modalCapacity").innerHTML = capacity;
        document.getElementById("modalFacilities").innerHTML = facilities;
        document.getElementById("modalCode").innerHTML = code;
        document.getElementById("modalRoomType").innerHTML = type;
    }

    // Modal view window click targets dismiss hooks
    document.querySelector(".close-btn").onclick = () => cancelModal.style.display = "none";
    document.getElementById("keepBtn").onclick = () => cancelModal.style.display = "none";
    document.querySelector(".close-details").onclick = () => detailsModal.style.display = "none";
    document.querySelector(".close-modal-btn").onclick = () => detailsModal.style.display = "none";

    // Global document window dismissal capture clicks
    window.onclick = function(event) {
        if (event.target == cancelModal) cancelModal.style.display = "none";
        if (event.target == detailsModal) detailsModal.style.display = "none";
    }
    </script>
</body>
</html>