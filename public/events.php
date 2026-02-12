<?php
session_start();
require_once '../backend/data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Timeline | SyncSXC</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/events.css">
    
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
                <a href="events.php" class="nav-link active">
                    <i class="fas fa-calendar-alt"></i> Events
                </a>
                <a href="clubs.php" class="nav-link">
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
                <h1>Event Explorer</h1>
                <p>Live updates from your favorite St. Xavier's Clubs</p>
                
                <?php
                    $events = fetchEvents();
                    $totalEvents = $events ? $events->num_rows : 0;
                    $approvedCount = 0;
                    $pendingCount = 0;
                    
                    if ($events) {
                        $events->data_seek(0);
                        while($ev = $events->fetch_assoc()) {
                            if ($ev['approval_status'] == 'Approved') $approvedCount++;
                            else $pendingCount++;
                        }
                        $events->data_seek(0);
                    }
                ?>
                
                <div class="header-stats">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $totalEvents; ?>+</span>
                        <span class="stat-label">Total Events</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $approvedCount; ?></span>
                        <span class="stat-label">Approved</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $pendingCount; ?></span>
                        <span class="stat-label">Pending</span>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- Events Section -->
    <section class="events-section">
        <div class="container">
            <div class="section-header">
                <h2>
                    <i class="fas fa-calendar-alt"></i>
                    Upcoming Events
                </h2>
                <span class="event-count" id="eventCount">
                    <i class="fas fa-list"></i>
                    <?php echo $totalEvents; ?> Events
                </span>
            </div>

            <!-- Events Grid -->
            <div class="event-grid" id="eventGrid">
                <?php
                    if ($events && $events->num_rows > 0):
                        while($ev = $events->fetch_assoc()): 
                            $statusClass = ($ev['approval_status'] == 'Approved') ? 'status-approved' : 'status-pending';
                            $isTeam = ($ev['is_team_event']) ? 'Team Based' : 'Individual';
                            $priceText = ($ev['price'] > 0) ? 'Rs. ' . number_format($ev['price']) : 'FREE';
                            $teamSize = ($ev['is_team_event']) ? $ev['min_team_size'] . '-' . $ev['max_team_size'] . ' members' : 'Solo';
                ?>
                    <div class="event-card" 
                         data-status="<?php echo strtolower($ev['approval_status']); ?>"
                         data-price="<?php echo $ev['price']; ?>"
                         data-title="<?php echo strtolower(htmlspecialchars($ev['title'])); ?>"
                         data-club="<?php echo strtolower(htmlspecialchars($ev['name'])); ?>">
                        
                        <div class="event-header">
                            <span class="status-badge <?php echo $statusClass; ?>">
                                <i class="fas <?php echo ($ev['approval_status'] == 'Approved') ? 'fa-check-circle' : 'fa-clock'; ?>"></i>
                                <?php echo htmlspecialchars($ev['approval_status']); ?>
                            </span>
                            <span class="price-tag">
                                <i class="fas fa-tag"></i>
                                <?php echo $priceText; ?>
                            </span>
                        </div>

                        <div class="event-body">
                            <span class="club-badge">
                                <i class="fas fa-users"></i>
                                <?php echo htmlspecialchars($ev['name']); ?>
                            </span>
                            <h3 class="event-title"><?php echo htmlspecialchars($ev['title']); ?></h3>
                            <p class="event-desc">
                                <?php 
                                    $desc = htmlspecialchars($ev['description']);
                                    echo strlen($desc) > 120 ? substr($desc, 0, 117) . '...' : $desc;
                                ?>
                            </p>
                            
                            <div class="info-grid">
                                <div class="info-item">
                                    <i class="fa-solid fa-calendar-day"></i>
                                    <span><?php echo date('M d, Y', strtotime($ev['proposed_date'])); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <span><?php echo htmlspecialchars($ev['venue']); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="fa-solid fa-users"></i>
                                    <span><?php echo $isTeam; ?> (<?php echo $teamSize; ?>)</span>
                                </div>
                                <div class="info-item">
                                    <i class="fa-solid fa-layer-group"></i>
                                    <span><?php echo ($ev['is_multistep']) ? 'Multi-Step' : 'Single Form'; ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="event-footer">
                            <?php if(!empty($ev['rulebook_url'])): ?>
                                <a href="<?php echo htmlspecialchars($ev['rulebook_url']); ?>" class="btn btn-outline" target="_blank">
                                    <i class="fa-solid fa-book"></i> Rules
                                </a>
                            <?php else: ?>
                                <span class="btn btn-outline" style="opacity: 0.5; cursor: not-allowed;">
                                    <i class="fa-solid fa-book"></i> Rules
                                </span>
                            <?php endif; ?>
                            <a href="event-details.php?id=<?php echo $ev['event_id']; ?>" class="btn btn-main">
                                <i class="fa-solid fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                <?php 
                        endwhile;
                    else:
                ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <p>No events found at the moment.</p>
                        <p style="font-size: 0.9rem; margin-bottom: 30px;">Check back soon for exciting campus events!</p>
                        <button onclick="resetFilters()" class="btn-reset">
                            <i class="fas fa-redo-alt"></i> Refresh
                        </button>
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
                        <li><a href="../backend/login.php"><i class="fas fa-sign-in-alt"></i> Admin Login</a></li>
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

    <script src="../js/events.js">
    
    </script>
</body>
</html>