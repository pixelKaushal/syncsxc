<?php
session_start();
require_once '../backend/data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official SXC Clubs | SyncSXC</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/clubs.css">
    
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container nav-container">
            <a href="../index.php" class="logo">
                <i class="fas fa-sync-alt"></i>
                Sync<span class="logo-gold">SXC</span>
            </a>
            
            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="nav-links" id="navLinks">
                <a href="../index.php" class="nav-link">
                    <i class="fas fa-home"></i> Home
                </a>
                <a href="events.php" class="nav-link">
                    <i class="fas fa-calendar-alt"></i> Events
                </a>
                <a href="clubs.php" class="nav-link active">
                    <i class="fas fa-users"></i> Clubs
                </a>
                <a href="schedule.php" class="nav-link">
                    <i class="fas fa-clock"></i> Schedule
                </a>
                <?php if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email'])): ?>
                    <a href="../backend/login.php" class="nav-link">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                <?php else: ?>
                    <a href="../backend/logout.php" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="page-header-content">
                <h1>Official SXC Clubs</h1>
                <p>Discover and connect with St. Xavier's vibrant student clubs and organizations</p>
            </div>
        </div>
    </section>



    <!-- Clubs Section -->
    <section class="clubs-section">
        <div class="container">
            <div class="section-header">
                <h2>
                    <i class="fas fa-users"></i>
                    All Clubs
                </h2>
                <span class="club-count" id="clubCount">
                    <i class="fas fa-list"></i>
                    <?php
                        $clubs = fetchClubs();
                        $totalClubs = $clubs ? $clubs->num_rows : 0;
                        echo $totalClubs;
                    ?> Clubs
                </span>
            </div>

            <!-- Clubs Grid -->
            <div class="club-grid" id="clubGrid">
                <?php
                    if ($clubs && $clubs->num_rows > 0):
                        while($club = $clubs->fetch_assoc()): 
                ?>
                    <div class="club-card" 
                         data-name="<?php echo strtolower(htmlspecialchars($club['name'])); ?>"
                         data-code="<?php echo strtolower(htmlspecialchars($club['club_code'])); ?>">
                        
                        <div class="logo-wrapper">
                            <?php if($club['logo_path'] && file_exists("../img/{$club['logo_path']}")): ?>
                                <img src="../img/<?php echo htmlspecialchars($club['logo_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($club['name']); ?>" 
                                     class="club-logo"
                                     onerror="this.onerror=null; this.src='../img/default-club.png';">
                            <?php else: ?>
                                <i class="fas fa-users" style="font-size: 3rem; color: var(--sxc-maroon);"></i>
                            <?php endif; ?>
                        </div>
                        
                        <span class="club-code-tag">
                            <i class="fas fa-tag"></i>
                            <?php echo htmlspecialchars($club['club_code']); ?>
                        </span>
                        
                        <h3 class="club-name"><?php echo htmlspecialchars($club['name']); ?></h3>
                        <p class="club-desc">
                            <?php 
                                $desc = htmlspecialchars($club['description']);
                                echo (strlen($desc) > 120) ? substr($desc, 0, 117) . '...' : $desc;
                            ?>
                        </p>

                        <div class="club-footer">
                            <a href="mailto:<?php echo htmlspecialchars($club['email']); ?>" class="club-email">
                                <i class="fa-regular fa-envelope"></i>
                                <?php echo htmlspecialchars($club['email']); ?>
                            </a>
                        </div>
                    </div>
                <?php 
                        endwhile;
                    else:
                ?>
                    <div class="empty-state">
                        <i class="fas fa-users-slash"></i>
                        <p>No clubs found in the database.</p>
                        <p style="font-size: 0.9rem; margin-top: 10px;">Check back soon for club listings!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-col">
                    <div class="footer-logo">
                        <i class="fas fa-sync-alt"></i>
                        Sync<span class="footer-gold">SXC</span>
                    </div>
                    <p class="footer-description">
                        The ultimate platform for St. Xavier's College campus events, 
                        connecting students with opportunities and experiences since 2024.
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
                        <li><a href="../index.php"><i class="fas fa-home"></i> Home</a></li>
                        <li><a href="events.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
                        <li><a href="clubs.php"><i class="fas fa-users"></i> Clubs</a></li>
                        <li><a href="schedule.php"><i class="fas fa-clock"></i> Schedule</a></li>
                        <li><a href="../about.php"><i class="fas fa-info-circle"></i> About</a></li>
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
                <p>&copy; <?php echo date('Y'); ?> SyncSXC. All rights reserved. | Made with <i class="fas fa-heart"></i> for SXC Community</p>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button class="scroll-top" onclick="scrollToTop()" id="scrollTopBtn">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script src="../js/clubs.js">
    </script>
    <script> 
               document.getElementById('clubSearch')?.addEventListener('input', function(e) {
            if (e.target.value === '') {
                const cards = document.querySelectorAll('.club-card');
                cards.forEach(card => {
                    card.style.display = 'flex';
                });
                
                <?php
                    $clubs = fetchClubs();
                    $totalClubs = $clubs ? $clubs->num_rows : 0;
                ?>
                document.getElementById('clubCount').innerHTML = `<i class="fas fa-list"></i> ${<?php echo $totalClubs; ?>} Clubs`;
            }
        });
    </script>
</body>
</html>