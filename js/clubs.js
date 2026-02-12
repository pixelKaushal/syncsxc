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

        // Search clubs
        function searchClubs() {
            const searchTerm = document.getElementById('clubSearch').value.toLowerCase();
            const cards = document.querySelectorAll('.club-card');
            let visibleCount = 0;
            
            cards.forEach(card => {
                const name = card.dataset.name;
                const code = card.dataset.code;
                
                if (name.includes(searchTerm) || code.includes(searchTerm)) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Update club count
            document.getElementById('clubCount').innerHTML = `<i class="fas fa-list"></i> ${visibleCount} Clubs`;
        }

        // Enter key for search
        document.getElementById('clubSearch')?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchClubs();
            }
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