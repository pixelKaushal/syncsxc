<?php
session_start();
require_once '../backend/data.php'; // For potential footer stats
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Institutional Protocol | SyncSXC</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/terms.css">
    <link rel="stylesheet" href="../css/global.css">
   
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
                <a href="clubs.php" class="nav-link">
                    <i class="fas fa-users"></i> Clubs
                </a>
                <a href="schedule.php" class="nav-link">
                    <i class="fas fa-clock"></i> Schedule
                </a>
                <a href="about.php" class="nav-link active">
                    <i class="fas fa-shield-alt"></i> Protocol
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
                <span class="page-badge">
                    <i class="fas fa-shield-alt"></i> Institutional Governance
                </span>
                <h1>Institutional Terms</h1>
                <p>SyncSXC Access Control & Governance Protocol</p>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container">
        <div class="protocol-card">
            <!-- Important Note -->
            <div class="important-note">
                <i class="fa-solid fa-envelope-circle-check"></i>
                <div>
                    <strong style="font-size: 1.1rem;">🔐 Automated Notification Clause</strong>
                    <p style="margin-top: 10px; color: #856404;">By utilizing SyncSXC, you provide explicit consent for the system to transmit security alerts, administrative updates, and recovery protocols to your <strong style="color: #7a5c00;">Recovery Email Address</strong>.</p>
                </div>
            </div>

            <!-- Legal Sections -->
            <section class="legal-section">
                <h2>
                    <i class="fas fa-sitemap"></i>
                    1. Access Hierarchy & Authentication
                </h2>
                <p>SyncSXC operates on a dual-tier authentication model designed to maintain the sanctity of college event data:</p>
                <ul>
                    <li>
                        <span style="flex: 1;"><strong>Executive Access</strong> <span class="role-tag tag-executive"><i class="fas fa-crown"></i> Admin</span></span>
                        <span style="color: var(--text-muted);">Official Club Emails (<code>clubname@sxc.edu.np</code>) are pre-registered within the core system. Executives do not require a Sign-Up process and may proceed directly to Sign-In.</span>
                    </li>
                    <li>
                        <span style="flex: 1;"><strong>Student Access</strong> <span class="role-tag tag-student"><i class="fas fa-user-graduate"></i> Viewer</span></span>
                        <span style="color: var(--text-muted);">Regular students are required to undergo a one-time Sign-Up process using their individual <code>@sxc.edu.np</code> institutional account.</span>
                    </li>
                </ul>

                <h2>
                    <i class="fas fa-scale-balanced"></i>
                    2. Governance & Ownership
                </h2>
                <p>SyncSXC is a structured repository for St. Xavier's College, Maitighar. Clubs are permanent institutional entities; they cannot be created, deleted, or altered by users. Information management is strictly reserved for appointed Club Executives.</p>

                <h2>
                    <i class="fas fa-shield-haltered"></i>
                    3. Data Usage & Security
                </h2>
                <p>We enforce a strict <strong style="color: var(--sxc-maroon);">Institutional-Only</strong> data policy. Attempting to use personal non-SXC domains for primary access is a violation of our security protocol and will result in an automatic account lockout.</p>
            </section>

            <!-- FAQ Section -->
            <section class="faq-section">
                <h2>
                    <i class="fas fa-circle-question"></i>
                    Comprehensive FAQ
                </h2>
                
                <div class="faq-grid">
                    <div class="faq-item">
                        <div class="faq-question">
                            <span><i class="fas fa-user-tie" style="color: var(--sxc-gold); margin-right: 12px;"></i> I am a Club Executive. Do I need to sign up?</span>
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="faq-answer">
                            Yes, even though you're an executive, you still need to sign up to create your personalized student profile. You can access admin powers through your club email during the sign-in process.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <span><i class="fas fa-envelope" style="color: var(--sxc-gold); margin-right: 12px;"></i> What happens if I try to sign up with a Gmail account?</span>
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="faq-answer">
                            The system will reject the request. SyncSXC is built exclusively for the Xavierian community. Only <code>@sxc.edu.np</code> domains are recognized by our authentication firewall.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <span><i class="fas fa-users-between-lines" style="color: var(--sxc-gold); margin-right: 12px;"></i> Can I manage multiple clubs with one email?</span>
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="faq-answer">
                            No. Each administrative session is tied to a specific Club Email. This ensures that actions taken (adding events, updating descriptions) are logged to the correct club entity.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <span><i class="fas fa-envelope-open-text" style="color: var(--sxc-gold); margin-right: 12px;"></i> Is the Recovery Email mandatory?</span>
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="faq-answer">
                            Yes. Because institutional emails can sometimes have strict spam filters, a secondary recovery email ensures you never lose access to your account during critical event periods like SET or Sports Week.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <span><i class="fas fa-clock" style="color: var(--sxc-gold); margin-right: 12px;"></i> Who moderates the events added by Executives?</span>
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="faq-answer">
                            All events added by Club Executives enter a "Pending" state. They are reviewed by the Master Admin and College Council before being published to the student-facing timeline.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Back Link -->
            <div class="back-link">
                <a href="../index.php">
                    <i class="fas fa-arrow-left"></i>
                    Return to SyncSXC Hub
                </a>
            </div>
        </div>
    </div>

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
                        <a href="https://www.instagram.com/pixelkaushal/" target="_blank" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="https://www.linkedin.com/in/kaushal-gautam-383401337/" target="_blank" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h3 class="footer-heading">Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="../index.php"><i class="fas fa-home"></i> Home</a></li>
                        <li><a href="../public/events.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
                        <li><a href="../public/clubs.php"><i class="fas fa-users"></i> Clubs</a></li>
                        <li><a href="../public/schedule.php"><i class="fas fa-clock"></i> Schedule</a></li>
                        <li><a href="../public/about.php"><i class="fas fa-info-circle"></i> About</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h3 class="footer-heading">Contact</h3>
                    <ul class="footer-links">
                        <li><a href="#"><i class="fas fa-map-marker-alt"></i> St. Xavier's College, Maitighar</a></li>
                        <li><a href="mailto:neb@sxc.edu.np"><i class="fas fa-envelope"></i> neb@sxc.edu.np</a></li>
                        <li><a href="tel:+977015321365"><i class="fas fa-phone"></i> +977-01-5321365, 5344636</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; <?php echo date('Y'); ?> SyncSXC. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button class="scroll-top" onclick="scrollToTop()" id="scrollTopBtn">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script src="../js/terms.js">
    </script>
</body>
</html>