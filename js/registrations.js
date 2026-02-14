  document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const navLinks = document.getElementById('navLinks');
            
            if (mobileMenuBtn && navLinks) {
                mobileMenuBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    navLinks.classList.toggle('active');
                    
                    const icon = this.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('fa-bars');
                        icon.classList.toggle('fa-times');
                    }
                    
                    document.body.style.overflow = navLinks.classList.contains('active') ? 'hidden' : '';
                });
                
                // Close menu when clicking on a link
                navLinks.querySelectorAll('.nav-link').forEach(link => {
                    link.addEventListener('click', function() {
                        navLinks.classList.remove('active');
                        document.body.style.overflow = '';
                        
                        const icon = mobileMenuBtn.querySelector('i');
                        if (icon) {
                            icon.classList.add('fa-bars');
                            icon.classList.remove('fa-times');
                        }
                    });
                });
                
                // Close menu when clicking outside
                document.addEventListener('click', function(event) {
                    if (window.innerWidth <= 768) {
                        if (!navLinks.contains(event.target) && !mobileMenuBtn.contains(event.target)) {
                            navLinks.classList.remove('active');
                            document.body.style.overflow = '';
                            
                            const icon = mobileMenuBtn.querySelector('i');
                            if (icon) {
                                icon.classList.add('fa-bars');
                                icon.classList.remove('fa-times');
                            }
                        }
                    }
                });
                
                // Close menu on window resize
                window.addEventListener('resize', function() {
                    if (window.innerWidth > 768) {
                        navLinks.classList.remove('active');
                        document.body.style.overflow = '';
                        
                        const icon = mobileMenuBtn?.querySelector('i');
                        if (icon) {
                            icon.classList.add('fa-bars');
                            icon.classList.remove('fa-times');
                        }
                    }
                });
            }
        });