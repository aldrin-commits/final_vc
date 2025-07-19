<?php
session_start();
require_once __DIR__ . '/../includes/db.php'; //global db

// Initialize response array
$response = [
    'success' => false,
    'message' => ''
];

// Check if user is logged in for admin actions
if (isset($_POST['admin_action']) && (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true)) {
    $response['message'] = "Authentication required";
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Process reservation status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_action']) && $_POST['admin_action'] === 'update_status') {
    $reservation_id = filter_input(INPUT_POST, 'reservation_id', FILTER_VALIDATE_INT);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
    
    if ($reservation_id && in_array($status, ['pending', 'confirmed', 'cancelled'])) {
        $stmt = $conn->prepare("UPDATE reservations SET reservation_status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $reservation_id);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            $response['success'] = true;
            $response['message'] = "Reservation status updated successfully!";
            
            // Get reservation details for email notification
            $stmt = $conn->prepare("SELECT email, event_name, date, time_start, time_end FROM reservations WHERE id = ?");
            $stmt->bind_param("i", $reservation_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                // In a production environment, you would send email notifications here
                // mail($row['contact_email'], "Reservation Status Update", "Your reservation for {$row['event_name']} on {$row['date']} has been {$status}.");
                
                $response['reservation'] = [
                    'email' => $row['email'],
                    'event_name' => $row['event_name'],
                    'date' => $row['date'],
                    'time_start' => $row['time_start'],
                    'time_end' => $row['time_end'],
                    'status' => $status
                ];
            }
        } else {
            $response['message'] = "Failed to update reservation status or no changes made.";
        }
    } else {
        $response['message'] = "Invalid reservation ID or status.";
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Process new reservation submission
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['admin_action']) || $_POST['admin_action'] === 'create')) {
    // Validate and sanitize inputs
    $event_name = filter_input(INPUT_POST, 'event_name', FILTER_SANITIZE_STRING);
    $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
    $time_start = filter_input(INPUT_POST, 'time_start', FILTER_SANITIZE_STRING);
    $time_end = filter_input(INPUT_POST, 'time_end', FILTER_SANITIZE_STRING);
    $contact_name = filter_input(INPUT_POST, 'contact_name', FILTER_SANITIZE_STRING);
    $contact_number = filter_input(INPUT_POST, 'contact_number', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $guest_count = filter_input(INPUT_POST, 'guest_count', FILTER_VALIDATE_INT);
    $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
    $special_requirements = filter_input(INPUT_POST, 'special_requirements', FILTER_SANITIZE_STRING);
    
    // Admin can set initial status if logged in
    $initial_status = 'pending';
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true && isset($_POST['reservation_status'])) {
        $requested_status = filter_input(INPUT_POST, 'reservation_status', FILTER_SANITIZE_STRING);
        if (in_array($requested_status, ['pending', 'confirmed', 'cancelled'])) {
            $initial_status = $requested_status;
        }
    }
    
    // Validate inputs
    $errors = [];
    
    if (empty($event_name)) {
        $errors[] = "Event name is required";
    }
    
    if (empty($date)) {
        $errors[] = "Date is required";
    } elseif (strtotime($date) < strtotime(date('Y-m-d')) && $initial_status !== 'cancelled') {
        $errors[] = "Date cannot be in the past";
    }
    
    if (empty($time_start) || empty($time_end)) {
        $errors[] = "Start and end times are required";
    } elseif (strtotime($time_start) >= strtotime($time_end)) {
        $errors[] = "End time must be after start time";
    }
    
    if (empty($contact_name)) {
        $errors[] = "Contact name is required";
    }
    
    if (empty($contact_number)) {
        $errors[] = "Contact number is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }
    
    if (!$guest_count || $guest_count <= 0) {
        $errors[] = "Expected attendees must be a positive number";
    }
    
    if (empty($location)) {
        $errors[] = "Location is required";
    }
    
    // Check if the date and time are available (skip this check for admin-created cancelled reservations)
    if (empty($errors) && $initial_status !== 'cancelled') {
        // If updating an existing reservation, exclude that reservation from the conflict check
        $exclude_id = isset($_POST['reservation_id']) ? filter_input(INPUT_POST, 'reservation_id', FILTER_VALIDATE_INT) : 0;
        
        $sql = "SELECT id FROM reservations WHERE date = ? AND 
                ((time_start <= ? AND time_end > ?) OR 
                 (time_start < ? AND time_end >= ?) OR 
                 (time_start >= ? AND time_end <= ?)) AND 
                 reservation_status IN ('pending', 'confirmed')";
                 
        if ($exclude_id > 0) {
            $sql .= " AND id != ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssssi", $date, $time_end, $time_start, $time_end, $time_start, $time_start, $time_end, $exclude_id);
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssss", $date, $time_end, $time_start, $time_end, $time_start, $time_start, $time_end);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors[] = "This time slot is already booked. Please choose a different time or date.";
        }
    }
    
    // Check if we're updating an existing reservation
    $updating = false;
    $reservation_id = 0;
    
    if (isset($_POST['reservation_id']) && isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        $reservation_id = filter_input(INPUT_POST, 'reservation_id', FILTER_VALIDATE_INT);
        if ($reservation_id > 0) {
            $updating = true;
        }
    }
    
    // If there are no errors, save the reservation
    if (empty($errors)) {
        if ($updating) {
            // Update existing reservation
            $stmt = $conn->prepare("UPDATE reservations SET 
                                    event_name = ?, 
                                    date = ?, 
                                    time_start = ?, 
                                    time_end = ?, 
                                    contact_name = ?, 
                                    contact_number = ?, 
                                    email = ?, 
                                    guest_count = ?, 
                                    location = ?, 
                                    special_requirements = ?, 
                                    reservation_status = ?,
                                    updated_at = NOW()
                                    WHERE id = ?");
            
            $stmt->bind_param("sssssssissssi", $event_name, $date, $time_start, $time_end, $contact_name, 
                             $contact_number, $email, $guest_count, $location, 
                             $special_requirements, $initial_status, $reservation_id);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Reservation #" . $reservation_id . " has been updated successfully!";
                $response['reservation_id'] = $reservation_id;
            } else {
                $response['message'] = "There was an error updating the reservation: " . $conn->error;
            }
        } else {
            // Create new reservation
            $stmt = $conn->prepare("INSERT INTO reservations (event_name, date, time_start, time_end, contact_name, 
                                   contact_number, email, guest_count, location, special_requirements, 
                                   reservation_status, created_at) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            
            $stmt->bind_param("sssssssisss", $event_name, $date, $time_start, $time_end, $contact_name, 
                             $contact_number, $email, $guest_count, $location, 
                             $special_requirements, $initial_status);
            
            if ($stmt->execute()) {
                $reservation_id = $conn->insert_id;
                
                // Send confirmation email to user (in a real application)
                // mail($contact_email, "Coffee Pop-up Reservation Confirmation", "...message...");
                
                // Set success response
                $response['success'] = true;
                $response['message'] = "Your reservation has been submitted successfully! Your reservation ID is: " . $reservation_id;
                $response['reservation_id'] = $reservation_id;
            } else {
                $response['message'] = "There was an error processing your reservation: " . $conn->error;
            }
        }
    } else {
        // Return validation errors
        $response['message'] = implode("<br>", $errors);
    }
} 
// Delete reservation (admin only)
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_action']) && $_POST['admin_action'] === 'delete') {
    $reservation_id = filter_input(INPUT_POST, 'reservation_id', FILTER_VALIDATE_INT);
    
    if ($reservation_id) {
        // Option 1: Completely delete the record
        // $stmt = $conn->prepare("DELETE FROM reservations WHERE id = ?");
        
        // Option 2: Soft delete by marking as cancelled and adding deletion note
        $stmt = $conn->prepare("UPDATE reservations SET 
                               reservation_status = 'cancelled', 
                               special_requirements = CONCAT(special_requirements, '\n[DELETED BY ADMIN ON ', NOW(), ']'),
                               updated_at = NOW()
                               WHERE id = ?");
        
        $stmt->bind_param("i", $reservation_id);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            $response['success'] = true;
            $response['message'] = "Reservation has been deleted successfully.";
        } else {
            $response['message'] = "Failed to delete reservation or reservation not found.";
        }
    } else {
        $response['message'] = "Invalid reservation ID.";
    }
} 
// Get reservations list or specific reservation details (admin only)
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    // Get specific reservation details
    if (isset($_GET['reservation_id'])) {
        $reservation_id = filter_input(INPUT_GET, 'reservation_id', FILTER_VALIDATE_INT);
        
        if ($reservation_id) {
            $stmt = $conn->prepare("SELECT * FROM reservations WHERE id = ?");
            $stmt->bind_param("i", $reservation_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                $response['success'] = true;
                $response['reservation'] = $row;
            } else {
                $response['message'] = "Reservation not found.";
            }
        } else {
            $response['message'] = "Invalid reservation ID.";
        }
    } 
    // Get filtered reservations list
    else {
        $where_clauses = [];
        $params = [];
        $types = "";
        
        // Filter by status
        if (isset($_GET['status']) && !empty($_GET['status'])) {
            $status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING);
            if (in_array($status, ['pending', 'confirmed', 'cancelled'])) {
                $where_clauses[] = "reservation_status = ?";
                $params[] = $status;
                $types .= "s";
            }
        }
        
        // Filter by date
        if (isset($_GET['date']) && !empty($_GET['date'])) {
            $date = filter_input(INPUT_GET, 'date', FILTER_SANITIZE_STRING);
            if (strtotime($date)) {
                $where_clauses[] = "date = ?";
                $params[] = $date;
                $types .= "s";
            }
        }
        
        // Filter by date range
        if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
            $date_from = filter_input(INPUT_GET, 'date_from', FILTER_SANITIZE_STRING);
            if (strtotime($date_from)) {
                $where_clauses[] = "date >= ?";
                $params[] = $date_from;
                $types .= "s";
            }
        }
        
        if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
            $date_to = filter_input(INPUT_GET, 'date_to', FILTER_SANITIZE_STRING);
            if (strtotime($date_to)) {
                $where_clauses[] = "date <= ?";
                $params[] = $date_to;
                $types .= "s";
            }
        }
        
        // Filter for upcoming events
        if (isset($_GET['upcoming']) && $_GET['upcoming'] === 'true') {
            $where_clauses[] = "date >= CURDATE() AND reservation_status = 'confirmed'";
        }
        
        // Build the SQL query
        $sql = "SELECT * FROM reservations";
        
        if (!empty($where_clauses)) {
            $sql .= " WHERE " . implode(" AND ", $where_clauses);
        }
        
        // Add ordering
        $sql .= " ORDER BY date DESC, time_start ASC";
        
        // Prepare and execute the statement
        if (!empty($params)) {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $conn->query($sql);
        }
        
        // Fetch results
        $reservations = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $reservations[] = $row;
            }
        }
        
        $response['success'] = true;
        $response['reservations'] = $reservations;
    }
} else {
    $response['message'] = "Invalid request method or unauthorized access";
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>