<?php

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>VC Cafe.ph</title>
        <link rel="shorcut icon" type="image" href="./images/orig_logo.png">

        <!--link to css-->
        <link rel="stylesheet" href="contact-style.css?v=3">

        <!--box icons-->
        <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">

        <!--Icons-->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
    <body>
        <!--nav bar-->
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
                    <a class="active" href="contact.php">Contact Us</a>
                    <a href="login_index.php">Login</a>
                </div>
        </header> 
        
        <!-- Updated Contact Section Based on Layout -->
<div class="main-content contact-flex-container">

    <div class="contact-box" style="margin-top: 35px;">
        <div class="contact-header">
            <h1>Get In Touch With Us!</h1>
        </div>

        <div class="contact-grid">
            <div class="contact-item top-left">
                <i class="fas fa-phone"></i>
                <h3>Phone Number</h3>
                <p>09176296230</p>
            </div>
            <div class="contact-item top-right">
                <i class="fas fa-envelope"></i>
                <h3>Email</h3>
                <p>vccafe@gmail.com</p>
            </div>
            <div class="contact-item bottom-left">
                <i class="fas fa-map-marker-alt"></i>
                <h3>Location</h3>
                <p>Talamban, Cebu City</p>
            </div>
            
            <div class="contact-item">
                    <i class="fas fa-comments"></i>
                    <h3>Social Media</h3>
                    <div class="social-links">
                        <a href="https://www.facebook.com/vccafecebu" target="_blank" class="social-link">
                            <i class='bx bxl-facebook-circle'></i>
                                <span>Facebook</span>
                         </a>
                        <a href="https://www.instagram.com/vccebu/" target="_blank" class="social-link">
                            <i class='bx bxl-instagram-alt'></i>
                                <span>Instagram</span>
                        </a>
                        <a href="https://www.tiktok.com/@brickandbrewph" target="_blank" class="social-link">
                            <i class='bx bxl-tiktok'></i>
                                <span>TikTok</span>
                        </a>
                    </div>
                </div>
        </div>
    </div>

    <!--div class="side-image">
        <img src="images/iconforcu.png" alt="Side Icon">
    </div-->
</div>

    <footer>
        <div class="footerBottom">
                <p>2025 Copyright ‎‎‎  |  ‎‎‎  VC Cafe All Rights Reserved.</p>
            </div>
    </footer>

       <script src="contact-script.js?v=2"></script> 
    </body>
</html>