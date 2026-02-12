   // Mobile Menu Toggle
        function toggleMobileMenu() {
            const navLinks = document.getElementById('navLinks');
            const menuBtn = document.querySelector('.mobile-menu-btn i');
            
            navLinks.classList.toggle('active');
            
            if (navLinks.classList.contains('active')) {
                menuBtn.classList.remove('fa-bars');
                menuBtn.classList.add('fa-times');
                document.body.style.overflow = 'hidden';
            } else {
                menuBtn.classList.remove('fa-times');
                menuBtn.classList.add('fa-bars');
                document.body.style.overflow = 'auto';
            }
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const navLinks = document.getElementById('navLinks');
            const menuBtn = document.querySelector('.mobile-menu-btn');
            
            if (navLinks.classList.contains('active') && 
                !navLinks.contains(event.target) && 
                !menuBtn.contains(event.target)) {
                toggleMobileMenu();
            }
        });

        // FAQ Accordion
        document.querySelectorAll('.faq-question').forEach(q => {
            q.addEventListener('click', () => {
                const item = q.parentElement;
                item.classList.toggle('active');
                const icon = q.querySelector('i:last-child');
                icon.classList.toggle('fa-plus');
                icon.classList.toggle('fa-minus');
            });
        });

        // Scroll to top button
        window.onscroll = function() {
            const scrollBtn = document.getElementById('scrollTopBtn');
            if (document.body.scrollTop > 500 || document.documentElement.scrollTop > 500) {
                scrollBtn.classList.add('visible');
            } else {
                scrollBtn.classList.remove('visible');
            }
        };

        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Close mobile menu on window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                const navLinks = document.getElementById('navLinks');
                const menuBtn = document.querySelector('.mobile-menu-btn i');
                
                if (navLinks.classList.contains('active')) {
                    navLinks.classList.remove('active');
                    menuBtn.classList.remove('fa-times');
                    menuBtn.classList.add('fa-bars');
                    document.body.style.overflow = 'auto';
                }
            }
        });

        // Auto-expand first FAQ
        window.addEventListener('load', function() {
            const firstFaq = document.querySelector('.faq-item');
            if (firstFaq) {
                firstFaq.classList.add('active');
                const icon = firstFaq.querySelector('.faq-question i:last-child');
                icon.classList.remove('fa-plus');
                icon.classList.add('fa-minus');
            }
        });