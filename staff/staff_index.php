<?php
session_start();
require_once __DIR__ . '/../includes/db.php'; // global DB config


// Check if user is logged in
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header("Location: staff_login.php");
    exit();
}

// Get reservations (staff can only view, no modification)
$reservations = [];
$sql = "SELECT id, date, time_start, time_end, event_name, location, contact_name, contact_number, email, reservation_status FROM reservations ORDER BY date DESC, time_start ASC";
$result = $conn->query($sql);

if (!$conn) {
    die("Database connection failed");
}

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
    <title>Staff Dashboard - Coffee Pop-up</title>
    <link rel="stylesheet" href="staff_style.css?v=3">
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="staff_script.js" defer></script>
</head>
<body>
    <div class="container">
        <div class="staff-header">
            <h1 style = "margin-top: 9px; margin-bottom: 9px;">VC Café Staff Dashboard</h1>
            <div>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['staff_username']); ?></span>
                <a href="staff_logout.php" class="btn logout-btn">Logout</a>
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
                                <th>Date</th>
                                <th>Time</th>
                                <th>Event</th>
                                <th>Location</th>
                                <th>Contact</th>
                                <th>Email</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations as $reservation): ?>
                                <tr data-date="<?php echo $reservation['date']; ?>">
                                    <td><?php echo $reservation['id']; ?></td>
                                    <td><?php echo date('M j, Y', strtotime($reservation['date'])); ?></td>
                                    <td><?php echo date('g:i A', strtotime($reservation['time_start'])) . ' - ' . 
                                             date('g:i A', strtotime($reservation['time_end'])); ?></td>
                                    <td><?php echo htmlspecialchars($reservation['event_name']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($reservation['location'], 0, 30)) . 
                                             (strlen($reservation['location']) > 30 ? '...' : ''); ?></td>
                                    <td><?php echo htmlspecialchars($reservation['contact_name']); ?></td>
                                    <td><?php echo htmlspecialchars($reservation['email']); ?></td>
                                    <td>
                                        <span class="status-<?php echo $reservation['reservation_status']; ?>">
                                            <?php echo ucfirst($reservation['reservation_status']); ?>
                                        </span>
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
                            <th>Location</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Status</th>
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
                                <td><?php echo htmlspecialchars(substr($reservation['location'], 0, 30)) . 
                                         (strlen($reservation['location']) > 30 ? '...' : ''); ?></td>
                                <td><?php echo htmlspecialchars($reservation['contact_name']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['email']); ?></td>
                                <td>
                                    <span class="status-<?php echo $reservation['reservation_status']; ?>">
                                        <?php echo ucfirst($reservation['reservation_status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php 
                            endif;
                        endforeach; 
                        
                        if (!$pending_found):
                        ?>
                            <tr>
                                <td colspan="8">No pending reservations found.</td>
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
                            <th>Location</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Status</th>
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
                                <td><?php echo htmlspecialchars(substr($reservation['location'], 0, 30)) . 
                                         (strlen($reservation['location']) > 30 ? '...' : ''); ?></td>
                                <td><?php echo htmlspecialchars($reservation['contact_name']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['email']); ?></td>
                                <td>
                                    <span class="status-<?php echo $reservation['reservation_status']; ?>">
                                        <?php echo ucfirst($reservation['reservation_status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php 
                            endif;
                        endforeach; 
                        
                        if (!$confirmed_found):
                        ?>
                            <tr>
                                <td colspan="8">No confirmed reservations found.</td>
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
                            <th>Status</th>
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
                                <td><?php echo htmlspecialchars($reservation['contact_name']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['email']); ?></td>
                                <td>
                                    <span class="status-<?php echo $reservation['reservation_status']; ?>">
                                        <?php echo ucfirst($reservation['reservation_status']); ?>
                                    </span>
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