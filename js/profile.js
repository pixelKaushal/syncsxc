// ===== FULLY RESPONSIVE MOBILE MENU WITH TOUCH SUPPORT =====
document.addEventListener('DOMContentLoaded', function() {
    'use strict';
    
    // ===== MOBILE MENU TOGGLE =====
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const navLinks = document.getElementById('navLinks');
    let isMenuOpen = false;
    
    if (mobileMenuBtn && navLinks) {
        // Toggle menu function
        function toggleMobileMenu(open) {
            if (open === undefined) {
                isMenuOpen = !isMenuOpen;
            } else {
                isMenuOpen = open;
            }
            
            if (isMenuOpen) {
                navLinks.classList.add('active');
                document.body.style.overflow = 'hidden'; // Prevent background scroll
                updateMenuIcon('times');
            } else {
                navLinks.classList.remove('active');
                document.body.style.overflow = ''; // Restore scroll
                updateMenuIcon('bars');
            }
        }
        
        // Update menu icon
        function updateMenuIcon(icon) {
            const iconElement = mobileMenuBtn.querySelector('i');
            if (iconElement) {
                iconElement.className = `fas fa-${icon}`;
            }
        }
        
        // Click event for menu button
        mobileMenuBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleMobileMenu();
        });
        
        // Close menu when clicking on a nav link (smooth scroll)
        navLinks.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                if (window.innerWidth <= 768) {
                    toggleMobileMenu(false);
                    
                    // Smooth scroll for anchor links
                    const href = this.getAttribute('href');
                    if (href && href.startsWith('#')) {
                        e.preventDefault();
                        const target = document.querySelector(href);
                        if (target) {
                            target.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    }
                }
            });
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 768 && isMenuOpen) {
                if (!navLinks.contains(event.target) && !mobileMenuBtn.contains(event.target)) {
                    toggleMobileMenu(false);
                }
            }
        });
        
        // Close menu on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && isMenuOpen) {
                toggleMobileMenu(false);
            }
        });
        
        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (window.innerWidth > 768) {
                    if (isMenuOpen) {
                        toggleMobileMenu(false);
                    }
                    // Reset styles
                    navLinks.removeAttribute('style');
                    document.body.style.overflow = '';
                }
            }, 250);
        });
    }
    
    // ===== TOUCH/SWIPE SUPPORT FOR MOBILE =====
    let touchStartX = 0;
    let touchEndX = 0;
    
    function handleSwipe() {
        const swipeThreshold = 100;
        if (touchEndX - touchStartX > swipeThreshold) {
            // Swipe right - open menu
            if (window.innerWidth <= 768 && !isMenuOpen) {
                toggleMobileMenu(true);
            }
        } else if (touchStartX - touchEndX > swipeThreshold) {
            // Swipe left - close menu
            if (window.innerWidth <= 768 && isMenuOpen) {
                toggleMobileMenu(false);
            }
        }
    }
    
    document.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });
    
    document.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, { passive: true });
    
    // ===== RESPONSIVE TABLE HANDLING =====
    function makeTablesResponsive() {
        const tables = document.querySelectorAll('table');
        tables.forEach(table => {
            if (!table.parentElement.classList.contains('table-responsive')) {
                const wrapper = document.createElement('div');
                wrapper.className = 'table-responsive';
                wrapper.style.overflowX = 'auto';
                wrapper.style.width = '100%';
                table.parentNode.insertBefore(wrapper, table);
                wrapper.appendChild(table);
            }
        });
    }
    makeTablesResponsive();
    
    // ===== RESPONSIVE INFO ROWS FOR MOBILE =====
    function adjustInfoRowsForMobile() {
        if (window.innerWidth <= 480) {
            document.querySelectorAll('.info-row').forEach(row => {
                const label = row.querySelector('.info-label');
                const value = row.querySelector('.info-value');
                if (label && value && !row.classList.contains('mobile-adjusted')) {
                    row.classList.add('mobile-adjusted');
                }
            });
        }
    }
    adjustInfoRowsForMobile();
    
    window.addEventListener('resize', function() {
        adjustInfoRowsForMobile();
    });
    
    // ===== SMOOTH SCROLL TO TOP =====
    const scrollTopBtn = document.getElementById('scrollTop');
    if (scrollTopBtn) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                scrollTopBtn.classList.add('visible');
            } else {
                scrollTopBtn.classList.remove('visible');
            }
        });
        
        scrollTopBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // ===== BACK TO TOP LINK =====
    const backToTopLink = document.querySelector('a[href="#top"]');
    if (backToTopLink) {
        backToTopLink.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // ===== ACTIVE NAVIGATION LINK =====
    function setActiveNavLink() {
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href && currentPath.includes(href) && href !== '#') {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    }
    setActiveNavLink();
    
    // ===== ORIENTATION CHANGE HANDLER =====
    window.addEventListener('orientationchange', function() {
        setTimeout(function() {
            if (window.innerWidth <= 768 && isMenuOpen) {
                toggleMobileMenu(false);
            }
        }, 200);
    });
    
    // ===== LOADING STATE =====
    window.addEventListener('load', function() {
        document.body.classList.add('loaded');
        
        // Remove any loading spinners if present
        const spinners = document.querySelectorAll('.spinner');
        spinners.forEach(spinner => spinner.remove());
    });
});