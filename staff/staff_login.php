<?php
session_start();
require_once __DIR__ . '/../includes/db.php'; // global DB config

// If staff is already logged in, redirect to dashboard
if (isset($_SESSION['staff_logged_in']) && $_SESSION['staff_logged_in'] === true) {
    header("Location: staff_index.php");
    exit();
}

$error_message = '';

// Process login form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Staff credentials (in a real application, these would be stored in a database with hashed passwords)
    $staff_username = "staff1";
    $staff_password = "vcstaff"; // This should be hashed in production
    
    // Get and sanitize input
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password']; // No sanitization for password as it may contain special characters
    
    // Validate credentials
    if ($username === $staff_username && $password === $staff_password) {
        // Set session variables
        $_SESSION['staff_logged_in'] = true;
        $_SESSION['staff_username'] = $username;
        
        // Redirect to dashboard
        header("Location: staff_index.php");
        exit();
    } else {
        $error_message = "Invalid username or password";
    }
}

?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login - Coffee Pop-up</title>
    <link rel="stylesheet" href="staff_style.css?v=3">
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <h1>VC Cafe Staff Panel</h1>
            <p>Employee Access Portal</p>
        </div>
        
        <?php if ($error_message): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form action="staff_login.php" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required 
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="login-btn">Login</button>
        </form>
    </div>
</body>
</html>