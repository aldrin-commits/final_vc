<?php
session_start();
require_once 'includes/db.php';
 // Make sure this file exists and contains your database connection
    
$error = '';
$success = '';
    
// Registration Process
if(isset($_POST['signUp'])){
    $firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
    $lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    // Validate input
    if(empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        $error = "All fields are required";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } elseif(strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {
        // Check if email already exists
        $checkEmail = "SELECT * FROM users WHERE email='$email'";
        $result = $conn->query($checkEmail);
        
        if($result->num_rows > 0){
            $error = "Email address already exists!";
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user data
            $insertQuery = "INSERT INTO users(firstName, lastName, email, password, created_at) 
                            VALUES ('$firstName', '$lastName', '$email', '$hashedPassword', NOW())";
            
            if($conn->query($insertQuery) === TRUE){
                $success = "Registration successful! Please sign in.";
                $_SESSION['show_signin'] = true;
            } else {
                $error = "Error: " . $conn->error;
            }
        }
    }
}

// Login Process
if(isset($_POST['signIn'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    if(empty($email) || empty($password)) {
        $error = "Both email and password are required";
    } else {
        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = $conn->query($sql);
        
        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            if(password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['firstName'] = $row['firstName'];
                $_SESSION['lastName'] = $row['lastName'];
                
                // Redirect to profile page
                header("Location: profile.php");
                exit();
            } else {
                $error = "Incorrect password";
            }
        } else {
            $error = "Email not found";
        }
    }
}

// Determine which form to show
$showSignIn = isset($_SESSION['show_signin']) || isset($_POST['signIn']);
?>

<!DOCTYPE html>
<html lang="en">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VC Café - Login & Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login_style.css?v=3">
</head>
    
<body>
    <header>
        <a href="#" class="logo">
            <img src="images/new_vc_logo_nobg.png" alt="">
            <h1 style="color:#BCA788;">VC Cafe</h1>
        </a>
        <!--menu icon bar-->
        <i class='bx bx-menu' id="menu-icon"></i>
        <!--links-->
        <div class="navbar">
            <a href="index.php">Home</a>
            <a href="services.php">Services</a>

            <!-- <a href="aboutus.php">About Us</a>-->
            <a href="gallery.php">Gallery</a>
            <a href="contact.php">Contact Us</a>
            <a class="active" href="login_index.php">Login</a>

        </div>
    </header>

    <!-- Registration Form -->
    <div class="container" id="signup" style="display: <?php echo $showSignIn ? 'none' : 'block'; ?>">
        
        <div class="header-inline">
            <h1 class="header-text"><span>Register at VC Cafe</span></h1>
            </div>
            
        
        <?php if($error && !$showSignIn): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="firstName" id="firstName" placeholder=" " required>
                <label for="firstName">First Name</label>
            </div>
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="lastName" id="lastName" placeholder=" " required>
                <label for="lastName">Last Name</label>
            </div>
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" id="registerEmail" placeholder=" " required>
                <label for="registerEmail">Email</label>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="registerPassword" placeholder=" " required>
                <label for="registerPassword">Password</label>
            </div>
            <input type="submit" class="btn" value="Sign Up" name="signUp">
        </form>
        <div class="links">
            <p>Already have an account?</p>
            <button id="signInButton">Sign In</button>
        </div>
    </div>

    <!-- Login Form -->
    <div class="container" id="signIn" style="display: <?php echo $showSignIn ? 'block' : 'none'; ?>">
        <div class="header-inline">
        <h1 class="header-text"><span>Welcome Back</span></h1>
        </div>
        
        <?php if($error && $showSignIn): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" id="loginEmail" placeholder=" " required>
                <label for="loginEmail">Email</label>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="loginPassword" placeholder=" " required>
                <label for="loginPassword">Password</label>
            </div>
            
            <input type="submit" class="btn" value="Sign In" name="signIn">
        </form>
        <!--div class="or">
            <span>or sign in with</span>
        </div>
        <div class="icons">
            <i class="fab fa-google"></i>
            <i class="fab fa-facebook"></i>
        </div-->
        
        <div class="links">
            <p>Don't have an account yet?</p>
            <button id="signUpButton">Sign Up</button>
        </div>
    </div>

    <script src="login_script.js"></script>
</body>
</html>