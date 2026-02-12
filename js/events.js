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

        // Filter events
        function filterEvents(filter, element) {
            const cards = document.querySelectorAll('.event-card');
            let visibleCount = 0;
            
            cards.forEach(card => {
                const status = card.dataset.status;
                const price = parseFloat(card.dataset.price);
                let shouldShow = false;
                
                switch(filter) {
                    case 'all':
                        shouldShow = true;
                        break;
                    case 'approved':
                        shouldShow = status === 'approved';
                        break;
                    case 'pending':
                        shouldShow = status === 'pending';
                        break;
                    case 'free':
                        shouldShow = price === 0;
                        break;
                }
                
                card.style.display = shouldShow ? 'flex' : 'none';
                if (shouldShow) visibleCount++;
            });
            
            // Update active tab
            document.querySelectorAll('.filter-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            element.classList.add('active');
            
            // Update event count
            const eventCountEl = document.getElementById('eventCount');
            if (eventCountEl) {
                eventCountEl.innerHTML = `<i class="fas fa-list"></i> ${visibleCount} Events`;
            }
        }

        // Search events
        function searchEvents() {
            const searchTerm = document.getElementById('eventSearch').value.toLowerCase();
            const cards = document.querySelectorAll('.event-card');
            let visibleCount = 0;
            
            cards.forEach(card => {
                const title = card.dataset.title;
                const club = card.dataset.club;
                
                if (title.includes(searchTerm) || club.includes(searchTerm)) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Update event count
            const eventCountEl = document.getElementById('eventCount');
            if (eventCountEl) {
                eventCountEl.innerHTML = `<i class="fas fa-list"></i> ${visibleCount} Events`;
            }
        }

        // Reset filters
        function resetFilters() {
            // Reset search input
            document.getElementById('eventSearch').value = '';
            
            // Show all events
            const cards = document.querySelectorAll('.event-card');
            cards.forEach(card => {
                card.style.display = 'flex';
            });
            
            // Reset active tab
            document.querySelectorAll('.filter-tab').forEach(tab => {
                tab.classList.remove('active');
                if (tab.innerHTML.includes('All Events')) {
                    tab.classList.add('active');
                }
            });
            
            // Reset event count
            const eventCountEl = document.getElementById('eventCount');
            if (eventCountEl) {
                eventCountEl.innerHTML = `<i class="fas fa-list"></i> ${cards.length} Events`;
            }
        }

        // Enter key for search
        document.getElementById('eventSearch').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchEvents();
            }
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
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

        // Handle responsive filter tabs
        function handleResize() {
            const filterTabs = document.querySelector('.filter-tabs');
            if (window.innerWidth <= 768) {
                filterTabs.style.flexWrap = 'wrap';
            } else {
                filterTabs.style.flexWrap = 'nowrap';
            }
        }

        window.addEventListener('resize', handleResize);
        handleResize();

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial event count
            const cards = document.querySelectorAll('.event-card');
            const eventCountEl = document.getElementById('eventCount');
            if (eventCountEl && cards.length > 0) {
                eventCountEl.innerHTML = `<i class="fas fa-list"></i> ${cards.length} Events`;
            }
        });