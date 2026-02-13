    // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const navLinks = document.querySelector('.nav-links');
        
        if(mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                navLinks.classList.toggle('active');
                const icon = this.querySelector('i');
                if(icon) {
                    icon.classList.toggle('fa-bars');
                    icon.classList.toggle('fa-times');
                }
            });
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            if(navLinks && navLinks.classList.contains('active')) {
                if(!navLinks.contains(event.target) && !mobileMenuBtn.contains(event.target)) {
                    navLinks.classList.remove('active');
                    const icon = mobileMenuBtn.querySelector('i');
                    if(icon) {
                        icon.classList.add('fa-bars');
                        icon.classList.remove('fa-times');
                    }
                }
            }
        });

        // Scroll to Top
        const scrollTop = document.getElementById('scrollTop');
        
        if(scrollTop) {
            window.addEventListener('scroll', function() {
                if(window.scrollY > 300) {
                    scrollTop.classList.add('visible');
                } else {
                    scrollTop.classList.remove('visible');
                }
            });
            
            scrollTop.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }

            // delete and edit buttons
            document.querySelectorAll('.action-btn.edit').forEach(btn => {
                btn.addEventListener('click', function() {
                    const eventId = this.dataset.eventId;
                    window.location.href = `../backend/edit_event.php?event_id=${eventId}`;
                });
            });


            document.querySelectorAll('.action-btn.delete').forEach(btn => {
                btn.addEventListener('click', function() {
                    const eventId = this.dataset.eventId;
                    if(confirm('Are you sure you want to delete this event? This action cannot be undone.')) {
                        window.location.href = `../backend/delete_event.php?event_id=${eventId}`;
                    }
                });
            });

        // Close mobile menu on window resize
        window.addEventListener('resize', function() {
            if(window.innerWidth > 768) {
                if(navLinks && navLinks.classList.contains('active')) {
                    navLinks.classList.remove('active');
                    const icon = mobileMenuBtn?.querySelector('i');
                    if(icon) {
                        icon.classList.add('fa-bars');
                        icon.classList.remove('fa-times');
                    }
                }
            }
        });

        // Set min date for date input
        const dateInput = document.querySelector('input[type="date"]');
        if(dateInput) {
            const today = new Date().toISOString().split('T')[0];
            dateInput.setAttribute('min', today);
        }

        // Team size validation hint
        const teamSelect = document.querySelector('select[name="is_team_event"]');
        const minInput = document.querySelector('input[name="min_team_size"]');
        const maxInput = document.querySelector('input[name="max_team_size"]');

        if(teamSelect && minInput && maxInput) {
            teamSelect.addEventListener('change', function() {
                if(this.value === '0') {
                    minInput.value = 1;
                    maxInput.value = 1;
                    minInput.readOnly = true;
                    maxInput.readOnly = true;
                } else {
                    minInput.readOnly = false;
                    maxInput.readOnly = false;
                }
            });
        }
        