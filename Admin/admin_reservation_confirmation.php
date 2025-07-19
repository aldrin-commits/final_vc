<?php
session_start();
require_once __DIR__ . '/../includes/db.php'; //global db


// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "No reservation ID provided";
    header("Location: admin_index.php");
    exit();
}

$reservation_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$reservation_id) {
    $_SESSION['error_message'] = "Invalid reservation ID";
    header("Location: admin_index.php");
    exit();
}

// Process form submission for status update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check which button was clicked
    if (isset($_POST['confirm_reservation'])) {
        $status = 'confirmed';
    } elseif (isset($_POST['cancel_reservation'])) {
        $status = 'cancelled';
    } elseif (isset($_POST['pending_reservation'])) {
        $status = 'pending';
    } else {
        // Default fallback if somehow no button was specified
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
    }
    
    $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING);
    
    if (in_array($status, ['pending', 'confirmed', 'cancelled'])) {
        $stmt = $conn->prepare("UPDATE reservations SET reservation_status = ?, notes = ? WHERE id = ?");
        $stmt->bind_param("ssi", $status, $notes, $reservation_id);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            $_SESSION['success_message'] = "Reservation updated successfully to " . ucfirst($status) . "!";
        } else {
            $_SESSION['error_message'] = "No changes were made or update failed.";
        }
        
        // Redirect to refresh page
        header("Location: admin_view_reservation.php?id=" . $reservation_id);
        exit();
    }
}

// Get reservation details
$stmt = $conn->prepare("SELECT * FROM reservations WHERE id = ?");
$stmt->bind_param("i", $reservation_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Reservation not found";
    header("Location: admin_index.php");
    exit();
}

$reservation = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Reservation - Coffee Pop-up Admin</title>
    <link rel="stylesheet" href="admin_style.css?v=3">
    <style>
        /* Additional styles for the buttons */
        .btn-confirm {
            background-color: #28a745;
            color: white;
        }
        .btn-cancel {
            background-color: #dc3545;
            color: white;
        }
        .btn-pending {
            background-color: #ffc107;
            color: black;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        .current-status {
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="admin-header">
            <h1>Reservation Details</h1>
            <div class="button-group">
                <a href="admin_index.php" class="btn">Back to Dashboard</a>
            </div>
        </div>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert success"><?php echo $_SESSION['success_message']; ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert error"><?php echo $_SESSION['error_message']; ?></div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
        
        <div class="reservation-container">
            <div class="admin-header">
                <h2>Reservation #<?php echo $reservation['id']; ?></h2>
                <span class="status-badge status-<?php echo $reservation['reservation_status']; ?>">
                    <?php echo ucfirst($reservation['reservation_status']); ?>
                </span>
            </div>
            
            <div class="reservation-details">
                <div class="detail-item">
                    <div class="detail-label">Event Name</div>
                    <div class="detail-value"><?php echo htmlspecialchars($reservation['event_name']); ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Date</div>
                    <div class="detail-value"><?php echo date('F j, Y', strtotime($reservation['date'])); ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Time</div>
                    <div class="detail-value">
                        <?php echo date('g:i A', strtotime($reservation['time_start'])) . ' - ' . 
                                 date('g:i A', strtotime($reservation['time_end'])); ?>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Duration</div>
                    <div class="detail-value">
                        <?php 
                        $start = new DateTime($reservation['time_start']);
                        $end = new DateTime($reservation['time_end']);
                        $interval = $start->diff($end);
                        echo $interval->format('%h hours %i minutes');
                        ?>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Contact Name</div>
                    <div class="detail-value"><?php echo htmlspecialchars($reservation['contact_name']); ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Contact Number</div>
                    <div class="detail-value"><?php echo htmlspecialchars($reservation['contact_number']); ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Email</div>
                    <div class="detail-value"><?php echo htmlspecialchars($reservation['email']); ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Expected Attendees</div>
                    <div class="detail-value"><?php echo $reservation['guest_count']; ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Location</div>
                    <div class="detail-value"><?php echo htmlspecialchars($reservation['location']); ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Created At</div>
                    <div class="detail-value">
                        <?php 
                        $created_at = isset($reservation['created_at']) ? 
                            date('F j, Y g:i A', strtotime($reservation['created_at'])) : 'N/A';
                        echo $created_at;
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="detail-item" style="grid-column: 1 / -1;">
                <div class="detail-label">Special Requirements</div>
                <div class="detail-value">
                    <?php echo !empty($reservation['special_requirements']) ? 
                        nl2br(htmlspecialchars($reservation['special_requirements'])) : 'None'; ?>
                </div>
            </div>
            
            <div class="status-form">
                <h3>Update Reservation</h3>
                <div class="current-status">
                    Current Status: <span class="status-<?php echo $reservation['reservation_status']; ?>">
                        <?php echo ucfirst($reservation['reservation_status']); ?>
                    </span>
                </div>
                
                <form action="admin_view_reservation.php?id=<?php echo $reservation_id; ?>" method="post">
                    <div class="form-group">
                        <label for="notes">Admin Notes</label>
                        <textarea id="notes" name="notes" rows="4"><?php echo htmlspecialchars($reservation['notes'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="action-buttons">
                        <button type="submit" name="confirm_reservation" class="btn btn-confirm" <?php echo ($reservation['reservation_status'] === 'confirmed') ? 'disabled' : ''; ?>>
                            Confirm Reservation
                        </button>
                        <button type="submit" name="cancel_reservation" class="btn btn-cancel" <?php echo ($reservation['reservation_status'] === 'cancelled') ? 'disabled' : ''; ?>>
                            Cancel Reservation
                        </button>
                        <button type="submit" name="pending_reservation" class="btn btn-pending" <?php echo ($reservation['reservation_status'] === 'pending') ? 'disabled' : ''; ?>>
                            Mark as Pending
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>