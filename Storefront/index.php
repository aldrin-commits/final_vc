<!-- landing page -->
<?php
session_start();

$siteName = "VC Cafe.ph";
$currentYear = date("Y");
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $siteName; ?></title>
        <link rel="shorcut icon" type="image" href="./images/orig_logo.png">

        <!--link to css-->
        <link rel="stylesheet" href="index-style.css">

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
                    <a class="active" href="index.php">Home</a>
                    <a href="aboutus.php">About Us</a>
                    <a href="gallery.php">Gallery</a>
                    <a href="services.php">Services</a>
                    <a href="contact.php">Contact Us</a>
                    <a href="login_index.php"><i class="fa-solid fa-user"></i></a>
                </div>
        </header>

        <!--home-->
        <section class="home" id="home">
            <div class="home-text">
                <h1>Bring the Coffee <br> Experience to Your Event!</h1>
                <h6><i>Book a pop-up coffee shop for your special occassion</i></h6>
                <p>Want to elevate your event with freshly brewed <br> coffee and a cozy café vibe? <bold>VC Cafe Pop-Up</bold> brings <br>a fully equipped pop-up coffee shop to your <br>location — perfect for weddings, corporate events, <br>markets, and private gatherings.</p>
                <a href="login_index.php" class="button">Book Now!</a>
            </div>
            <div class="home-img">
                <img src="images/cafee-removebg-preview.png" alt="">
            </div>
        </section>

        <!--Slideshow: Glimpse of the Gallery-->
        <section class="container">
            <h2 class="container-title">A Glimpse into Catered Events</h2>
            
            <div class="slideshow-contianer-glimpse">
                <a href = "gallery.php">
                    <div class="slidesfade">
                        <div class="numbertext">1/3</div>
                        <img src="images/wedding.jpg">
                        <div class="text">Chic Bar Setup for Unforgettable Celebrations</div>
                    </div>

                    <div class="slidesfade">
                        <div class="numbertext">2/3</div>
                        <img src="images/3.jpeg">
                        <div class="text">Construction Fun Meets Caffeine Run at Leo's 1st Birthday!</div>
                    </div>

                    <div class="slidesfade">
                        <div class="numbertext">3/3</div>
                        <img src="images/visayasmed.heic">
                        <div class="text">Brewing Comfort at VisayasMed</div>
                    </div>
                </a>
            </div>

        </section>

        <!--What We Do-->
        <section class="what-we-do">
            <h1>What We Do</h1>
            <div class="services-container">
                <?php
                $services = [
                    [
                        'icon' => '☕',
                        'title' => 'Corporate Gatherings',
                        'description' => 'Elevate your corporate events with VC Cafe\'s premium mobile coffee experience. We provide customized coffee service for meetings, conferences, team-building events, and office celebrations. Our professional setup creates a sophisticated coffee bar experience that impresses clients and energizes employees, all while fostering meaningful connections through exceptional beverages and food.'
                    ],
                    [
                        'icon' => '💍',
                        'title' => 'Weddings & Private Events',
                        'description' => 'Make your special day even more memorable with our artisanal coffee and food offerings. We create bespoke coffee experiences for weddings, engagement parties, and intimate gatherings. Our Melbourne-inspired coffee bar becomes a charming focal point where guests can enjoy handcrafted beverages and freshly made pastries while celebrating your milestone moments.'
                    ],
                    [
                        'icon' => '🎉',
                        'title' => 'Festivals & Brand Launches',
                        'description' => 'Stand out at festivals and product launches with our distinctive mobile coffee concept. We collaborate with brands and event organizers to create engaging coffee experiences that attract crowds and enhance brand visibility. Our athlete-owned cafe brings quality, authenticity, and a unique atmosphere to any festival or launch event, helping to create buzz and memorable impressions.'
                    ]
                ];

                foreach ($services as $service) {
                    echo '<div class="service-box">';
                    echo '<h3>' . $service['icon'] . ' ' . $service['title'] . '</h3>';
                    echo '<p>' . $service['description'] . '</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="testimonials-section">
            <div class="testimonials-container">
                <?php
                $testimonials = [
                    [
                        'image' => 'images/prof1.jpeg',
                        'content' => 'Hehe we loved the coffee and cocktails yesterday. <br>So good❤️',
                        'rating' => 5
                    ],
                    [
                        'image' => 'images/prof2.jpeg',
                        'content' => 'Thank you so much for your services, Vic! The guests loved the drinks😊😊',
                        'rating' => 5
                    ],
                    [
                        'image' => 'images/prof3.jpeg',
                        'content' => 'Thank you jud kaayo Vic!!!Everyone loved it!',
                        'rating' => 5
                    ]
                ];

                // Loop through testimonials and display them
                foreach ($testimonials as $testimonial) {
                    echo '<div class="testimonial">';
                    echo '<div class="testimonial-profile">';
                    echo '<img src="' . $testimonial['image'] . '" class="testimonial-img" alt="Profile">';
                    echo '</div>';
                    echo '<div class="testimonial-content">';
                    echo '<p>' . $testimonial['content'] . '</p>';
                    echo '<div class="rating">' . str_repeat('⭐', $testimonial['rating']) . '</div>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </section>

        <footer>
            <div class="footerContainer">
                <div class="soc_med_links">
                    <a href="https://www.facebook.com/vccafecebu"><i class='bx bxl-facebook-circle'></i></a>
                    <a href="https://www.instagram.com/vccebu/"><i class='bx bxl-instagram-alt'></i></a>
                    <a href="https://www.tiktok.com/@brickandbrewph"><i class='bx bxl-tiktok'></i></a>
                </div>  
            </div>
            
            <div class="footerBottom">
                <p>Copyright &copy;<?php echo $currentYear; ?></p>
            </div>
        </footer>
       <script src="index-script.js"></script> 
    </body>
</html>