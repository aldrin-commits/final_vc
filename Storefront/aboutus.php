<?php
$pageTitle = "About Us - VC Cafe Pop-Up";
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $pageTitle; ?></title>
        <link rel="shorcut icon" type="image" href="./images/orig_logo.png">

        <!--link to css-->
        <link rel="stylesheet" href="aboutus-style.css?v=2">

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
                    <a class="active" href="aboutus.php">About Us</a>
                    <a href="gallery.php">Gallery</a>
                    <a href="services.php">Services</a>
                    <a href="contact.php">Contact Us</a>
                    <a href="login_index.php"><i class="fa-solid fa-user"></i></a>
                </div>
        </header>

            <!--about us-->
            <div class="about-container">
                <!--
                <h1>About Us</h1>
                <p class="subtitle">Bringing a full-service coffee experience to your event</p>-->
        
                <div class="content-wrapper">
                    <div class="image-grid">
                        <div class="image-item">
                            <div class="image-placeholder"><img src="images/event pop-up.jpg" alt="About Us Image 1"></div>
                        </div>
                        <div class="image-item">
                            <div class="image-placeholder"><img src="images/2.jpeg" alt="About Us Image 2"></div>
                        </div>
                        <div class="image-item">
                            <div class="image-placeholder"><img src="images/4.5.jpeg" alt="About Us Image 3"></div>
                        </div>
                        <div class="image-item">
                            <div class="image-placeholder"><img src="images/5.5.jpeg" alt="About Us Image 4"></div>
                        </div>
                    </div>
                
                    <div class="text-section">
                        <div class="story-section">
                            <h2>Our Story</h2>
                            <p>VC Cafe, founded in 2023, aims to bring Melbourne's vibrant cafe culture to Cebu through a unique mobile coffee and bar concept. With a focus on high-quality, ethically sourced coffee, handmade food, freshly baked bread, and artisanal pastries, VC Cafe strives to create meaningful experiences and connections. As athlete-owners, they understand the importance of both quality fuel and genuine community. Their mobile concept allows them to bring these experiences to various locations, creating pop-up communities throughout Cebu, and offering a welcoming space for coffee lovers and casual visitors alike.</p>
                        </div>
                        <div class="why-choose-section">
                            <h2>Why Choose Us?</h2>
                            <p>VC Cafe brings an authentic Melbourne coffee experience to Cebu through its mobile concept. As athletes with a focus on quality, they offer expertly sourced, roasted, and prepared coffee alongside handmade food, freshly baked bread, and artisanal pastries. Each visit to VC Cafe is more than just about great coffee; it's about fostering community and connections in a unique, pop-up setting. By choosing VC Cafe, you're not only getting premium coffee and food but also an experience built on quality, passion, and a commitment to enriching Cebu's coffee culture.</p>
                        </div>
                    </div>
                </div>
            </div>
        
            <footer>
                <div class="footerContainer">
                    <div class="soc_med_links">
                        <a href="https://www.facebook.com/vccafecebu"><i class='bx bxl-facebook-circle'></i></a>
                        <a href="https://www.instagram.com/vccebu/"><i class='bx bxl-instagram-alt'></i></a>
                        <a href="https://www.tiktok.com/@brickandbrewph"><i class='bx bxl-tiktok'></i></a>
                    </div>  
                </div>
                
                <div class="footerBottom">
                    <p>Copyright &copy;<?php echo date("Y"); ?></p>
                </div>
            </footer>
    
    <script src="aboutus-script.js?v=2"></script>

    </body>
</html>