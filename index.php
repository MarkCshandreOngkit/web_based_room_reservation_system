<?php

$conn = mysqli_connect("localhost", "root", "", "try_his");

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}


function formatDate($dateStr)
{
    return date("F j, Y", strtotime($dateStr));
}

function formatTime($timeStr)
{
    return date("h:i A", strtotime($timeStr));
}


function e($val)
{
    return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
}


function ejs($val)
{
    return addslashes(htmlspecialchars($val, ENT_QUOTES, 'UTF-8'));
}

function renderCard($row, $showCancel = false)
{
    $status = strtolower($row['status']);
    $timeRange = formatTime($row['start_time']) . " – " . formatTime($row['end_time']);
    $dateFmt = formatDate($row['reservation_date']);

    $jsArgs = implode(', ', array_map(fn($v) => '"' . ejs($v) . '"', [
        $row['image'],
        $row['room_name'],
        $row['floor_location'],
        $row['subject_activity'],
        $dateFmt,
        $timeRange,
        $row['reservation_type'],
        $row['room_type'],
        $row['notes'],
        $row['status'],
        $row['section_organization'],
        $row['capacity'],
        $row['facilities'],
        $row['reservation_code'],
    ]));

    echo "
    <div class='card'>

        <div class='left'>
            <div class='image-section'>
                <img src='" . e($row['image']) . "' alt='" . e($row['room_name']) . "'>
            </div>
            <h3>" . e($row['room_name']) . "</h3>
            <p>" . e($row['floor_location']) . "</p>
            <p>" . e($row['subject_activity']) . "</p>
            <p>Section: " . e($row['section_organization']) . "</p>
        </div>

        <div class='right'>
            <p>Date: " . e($dateFmt) . "</p>
            <p>Start Time: " . e(formatTime($row['start_time'])) . "</p>
            <p>End Time: " . e(formatTime($row['end_time'])) . "</p>
            <p>Room Type: " . e($row['room_type']) . "</p>
            <p class='notes-text'>Notes: " . e($row['notes']) . "</p>
            <span class='status $status'>" . e($row['status']) . "</span>

            <div class='buttons'>
                <button class='details-btn' onclick='openDetails($jsArgs)'>
                    View Details
                </button>
                " . ($showCancel ? "
                <button class='cancel-btn' onclick='openCancelModal(" . (int) $row['reservation_id'] . ")'>
                    Cancel
                </button>" : "") . "
            </div>
        </div>

    </div>
    ";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation History</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        :root {
            --red: #8b1a1a;
            --red-light: #fdf0f0;
            --red-dark: #5c1010;
            --grey-100: #f7f7f7;
            --grey-200: #e8e8e8;
            --grey-400: #aaa;
            --grey-700: #444;
            --green-bg: #e6f4ea;
            --green-fg: #1e7e34;
            --radius: 12px;
            --shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            --shadow-hover: 0 10px 28px rgba(0, 0, 0, 0.15);
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--grey-100);
            color: #222;
            padding: 32px 24px;
            max-width: 1100px;
            margin: 0 auto;
        }

        h1 {
            font-family: 'DM Serif Display', serif;
            font-size: 2.6rem;
            color: var(--red);
            margin-bottom: 4px;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--grey-700);
            margin: 32px 0 14px;
            padding-left: 4px;
            border-left: 4px solid var(--red);
            padding-left: 12px;
        }

        .empty-state {
            color: var(--grey-400);
            font-style: italic;
            margin: 8px 0 24px 16px;
        }

        .card {
            background: white;
            border: 1px solid var(--grey-200);
            border-radius: var(--radius);
            padding: 20px;
            margin-bottom: 16px;
            display: flex;
            gap: 24px;
            box-shadow: var(--shadow);
            transition: transform 0.25s, box-shadow 0.25s;
            cursor: default;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .left {
            flex: 0 0 auto;
            width: 200px;
        }

        .right {
            flex: 1;
        }

        .image-section img {
            width: 100%;
            height: 110px;
            border-radius: 8px;
            object-fit: cover;
            display: block;
            margin-bottom: 12px;
        }

        .left h3 {
            margin: 0 0 4px;
            color: var(--red);
            font-family: 'DM Serif Display', serif;
            font-size: 1.05rem;
        }

        .left p {
            margin: 3px 0;
            font-size: 0.83rem;
            color: var(--grey-700);
            line-height: 1.4;
        }

        .right p {
            margin: 5px 0;
            font-size: 0.88rem;
            color: #333;
        }

        .notes-text {
            color: var(--grey-700) !important;
            font-style: italic;
        }

        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 600;
            margin-top: 6px;
        }

        .ongoing {
            background: var(--green-bg);
            color: var(--green-fg);
        }

        .completed {
            background: var(--grey-200);
            color: var(--grey-700);
        }

        .cancelled {
            background: #fdecea;
            color: #c62828;
        }

        /* ── Buttons ── */
        .buttons {
            margin-top: 14px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .details-btn,
        .cancel-btn {
            padding: 8px 16px;
            border-radius: 7px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
        }

        .details-btn {
            border: 1.5px solid var(--red);
            background: white;
            color: var(--red);
        }

        .details-btn:hover {
            background: var(--red-light);
        }

        .cancel-btn {
            border: none;
            background: var(--red);
            color: white;
        }

        .cancel-btn:hover {
            background: var(--red-dark);
            transform: scale(1.03);
        }

        /* ── Modals shared ── */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 999;
            background: rgba(0, 0, 0, 0.45);
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(3px);
        }

        @keyframes popup {
            from {
                opacity: 0;
                transform: translateY(-16px) scale(0.96);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* ── Cancel modal ── */
        .modal-content {
            background: white;
            width: min(460px, 94vw);
            padding: 28px;
            border-radius: var(--radius);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
            animation: popup 0.22s ease;
            position: relative;
        }

        .close-btn {
            position: absolute;
            right: 18px;
            top: 14px;
            font-size: 22px;
            cursor: pointer;
            color: #888;
            line-height: 1;
        }

        .close-btn:hover {
            color: #222;
        }

        .modal-content h2 {
            font-family: 'DM Serif Display', serif;
            font-size: 1.5rem;
            margin: 0 0 20px;
            color: var(--red);
        }

        .warning-row {
            display: flex;
            gap: 14px;
            align-items: flex-start;
        }

        .warning-icon {
            font-size: 32px;
        }

        .warning-title {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .warning-sub {
            color: var(--grey-400);
            font-size: 0.85rem;
        }

        hr {
            margin: 20px 0;
            border: none;
            border-top: 1px solid var(--grey-200);
        }

        .reservation-info p {
            margin: 8px 0;
            font-size: 0.9rem;
        }

        .modal-buttons {
            margin-top: 24px;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .keep-btn {
            padding: 10px 18px;
            border: none;
            border-radius: 7px;
            background: var(--grey-200);
            font-family: 'DM Sans', sans-serif;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .keep-btn:hover {
            background: #d0d0d0;
        }

        .confirm-btn {
            padding: 10px 18px;
            border: none;
            border-radius: 7px;
            background: var(--red);
            color: white;
            font-family: 'DM Sans', sans-serif;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
        }

        .confirm-btn:hover {
            background: var(--red-dark);
            transform: scale(1.03);
        }

        /* ── Details modal ── */
        .details-content {
            background: white;
            width: min(680px, 96vw);
            max-height: 90vh;
            overflow-y: auto;
            padding: 28px;
            border-radius: var(--radius);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
            animation: popup 0.22s ease;
            position: relative;
        }

        .details-content h2 {
            font-family: 'DM Serif Display', serif;
            font-size: 1.6rem;
            margin: 0 0 20px;
            color: var(--red);
        }

        .close-details {
            position: absolute;
            right: 20px;
            top: 16px;
            font-size: 24px;
            cursor: pointer;
            color: #888;
            line-height: 1;
        }

        .close-details:hover {
            color: #222;
        }

        .top-section {
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }

        .modal-image {
            width: 190px;
            height: 130px;
            object-fit: cover;
            border-radius: 10px;
            flex-shrink: 0;
        }

        .top-info h3 {
            margin: 0 0 6px;
            color: var(--red);
            font-family: 'DM Serif Display', serif;
            font-size: 1.2rem;
        }

        .top-info p {
            margin: 6px 0;
            color: #555;
            font-size: 0.9rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px 24px;
            margin-top: 20px;
        }

        .info-grid p {
            margin: 3px 0;
            font-size: 0.88rem;
        }

        .info-grid strong {
            display: block;
            color: #555;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 2px;
        }

        .details-buttons {
            margin-top: 28px;
            text-align: right;
        }

        .close-modal-btn {
            padding: 10px 22px;
            border: none;
            border-radius: 8px;
            background: var(--red);
            color: white;
            font-family: 'DM Sans', sans-serif;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .close-modal-btn:hover {
            background: var(--red-dark);
        }

        /* ── Flash message ── */
        .flash {
            background: var(--green-bg);
            color: var(--green-fg);
            border: 1px solid #b2dfdb;
            border-radius: 8px;
            padding: 12px 18px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        @media (max-width: 600px) {
            .card {
                flex-direction: column;
            }

            .left {
                width: 100%;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .top-section {
                flex-direction: column;
            }

            .modal-image {
                width: 100%;
                height: 160px;
            }
        }
    </style>
</head>

<body>

    <!-- ── Cancel Confirmation Modal ── -->
    <div id="cancelModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="cancelModalTitle">
        <div class="modal-content">
            <span class="close-btn" id="closeCancelModal" aria-label="Close">&times;</span>
            <h2 id="cancelModalTitle">Cancel Reservation</h2>
            <div class="warning-row">
                <div class="warning-icon">⚠️</div>
                <div>
                    <p class="warning-title">Are you sure you want to cancel this reservation?</p>
                    <p class="warning-sub">This action cannot be undone.</p>
                </div>
            </div>
            <hr>
            <div class="reservation-info" id="cancelInfo"></div>
            <div class="modal-buttons">
                <button id="keepBtn" class="keep-btn">No, Keep It</button>
                <a id="confirmCancel" href="#">
                    <button class="confirm-btn">Yes, Cancel Reservation</button>
                </a>
            </div>
        </div>
    </div>

    <!-- ── Details Modal ── -->
    <div id="detailsModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="detailsModalTitle">
        <div class="details-content">
            <span class="close-details" id="closeDetailsModal" aria-label="Close">&times;</span>
            <h2 id="detailsModalTitle">Reservation Details</h2>
            <div class="top-section">
                <img id="modalImage" src="" alt="Room image" class="modal-image">
                <div class="top-info">
                    <h3 id="modalRoom"></h3>
                    <p id="modalFloor"></p>
                    <span id="modalStatus" class="status"></span>
                </div>
            </div>
            <hr>
            <h3>Reservation Information</h3>
            <div class="info-grid">
                <div><strong>Subject / Activity</strong>
                    <p id="modalSubject"></p>
                </div>
                <div><strong>Section / Organization</strong>
                    <p id="modalSection"></p>
                </div>
                <div><strong>Date</strong>
                    <p id="modalDate"></p>
                </div>
                <div><strong>Time</strong>
                    <p id="modalTime"></p>
                </div>
                <div><strong>Reservation Type</strong>
                    <p id="modalType"></p>
                </div>
                <div><strong>Room Type</strong>
                    <p id="modalRoomType"></p>
                </div>
            </div>
            <hr>
            <h3>Additional Information</h3>
            <div class="info-grid">
                <div><strong>Capacity</strong>
                    <p id="modalCapacity"></p>
                </div>
                <div><strong>Facilities</strong>
                    <p id="modalFacilities"></p>
                </div>
                <div><strong>Reservation Code</strong>
                    <p id="modalCode"></p>
                </div>
            </div>
            <hr>
            <strong style="font-size:0.78rem;color:#555;text-transform:uppercase;letter-spacing:0.04em;">Notes</strong>
            <p id="modalNotes" style="font-size:0.9rem;margin-top:4px;"></p>
            <div class="details-buttons">
                <button class="close-modal-btn" id="closeDetailsBtn">Close</button>
            </div>
        </div>
    </div>

    <!-- ── Page Content ── -->
    <h1>My Reservation History</h1>

    <?php if (isset($_GET['cancelled'])): ?>
        <div class="flash">✅ Reservation successfully cancelled.</div>
    <?php endif; ?>

    <!-- Upcoming -->
    <p class="section-title">Upcoming Reservations</p>
    <?php
    $sql = "SELECT * FROM reservations WHERE status = 'Ongoing' ORDER BY reservation_date ASC, start_time ASC";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) === 0):
        ?>
        <p class="empty-state">No upcoming reservations.</p>
    <?php else:
        while ($row = mysqli_fetch_assoc($result)):
            renderCard($row, true);
        endwhile;
    endif; ?>

    <!-- Past -->
    <p class="section-title">Past Reservations</p>
    <?php
    $sql2 = "SELECT * FROM reservations WHERE status IN ('Completed', 'Cancelled') ORDER BY reservation_date DESC";
    $result2 = mysqli_query($conn, $sql2);
    if (mysqli_num_rows($result2) === 0):
        ?>
        <p class="empty-state">No past reservations.</p>
    <?php else:
        while ($row2 = mysqli_fetch_assoc($result2)):
            renderCard($row2, false);
        endwhile;
    endif; ?>

    <?php mysqli_close($conn); ?>

    <script>

        const cancelModal = document.getElementById("cancelModal");
        const detailsModal = document.getElementById("detailsModal");

        function openCancelModal(id, room, date, time, subject, section) {
            document.getElementById("confirmCancel").href = "cancel.php?id=" + id;
            document.getElementById("cancelInfo").innerHTML =
                "<p><strong>Room:</strong> " + room + "</p>" +
                "<p><strong>Date:</strong> " + date + "</p>" +
                "<p><strong>Time:</strong> " + time + "</p>" +
                "<p><strong>Subject:</strong> " + subject + "</p>" +
                "<p><strong>Section:</strong> " + section + "</p>";
            cancelModal.style.display = "flex";
        }

        document.getElementById("closeCancelModal").onclick = () => cancelModal.style.display = "none";
        document.getElementById("keepBtn").onclick = () => cancelModal.style.display = "none";

        function openDetails(image, room, floor, subject, date, time, type, roomType, notes, status, section, capacity, facilities, code) {
            document.getElementById("modalImage").src = image;
            document.getElementById("modalRoom").textContent = room;
            document.getElementById("modalFloor").textContent = floor;
            document.getElementById("modalSubject").textContent = subject;
            document.getElementById("modalDate").textContent = date;
            document.getElementById("modalTime").textContent = time;
            document.getElementById("modalType").textContent = type;
            document.getElementById("modalRoomType").textContent = roomType;
            document.getElementById("modalNotes").textContent = notes;
            document.getElementById("modalSection").textContent = section;
            document.getElementById("modalCapacity").textContent = capacity;
            document.getElementById("modalFacilities").textContent = facilities;
            document.getElementById("modalCode").textContent = code;

            const statusEl = document.getElementById("modalStatus");
            statusEl.textContent = status;
            statusEl.className = "status " + status.toLowerCase();

            detailsModal.style.display = "flex";
        }

        document.getElementById("closeDetailsModal").onclick = () => detailsModal.style.display = "none";
        document.getElementById("closeDetailsBtn").onclick = () => detailsModal.style.display = "none";

        window.addEventListener("click", function (e) {
            if (e.target === cancelModal) cancelModal.style.display = "none";
            if (e.target === detailsModal) detailsModal.style.display = "none";
        });

        window.addEventListener("keydown", function (e) {
            if (e.key === "Escape") {
                cancelModal.style.display = "none";
                detailsModal.style.display = "none";
            }
        });
    </script>

</body>

</html>