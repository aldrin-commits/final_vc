<?php
session_start();
// Check if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin_index.php");
    exit();
}

// Initialize variables
$username = $password = "";
$error_message = "";

// Process login form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Admin credentials (in a real application, these would be stored in a database with hashed passwords)
    $admin_username = "admin";
    $admin_password = "vcadmin123"; // This should be hashed in production
    
    // Get and sanitize input
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password']; // No sanitization for password as it may contain special characters
    
    // Validate credentials
    if ($username === $admin_username && $password === $admin_password) {
        // Set session variables
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        
        // Redirect to dashboard
        header("Location: admin_index.php");
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
    <title>Admin Login - Coffee Pop-up</title>
    <link rel="stylesheet" href="admin_style.css?v=3">
</head>
<body>
    <div class="login-container">
        <div class="login-logo">
            <h1>VC Cafe Admin Panel</h1>
        </div>
        
        <div class="login-form">
            <h2 style="font-weight: 500; color:black;">Login</h2>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo $username; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">Login</button>
                </div>
            </form>
            
            <div class="back-link">
                <a href="index.php">Back to Main Site</a> 
            </div>
        </div>
    </div>
</body>
</html>