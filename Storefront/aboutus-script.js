// Wait for the DOM to fully load
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle functionality
    const menuIcon = document.getElementById('menu-icon');
    const navbar = document.querySelector('.navbar');
    
    if (menuIcon && navbar) {
        menuIcon.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent event bubbling
            navbar.classList.toggle('active');
            this.classList.toggle('bx-x');
            
            // Log for debugging
            console.log('Menu clicked: navbar active = ' + navbar.classList.contains('active'));
        });
        
        // Close mobile menu when clicking anywhere else
        document.addEventListener('click', function(e) {
            if (!menuIcon.contains(e.target) && !navbar.contains(e.target)) {
                navbar.classList.remove('active');
                menuIcon.classList.remove('bx-x');
            }
        });
        
        // Close mobile menu when window is resized to desktop size
        window.addEventListener('resize', function() {
            if (window.innerWidth > 992 && navbar.classList.contains('active')) {
                navbar.classList.remove('active');
                menuIcon.classList.remove('bx-x');
            }
        });

        // Prevent clicks inside the navbar from closing it
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
                    if (window.innerWidth <= 992 && navbar) {
                        navbar.classList.remove('active');
                        menuIcon.classList.remove('bx-x');
                    }
                }
            }
        });
    });
    
    // Image Hover Animation Enhancement - Move inside DOMContentLoaded
    const imageItems = document.querySelectorAll('.image-placeholder img');
    if (imageItems && imageItems.length > 0) {
        imageItems.forEach(img => {
            img.addEventListener('mouseover', () => {
                img.style.transform = 'scale(1.05)';
            });
            
            img.addEventListener('mouseout', () => {
                img.style.transform = 'scale(1)';
            });
        });
    }
});