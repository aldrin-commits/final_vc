const menuIcon = document.querySelector('#menu-icon');
const navbar = document.querySelector('.navbar');

menuIcon.addEventListener('click', () => {
    navbar.classList.toggle('active');
    // Toggle between menu and close icon
    menuIcon.classList.toggle('bx-menu');
    menuIcon.classList.toggle('bx-x');
});

document.addEventListener('click', function(e) {
    if (!menuIcon.contains(e.target) && !navbar.contains(e.target) && navbar.classList.contains('active')) {
        navbar.classList.remove('active');
        // Also reset the icon when clicking outside
        if (menuIcon.classList.contains('bx-x')) {
            menuIcon.classList.remove('bx-x');
            menuIcon.classList.add('bx-menu');
        }
    }
});

// Add smooth scroll behavior for better UX
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});