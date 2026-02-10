 // Simple animations
        document.addEventListener('DOMContentLoaded', function() {
            // Add scroll animation to elements
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);
            
            // Observe elements
            document.querySelectorAll('.event-card, .club-card').forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                observer.observe(el);
            });
            
            // Mobile menu toggle (simple implementation)
            const navToggle = document.createElement('button');
            navToggle.innerHTML = '<i class="fas fa-bars"></i>';
            navToggle.style.cssText = `
                background: none;
                border: none;
                font-size: 1.5rem;
                color: var(--sxc-maroon);
                cursor: pointer;
                display: none;
            `;
            
            const navContainer = document.querySelector('.nav-container');
            const navLinks = document.querySelector('.nav-links');
            
            navContainer.appendChild(navToggle);
            
            // Check if we need mobile menu
            function checkMobileMenu() {
                if (window.innerWidth <= 768) {
                    navToggle.style.display = 'block';
                    navLinks.style.display = 'none';
                } else {
                    navToggle.style.display = 'none';
                    navLinks.style.display = 'flex';
                }
            }
            
            navToggle.addEventListener('click', function() {
                navLinks.style.display = navLinks.style.display === 'flex' ? 'none' : 'flex';
                navLinks.style.flexDirection = 'column';
                navLinks.style.position = 'absolute';
                navLinks.style.top = '100%';
                navLinks.style.left = '0';
                navLinks.style.right = '0';
                navLinks.style.background = 'white';
                navLinks.style.padding = '20px';
                navLinks.style.boxShadow = '0 10px 30px rgba(0,0,0,0.1)';
                navLinks.style.gap = '15px';
            });
            
            checkMobileMenu();
            window.addEventListener('resize', checkMobileMenu);
        });