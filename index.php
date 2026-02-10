<?php
session_start();
require_once 'backend/data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SyncSXC | Unified Campus Events Platform</title>
    <link rel="stylesheet" href="css/index.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container nav-container">
            <a href="index.php" class="logo">
                <i class="fas fa-sync-alt"></i>
                Sync<span class="logo-gold">SXC</span>
            </a>
            
            <div class="nav-links">
                <a href="index.php" class="nav-link active">Home</a>
                <a href="public/events.php" class="nav-link">Events</a>
                <a href="public/clubs.php" class="nav-link">Clubs</a>
                <a href="public/schedule.php" class="nav-link">Schedule</a>
                <a href="backend/login.php" class="btn btn-login">Login</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-grid">
                <div class="hero-content">
                    <h1>Stay in <span class="hero-highlight">Sync</span> with <span class="hero-gold">Maitighar</span></h1>
                    <p class="hero-description">
                        The centralized platform for all St. Xavier's College events, club activities, and official schedules. 
                        Never miss another campus event again.
                    </p>
                    
                    <div class="hero-buttons">
                        <a href="public/events.php" class="btn btn-primary">
                            <i class="fas fa-calendar-alt"></i> Explore Events
                        </a>
                        <a href="public/clubs.php" class="btn btn-secondary">
                            <i class="fas fa-users"></i> Browse Clubs
                        </a>
                    </div>
                    
                    <div class="hero-stats">
                        <div class="stat-item">
                            <span class="stat-number">
                                <?php
                                    $eventsCount = fetchEvents();
                                    echo  '10+';
                                ?>
                            </span> 
                            <span class="stat-label">Events</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">
                                <?php
                                    echo  '12';
                                ?>
                            </span>
                            <span class="stat-label">Clubs</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">1000+</span>
                            <span class="stat-label">Students</span>
                        </div>
                    </div>
                </div>
                
                <div class="hero-visual">
                    <div class="floating-card card-1">
                        <div class="card-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3 class="card-title">Live Updates</h3>
                        <p class="card-desc">Real-time event notifications and changes</p>
                    </div>
                    
                    <div class="floating-card card-2">
                        <div class="card-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <h3 class="card-title">Competitions</h3>
                        <p class="card-desc">Register for campus events easily</p>
                    </div>
                    
                    <div class="floating-card card-3">
                        <div class="card-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3 class="card-title">Smart Schedule</h3>
                        <p class="card-desc">Personalized event calendar</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Events Section -->
    <section class="section">
        <div class="container">
            <div class="section-title">
                <h2>Upcoming Events</h2>
                <div class="title-underline"></div>
            </div>
            
            <div class="events-grid">
                <?php
                    $featuredEvents = fetchEvents(3);
                    if ($featuredEvents && $featuredEvents->num_rows > 0):
                        while($event = $featuredEvents->fetch_assoc()):
                ?>
                <div class="event-card">
                    <div class="event-header">
                        <span class="event-club"><?php echo htmlspecialchars($event['name']); ?></span>
                        <span class="event-date"><?php echo date('M d, Y', strtotime($event['proposed_date'])); ?></span>
                    </div>
                    
                    <div class="event-body">
                        <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                        <p class="event-desc">
                            <?php 
                                $desc = htmlspecialchars($event['description']);
                                echo (strlen($desc) > 100) ? substr($desc, 0, 97) . '...' : $desc;
                            ?>
                        </p>
                    </div>
                    
                    <div class="event-footer">
                        <div class="event-venue">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo htmlspecialchars($event['venue']); ?></span>
                        </div>
                        <a href="public/events.php#event-<?php echo $event['event_id']; ?>" class="btn btn-primary" style="padding: 8px 16px; font-size: 0.85rem;">
                            View Details
                        </a>
                    </div>
                </div>
                <?php
                        endwhile;
                    else:
                ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 40px;">
                    <p style="color: var(--text-muted); font-size: 1.1rem;">No upcoming events at the moment. Check back soon!</p>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="view-all">
                <a href="public/events.php" class="btn btn-secondary">
                    <i class="fas fa-calendar-alt"></i> View All Events
                </a>
            </div>
        </div>
    </section>

    <!-- Clubs Preview Section -->
    <section class="section clubs-preview">
        <div class="container">
            <div class="section-title">
                <h2>Featured Clubs</h2>
                <div class="title-underline"></div>
            </div>
            
            <div class="clubs-grid">
                <?php
                    $featuredClubs = fetchClubs(3);
                    if ($featuredClubs && $featuredClubs->num_rows > 0):
                        while($club = $featuredClubs->fetch_assoc()):
                ?>
                <div class="club-card">
                    <div class="club-logo-wrapper">
                        <?php if($club['logo_path'] && file_exists("img/{$club['logo_path']}")): ?>
                            <img src="img/<?php echo htmlspecialchars($club['logo_path']); ?>" alt="<?php echo htmlspecialchars($club['name']); ?>" class="club-logo">
                        <?php else: ?>
                            <i class="fas fa-users" style="font-size: 2rem; color: var(--sxc-maroon);"></i>
                        <?php endif; ?>
                    </div>
                    
                    <h3 class="club-name"><?php echo htmlspecialchars($club['name']); ?></h3>
                    <p class="club-desc">
                        <?php 
                            $desc = htmlspecialchars($club['description']);
                            echo (strlen($desc) > 100) ? substr($desc, 0, 97) . '...' : $desc;
                        ?>
                    </p>
                    
                    <a href="public/clubs.php#club-<?php echo $club['club_code']; ?>" class="btn btn-primary" style="padding: 8px 20px; font-size: 0.85rem; margin-top: 10px;">
                        <i class="fas fa-eye"></i> View Club
                    </a>
                </div>
                <?php
                        endwhile;
                    else:
                ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 40px;">
                    <p style="color: var(--text-muted); font-size: 1.1rem;">Club information coming soon!</p>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="view-all" style="margin-top: 40px;">
                <a href="public/clubs.php" class="btn btn-secondary">
                    <i class="fas fa-users"></i> Explore All Clubs
                </a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Get Involved?</h2>
                <p>
                    Join thousands of SXC students who use SyncSXC to stay updated, 
                    participate in events, and make the most of their campus life.
                </p>
                
                <div class="cta-buttons">
                    <a href="public/events.php" class="btn btn-cta-primary">
                        <i class="fas fa-rocket"></i> Get Started
                    </a>
                    <a href="about.php" class="btn btn-cta-secondary">
                        <i class="fas fa-info-circle"></i> Learn More
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-col">
                    <div class="footer-logo">Sync<span class="footer-gold">SXC</span></div>
                    <p class="footer-description">
                        The ultimate platform for St. Xavier's College campus events, 
                        connecting students with opportunities and experiences.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h3 class="footer-heading">Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="/"><i class="fas fa-home"></i> Home</a></li>
                        <li><a href="public/events.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
                        <li><a href="public/clubs.php"><i class="fas fa-users"></i> Clubs</a></li>
                        <li><a href="public/schedule.php"><i class="fas fa-clock"></i> Schedule</a></li>
                        <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Admin Login</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h3 class="footer-heading">Contact</h3>
                    <ul class="footer-links">
                        <li><a href="#"><i class="fas fa-map-marker-alt"></i> St. Xavier's College, Maitighar</a></li>
                        <li><a href="mailto:info@syncsxc.edu.np"><i class="fas fa-envelope"></i> info@syncsxc.edu.np</a></li>
                        <li><a href="tel:+97711234567"><i class="fas fa-phone"></i> +977 1-1234567</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="copyright">
                &copy; <?php echo date('Y'); ?> SyncSXC. All rights reserved. | 
                St. Xavier's College, Maitighar
            </div>
        </div>
    </footer>

    <script src="js/index.js">
       
    </script>
</body>
</html>