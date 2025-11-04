// Mobile Menu Functionality
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileSidebar = document.getElementById('mobile-sidebar');
    const mobileSidebarOverlay = document.getElementById('mobile-sidebar-overlay');
    const mobileSidebarClose = document.getElementById('mobile-sidebar-close');
    
    // Mobile menu toggle
    if (mobileMenuButton && mobileSidebar) {
        mobileMenuButton.addEventListener('click', function() {
            mobileSidebar.classList.add('active');
            mobileSidebarOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }
    
    // Close mobile menu
    if (mobileSidebarClose) {
        mobileSidebarClose.addEventListener('click', closeMobileMenu);
    }
    
    // Close menu when clicking on overlay
    if (mobileSidebarOverlay) {
        mobileSidebarOverlay.addEventListener('click', closeMobileMenu);
    }
    
    // Close menu function
    function closeMobileMenu() {
        mobileSidebar.classList.remove('active');
        mobileSidebarOverlay.classList.remove('active');
        document.body.style.overflow = '';
        
        // Close all accordions when menu closes
        const accordionContents = document.querySelectorAll('.mobile-accordion-content');
        const accordionButtons = document.querySelectorAll('.mobile-accordion-btn');
        
        accordionContents.forEach(content => {
            content.classList.remove('active');
            content.style.maxHeight = '0';
        });
        
        accordionButtons.forEach(button => {
            button.classList.remove('active');
        });
    }
    
    // Mobile accordion functionality
    const accordionButtons = document.querySelectorAll('.mobile-accordion-btn');
    
    accordionButtons.forEach(button => {
        button.addEventListener('click', function() {
            const content = this.nextElementSibling;
            const isActive = this.classList.contains('active');
            
            // Close all other accordions
            accordionButtons.forEach(otherBtn => {
                if (otherBtn !== this) {
                    otherBtn.classList.remove('active');
                    otherBtn.nextElementSibling.classList.remove('active');
                    otherBtn.nextElementSibling.style.maxHeight = '0';
                }
            });
            
            // Toggle current accordion
            if (!isActive) {
                this.classList.add('active');
                content.classList.add('active');
                content.style.maxHeight = content.scrollHeight + 'px';
            } else {
                this.classList.remove('active');
                content.classList.remove('active');
                content.style.maxHeight = '0';
            }
        });
    });
    
    // Close mobile menu when clicking on nav items (except accordion buttons)
    const mobileNavItems = document.querySelectorAll('.mobile-nav-item, .mobile-subnav-item');
    mobileNavItems.forEach(item => {
        item.addEventListener('click', function(e) {
            if (!this.classList.contains('mobile-accordion-btn')) {
                setTimeout(closeMobileMenu, 300);
            }
        });
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 1024) {
            closeMobileMenu();
        }
    });
    
    // Escape key to close mobile menu
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeMobileMenu();
        }
    });
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                closeMobileMenu();
            }
        });
    });
});

