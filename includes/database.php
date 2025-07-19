<!-- This is a global datbase -->
<?php
// Error reporting (turn off in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_vc_cafe');

// Create MySQL connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character encoding
$conn->set_charset("utf8mb4");

// Optional: Define global app constants
define('SITE_NAME', 'Coffee Pop-up');
define('ADMIN_EMAIL', 'admin@coffeepopup.com');
?>

