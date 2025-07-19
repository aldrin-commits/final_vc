<?php
session_start();
require_once __DIR__ . '/../includes/db.php'; //  use global DB

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}


// Process status updates if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservation_id']) && isset($_POST['status'])) {
    $reservation_id = filter_input(INPUT_POST, 'reservation_id', FILTER_VALIDATE_INT);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
    
    if ($reservation_id && in_array($status, ['pending', 'confirmed', 'cancelled'])) {
        $stmt = $conn->prepare("UPDATE reservations SET reservation_status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $reservation_id);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            $_SESSION['success_message'] = "Reservation status updated successfully!";
        } else {
            $_SESSION['error_message'] = "Failed to update reservation status.";
        }
        
        // Redirect to refresh page and prevent form resubmission
        header("Location: admin_index.php");
        exit();
    }
}

// Get reservations
$reservations = [];
$sql = "SELECT * FROM reservations ORDER BY date DESC, time_start ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reservations[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - VC Café</title>
    <link rel="stylesheet" href="admin_style.css?v=3">
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="admin_script.js" defer></script>
</head>
<body>
    <div class="container">
        <div class="admin-header">
            <h1 style=
            "margin-top: 9px; margin-bottom: 9px">VC Café Admin Dashboard</h1>
            <div>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                <a href="admin_logout.php" class="btn logout-btn">Logout</a>
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
        
        <div class="dashboard-stats">
            <?php
            // Count reservations by status
            $pending_count = 0;
            $confirmed_count = 0;
            $cancelled_count = 0;
            $upcoming_count = 0;
            
            foreach ($reservations as $res) {
                if ($res['reservation_status'] === 'pending') {
                    $pending_count++;
                } else if ($res['reservation_status'] === 'confirmed') {
                    $confirmed_count++;
                } else if ($res['reservation_status'] === 'cancelled') {
                    $cancelled_count++;
                }
                
                // Count upcoming events (confirmed reservations with future dates)
                if ($res['reservation_status'] === 'confirmed' && $res['date'] >= date('Y-m-d')) {
                    $upcoming_count++;
                }
            }
            ?>
            
            <div class="stat-card">
                <h3>Pending</h3>
                <div class="number"><?php echo $pending_count; ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Confirmed</h3>
                <div class="number"><?php echo $confirmed_count; ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Cancelled</h3>
                <div class="number"><?php echo $cancelled_count; ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Upcoming Events</h3>
                <div class="number"><?php echo $upcoming_count; ?></div>
            </div>
        </div>
        
        <div class="tab-container">
            <div class="tabs">
                <button class="tab active" data-tab="all">All Reservations</button>
                <button class="tab" data-tab="pending">Pending</button>
                <button class="tab" data-tab="confirmed">Confirmed</button>
                <button class="tab" data-tab="upcoming">Upcoming Events</button>
            </div>
            
            <div class="filter-form">
                <label for="date-filter">Filter by Date:</label>
                <input type="date" id="date-filter" name="date">
                <button id="filter-btn" class="btn">Filter</button>
                <button id="reset-btn" class="btn">Reset</button>
            </div>
            
            <div id="all" class="tab-content active">
                <h2>All Reservations</h2>
                <?php if (count($reservations) > 0): ?>
                    <table class="reservation-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th style="padding: 12px 45px;">Date</th>
                                <th style="padding: 12px 60px;">Time</th>
                                <th>Event</th>
                                <th>Location</th>
                                <th>Contact</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations as $reservation): ?>
                                <tr data-date="<?php echo $reservation['date'] ?? ''; ?>">
                                    <td><?php echo $reservation['id'] ?? ''; ?></td>
                                    <td><?php echo !empty($reservation['date']) ? date('M j, Y', strtotime($reservation['date'])) : ''; ?></td>
                                    <td><?php echo !empty($reservation['time_start']) && !empty($reservation['time_end']) ? 
                                        date('g:i A', strtotime($reservation['time_start'])) . ' - ' . 
                                        date('g:i A', strtotime($reservation['time_end'])) : ''; ?></td>
                                    <td><?php echo htmlspecialchars($reservation['event_name'] ?? ''); ?></td>
                                    <td><?php echo !empty($reservation['location']) ? 
                                        htmlspecialchars(substr($reservation['location'], 0, 30)) . 
                                        (strlen($reservation['location']) > 30 ? '...' : '') : ''; ?></td>
                                    <td><?php echo htmlspecialchars($reservation['contact_name'] ?? '') . 
                                        (!empty($reservation['contact_name']) && !empty($reservation['contact_number']) ? '<br>' : '') . 
                                        htmlspecialchars($reservation['contact_number'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($reservation['email'] ?? ''); ?></td>
                                        <td>
                                            <span class="status-<?php echo $reservation['reservation_status'] ?? 'unknown'; ?>">
                                            <?php echo !empty($reservation['reservation_status']) ? ucfirst($reservation['reservation_status']) : 'Unknown'; ?>
                                            </span>
                                    </td>
                                    <td>
                                        <a href="admin_view_reservation.php?id=<?php echo $reservation['id']; ?>" class="action-btn view-btn">View</a>
                                        
                                        <?php if ($reservation['reservation_status'] === 'pending'): ?>
                                            <form action="admin_index.php" method="post" class="action-form">
                                                <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                                <input type="hidden" name="status" value="confirmed">
                                                <button type="submit" class="action-btn confirm-btn">Confirm</button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <?php if ($reservation['reservation_status'] !== 'cancelled'): ?>
                                            <form action="admin_index.php" method="post" class="action-form">
                                                <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="action-btn cancel-btn">Cancel</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No reservations found.</p>
                <?php endif; ?>
            </div>
            
            <div id="pending" class="tab-content">
                <h2>Pending Reservations</h2>
                <table class="reservation-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Event</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $pending_found = false;
                        foreach ($reservations as $reservation): 
                            if ($reservation['reservation_status'] === 'pending'):
                                $pending_found = true;
                        ?>
                            <tr data-date="<?php echo $reservation['date']; ?>">
                                <td><?php echo $reservation['id']; ?></td>
                                <td><?php echo date('M j, Y', strtotime($reservation['date'])); ?></td>
                                <td><?php echo date('g:i A', strtotime($reservation['time_start'])) . ' - ' . 
                                         date('g:i A', strtotime($reservation['time_end'])); ?></td>
                                <td><?php echo htmlspecialchars($reservation['event_name']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['contact_name']) . '<br>' . 
                                         htmlspecialchars($reservation['contact_number']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['email']); ?></td>
                                <td>
                                    <a href="admin_view_reservation.php?id=<?php echo $reservation['id']; ?>" class="action-btn view-btn">View</a>
                                    <form action="admin_index.php" method="post" class="action-form">
                                        <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                        <input type="hidden" name="status" value="confirmed">
                                        <button type="submit" class="action-btn confirm-btn">Confirm</button>
                                    </form>
                                    <form action="admin_index.php" method="post" class="action-form">
                                        <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="action-btn cancel-btn">Cancel</button>
                                    </form>
                                </td>
                            </tr>
                        <?php 
                            endif;
                        endforeach; 
                        
                        if (!$pending_found):
                        ?>
                            <tr>
                                <td colspan="7">No pending reservations found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div id="confirmed" class="tab-content">
                <h2>Confirmed Reservations</h2>
                <table class="reservation-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Event</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $confirmed_found = false;
                        foreach ($reservations as $reservation): 
                            if ($reservation['reservation_status'] === 'confirmed'):
                                $confirmed_found = true;
                        ?>
                            <tr data-date="<?php echo $reservation['date']; ?>">
                                <td><?php echo $reservation['id']; ?></td>
                                <td><?php echo date('M j, Y', strtotime($reservation['date'])); ?></td>
                                <td><?php echo date('g:i A', strtotime($reservation['time_start'])) . ' - ' . 
                                         date('g:i A', strtotime($reservation['time_end'])); ?></td>
                                <td><?php echo htmlspecialchars($reservation['event_name']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['contact_name']) . '<br>' . 
                                         htmlspecialchars($reservation['contact_number']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['email']); ?></td>
                                <td>
                                    <a href="admin_view_reservation.php?id=<?php echo $reservation['id']; ?>" class="action-btn view-btn">View</a>
                                    <form action="admin_index.php" method="post" class="action-form">
                                        <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="action-btn cancel-btn">Cancel</button>
                                    </form>
                                </td>
                            </tr>
                        <?php 
                            endif;
                        endforeach; 
                        
                        if (!$confirmed_found):
                        ?>
                            <tr>
                                <td colspan="7">No confirmed reservations found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div id="upcoming" class="tab-content">
                <h2>Upcoming Events</h2>
                <table class="reservation-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Event</th>
                            <th>Location</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $upcoming_found = false;
                        foreach ($reservations as $reservation): 
                            if ($reservation['reservation_status'] === 'confirmed' && $reservation['date'] >= date('Y-m-d')):
                                $upcoming_found = true;
                        ?>
                            <tr data-date="<?php echo $reservation['date']; ?>">
                                <td><?php echo $reservation['id']; ?></td>
                                <td><?php echo date('M j, Y', strtotime($reservation['date'])); ?></td>
                                <td><?php echo date('g:i A', strtotime($reservation['time_start'])) . ' - ' . 
                                         date('g:i A', strtotime($reservation['time_end'])); ?></td>
                                <td><?php echo htmlspecialchars($reservation['event_name']); ?></td>
                                <td><?php echo htmlspecialchars(substr($reservation['location'], 0, 30)) . 
                                         (strlen($reservation['location']) > 30 ? '...' : ''); ?></td>
                                <td><?php echo htmlspecialchars($reservation['contact_name']) . '<br>' . 
                                         htmlspecialchars($reservation['contact_number']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['email']); ?></td>
                                <td>
                                    <a href="admin_view_reservation.php?id=<?php echo $reservation['id']; ?>" class="action-btn view-btn">View</a>
                                    <form action="admin_index.php" method="post" class="action-form">
                                        <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="action-btn cancel-btn">Cancel</button>
                                    </form>
                                </td>
                            </tr>
                        <?php 
                            endif;
                        endforeach; 
                        
                        if (!$upcoming_found):
                        ?>
                            <tr>
                                <td colspan="8">No upcoming events found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>