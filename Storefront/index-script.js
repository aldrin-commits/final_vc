// Wait for the DOM to fully load
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle functionality
    const menuIcon = document.getElementById('menu-icon');
    const navbar = document.querySelector('.navbar');
    
    if (menuIcon) {
        menuIcon.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent event bubbling
            navbar.classList.toggle('active');
            this.classList.toggle('bx-x');
        });
    }
    
    // Close mobile menu when clicking anywhere else
    document.addEventListener('click', function(e) {
        if (menuIcon && navbar && !menuIcon.contains(e.target) && !navbar.contains(e.target)) {
            navbar.classList.remove('active');
            menuIcon.classList.remove('bx-x');
        }
    });
    
    window.addEventListener('resize', function() {
        if (window.innerWidth > 992 && navbar.classList.contains('active')) {
            navbar.classList.remove('active');
            menuIcon.classList.remove('bx-x');
        }
    });

    // Prevent clicks inside the navbar from closing it
    if (navbar) {
        navbar.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
    
    // Add scroll effect to header
    window.addEventListener('scroll', function() {
        const header = document.querySelector('header');
        if (header) {
            header.classList.toggle('sticky', window.scrollY > 0);
        }
    });
    
    // Smooth scrolling for navigation links
    const navLinks = document.querySelectorAll('.navbar a');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            
            // Only apply smooth scroll for on-page links
            if (targetId && targetId.startsWith('#') && targetId.length > 1) {
                e.preventDefault();
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    const headerHeight = document.querySelector('header').offsetHeight;
                    const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - headerHeight;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                    
                    // Close mobile menu after clicking
                    if (window.innerWidth <= 992) {
                        navbar.classList.remove('active');
                        menuIcon.classList.remove('bx-x');
                    }
                }
            }
        });
    });

    // Slideshow functionality
    let slideIndex = 1;
    showSlides(slideIndex);

    // Make plusSlides function global so onclick handlers can access it
    window.plusSlides = function(n) {
        showSlides(slideIndex += n);
    }

    function showSlides(n) {
        let slides = document.getElementsByClassName("slidesfade");
        if (slides.length === 0) return; // Exit if no slides found
        
        if (n > slides.length) {slideIndex = 1}
        if (n < 1) {slideIndex = slides.length}
        
        for (let i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
            slides[i].classList.remove("fade");
        }
        
        if (slides[slideIndex-1]) {
            slides[slideIndex-1].style.display = "block";
            slides[slideIndex-1].classList.add("fade");
        }
    }

    // Auto-advance slides every 5 seconds
    const slideInterval = setInterval(function() {
        if (document.getElementsByClassName("slidesfade").length > 0) {
            window.plusSlides(1);
        }
    }, 5000);

    
    

    // Stop auto-advance when user hovers over slideshow
    if (slideshowContainer) {
        slideshowContainer.addEventListener('mouseenter', function() {
            clearInterval(slideInterval);
        });

        slideshowContainer.addEventListener('mouseleave', function() {
            // Restart auto-advance when user leaves slideshow
            setInterval(function() {
                if (document.getElementsByClassName("slidesfade").length > 0) {
                    window.plusSlides(1);
                }
            }, 5000);
        });
    };
});