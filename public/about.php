<?php
session_start();
require_once '../backend/data.php';

// Fetch statistics for about page
$stats = [];

// Total users
$user_query = $conn->query("SELECT COUNT(*) as total FROM users");
$stats['total_users'] = $user_query->fetch_assoc()['total'] ?? 0;

// Total events
$event_query = $conn->query("SELECT COUNT(*) as total FROM events WHERE approval_status = 'Approved'");
$stats['total_events'] = $event_query->fetch_assoc()['total'] ?? 0;

// Total clubs
$club_query = $conn->query("SELECT COUNT(*) as total FROM clubs");
$stats['total_clubs'] = $club_query->fetch_assoc()['total'] ?? 0;

// Total registrations
$reg_query = $conn->query("SELECT COUNT(*) as total FROM registrations WHERE status != 'cancelled'");
$stats['total_registrations'] = $reg_query->fetch_assoc()['total'] ?? 0;

// Upcoming events (next 30 days)
$next_month = date('Y-m-d', strtotime('+30 days'));
$upcoming_query = $conn->prepare("
    SELECT COUNT(*) as total FROM events 
    WHERE proposed_date BETWEEN CURDATE() AND ? 
    AND approval_status = 'Approved'
");
$upcoming_query->bind_param("s", $next_month);
$upcoming_query->execute();
$stats['upcoming_events'] = $upcoming_query->get_result()->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About SyncSXC | St. Xavier's College Platform</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --sxc-maroon: #0667A4;
            --sxc-maroon-dark: #05558a;
            --sxc-maroon-light: rgba(6, 103, 164, 0.1);
            --sxc-maroon-soft: rgba(6, 103, 164, 0.05);
            --sxc-gold: #d4af37;
            --sxc-gold-dark: #b8960c;
            --sxc-gold-light: rgba(212, 175, 55, 0.1);
            --white: #ffffff;
            --bg-light: #f8f9fa;
            --bg-dark: #1a2634;
            --text-dark: #2d3436;
            --text-muted: #636e72;
            --border-light: #e9ecef;
            --shadow-sm: 0 5px 20px rgba(0,0,0,0.05);
            --shadow-md: 0 8px 30px rgba(0,0,0,0.08);
            --shadow-lg: 0 15px 40px rgba(0,0,0,0.12);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 24px;
            --radius-full: 9999px;
            --font-primary: 'Inter', sans-serif;
            --font-heading: 'Outfit', sans-serif;
        }

        body {
            font-family: var(--font-primary);
            background: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 24px;
        }

        /* ===== NAVIGATION ===== */
        .navbar {
            background: var(--white);
            box-shadow: var(--shadow-sm);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid var(--border-light);
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.6rem;
            font-weight: 800;
            text-decoration: none;
            color: var(--text-dark);
            font-family: var(--font-heading);
        }

        .logo i {
            color: var(--sxc-maroon);
            font-size: 1.8rem;
            transition: transform 0.5s ease;
        }

        .logo:hover i {
            transform: rotate(360deg) scale(1.1);
        }

        .logo-gold {
            color: var(--sxc-gold);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .nav-link {
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 500;
            padding: 0.6rem 1.2rem;
            border-radius: var(--radius-full);
            transition: all 0.2s;
        }

        .nav-link:hover {
            color: var(--sxc-maroon);
            background: var(--sxc-maroon-soft);
        }

        .nav-link.active {
            color: var(--sxc-maroon);
            background: var(--sxc-maroon-light);
            font-weight: 600;
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--sxc-maroon);
            cursor: pointer;
            padding: 10px;
            border-radius: var(--radius-full);
            transition: all 0.2s;
            z-index: 1001;
        }

        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }

            .nav-links {
                position: fixed;
                top: 70px;
                left: 0;
                right: 0;
                background: var(--white);
                flex-direction: column;
                padding: 25px;
                gap: 0.8rem;
                box-shadow: var(--shadow-lg);
                transform: translateY(-150%);
                opacity: 0;
                visibility: hidden;
                transition: all 0.4s ease;
                border-bottom: 3px solid var(--sxc-maroon);
                z-index: 999;
            }

            .nav-links.active {
                transform: translateY(0);
                opacity: 1;
                visibility: visible;
            }

            .nav-link {
                width: 100%;
                justify-content: center;
                padding: 14px;
            }
        }

        /* ===== PAGE HEADER ===== */
        .page-header {
            background: linear-gradient(135deg, var(--white), var(--bg-light));
            border-radius: var(--radius-xl);
            padding: 60px 40px;
            margin-bottom: 40px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-light);
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--sxc-maroon), var(--sxc-gold));
        }

        .page-header h1 {
            font-size: 3rem;
            font-weight: 800;
            font-family: var(--font-heading);
            color: var(--sxc-maroon);
            margin-bottom: 20px;
        }

        .page-header p {
            font-size: 1.2rem;
            color: var(--text-muted);
            max-width: 800px;
            margin: 0 auto;
        }

        .header-badge {
            display: inline-block;
            background: var(--sxc-gold-light);
            color: var(--sxc-gold-dark);
            padding: 8px 24px;
            border-radius: var(--radius-full);
            font-weight: 600;
            margin-bottom: 20px;
            border: 1px solid var(--sxc-gold);
        }

        /* ===== STATS SECTION ===== */
        .stats-section {
            margin-bottom: 60px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 25px;
        }

        .stat-card {
            background: var(--white);
            padding: 30px 25px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-light);
            text-align: center;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
            border-color: var(--sxc-gold);
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            background: var(--sxc-maroon-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .stat-icon i {
            font-size: 2rem;
            color: var(--sxc-maroon);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--sxc-maroon);
            font-family: var(--font-heading);
            line-height: 1;
            margin-bottom: 8px;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 1rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ===== MISSION SECTION ===== */
        .mission-section {
            background: linear-gradient(135deg, var(--white), var(--bg-light));
            border-radius: var(--radius-xl);
            padding: 50px;
            margin-bottom: 60px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-light);
            position: relative;
            overflow: hidden;
        }

        .mission-section::before {
            content: '"';
            position: absolute;
            bottom: -50px;
            right: 20px;
            font-size: 15rem;
            color: var(--sxc-maroon-light);
            font-family: serif;
            opacity: 0.3;
            z-index: 0;
        }

        .mission-content {
            position: relative;
            z-index: 1;
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }

        .mission-content h2 {
            font-size: 2.2rem;
            font-weight: 800;
            font-family: var(--font-heading);
            color: var(--text-dark);
            margin-bottom: 25px;
        }

        .mission-content h2 i {
            color: var(--sxc-gold);
            margin-right: 10px;
        }

        .mission-content p {
            font-size: 1.2rem;
            color: var(--text-muted);
            line-height: 1.8;
            margin-bottom: 30px;
        }

        .mission-tagline {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--sxc-maroon);
            font-style: italic;
        }

        /* ===== FEATURES SECTION ===== */
        .features-section {
            margin-bottom: 60px;
        }

        .section-title {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-title h2 {
            font-size: 2.2rem;
            font-weight: 800;
            font-family: var(--font-heading);
            color: var(--text-dark);
            margin-bottom: 15px;
        }

        .section-title .underline {
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--sxc-maroon), var(--sxc-gold));
            margin: 0 auto;
            border-radius: var(--radius-full);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .feature-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 35px 25px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-light);
            transition: all 0.3s;
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
            border-color: var(--sxc-maroon);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: var(--sxc-maroon-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            transition: all 0.3s;
        }

        .feature-card:hover .feature-icon {
            background: var(--sxc-maroon);
            transform: scale(1.1) rotate(5deg);
        }

        .feature-icon i {
            font-size: 2.2rem;
            color: var(--sxc-maroon);
            transition: all 0.3s;
        }

        .feature-card:hover .feature-icon i {
            color: var(--white);
        }

        .feature-card h3 {
            font-size: 1.4rem;
            font-weight: 700;
            font-family: var(--font-heading);
            margin-bottom: 15px;
            color: var(--text-dark);
        }

        .feature-card p {
            color: var(--text-muted);
            line-height: 1.7;
        }

        /* ===== HOW IT WORKS SECTION ===== */
        .how-it-works {
            margin-bottom: 60px;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .step-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 30px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-light);
            position: relative;
            text-align: center;
        }

        .step-number {
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 50px;
            background: var(--sxc-gold);
            color: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 800;
            font-family: var(--font-heading);
            box-shadow: var(--shadow-md);
            border: 3px solid var(--white);
        }

        .step-icon {
            width: 70px;
            height: 70px;
            background: var(--sxc-maroon-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px auto 20px;
        }

        .step-icon i {
            font-size: 2rem;
            color: var(--sxc-maroon);
        }

        .step-card h3 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--text-dark);
        }

        .step-card p {
            color: var(--text-muted);
        }

        /* ===== TEAM SECTION ===== */
        .team-section {
            margin-bottom: 60px;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .team-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-light);
            transition: all 0.3s;
        }

        .team-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
        }

        .team-avatar {
            height: 150px;
            background: linear-gradient(135deg, var(--sxc-maroon-light), var(--sxc-gold-light));
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 4px solid var(--sxc-gold);
        }

        .team-avatar i {
            font-size: 4rem;
            color: var(--sxc-maroon);
        }

        .team-info {
            padding: 25px;
            text-align: center;
        }

        .team-info h3 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 5px;
            color: var(--text-dark);
        }

        .team-info .role {
            color: var(--sxc-gold-dark);
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .team-info p {
            color: var(--text-muted);
            margin-bottom: 20px;
            font-size: 0.95rem;
        }

        .team-social {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .team-social a {
            width: 36px;
            height: 36px;
            background: var(--bg-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--sxc-maroon);
            transition: all 0.3s;
        }

        .team-social a:hover {
            background: var(--sxc-maroon);
            color: var(--white);
            transform: translateY(-3px);
        }

        /* ===== FAQ SECTION ===== */
        .faq-section {
            margin-bottom: 60px;
        }

        .faq-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        .faq-item {
            background: var(--white);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-light);
        }

        .faq-question {
            padding: 20px 25px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: var(--text-dark);
            transition: all 0.3s;
        }

        .faq-question:hover {
            background: var(--sxc-maroon-soft);
        }

        .faq-question i {
            color: var(--sxc-gold);
            transition: transform 0.3s;
        }

        .faq-item.active .faq-question i {
            transform: rotate(180deg);
        }

        .faq-answer {
            padding: 0 25px;
            max-height: 0;
            overflow: hidden;
            transition: all 0.4s ease;
            color: var(--text-muted);
            border-top: 1px solid transparent;
        }

        .faq-item.active .faq-answer {
            padding: 0 25px 25px;
            max-height: 500px;
            border-top-color: var(--border-light);
        }

        /* ===== CONTACT SECTION ===== */
        .contact-section {
            background: linear-gradient(135deg, var(--sxc-maroon), var(--sxc-maroon-dark));
            border-radius: var(--radius-xl);
            padding: 50px;
            color: var(--white);
            text-align: center;
            margin-bottom: 40px;
        }

        .contact-section h2 {
            font-size: 2rem;
            font-weight: 800;
            font-family: var(--font-heading);
            margin-bottom: 20px;
        }

        .contact-section p {
            font-size: 1.1rem;
            margin-bottom: 30px;
            opacity: 0.9;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 800px;
            margin: 0 auto;
        }

        .contact-item {
            background: rgba(255,255,255,0.1);
            padding: 25px;
            border-radius: var(--radius-lg);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s;
        }

        .contact-item:hover {
            transform: translateY(-5px);
            background: rgba(255,255,255,0.15);
        }

        .contact-item i {
            font-size: 2rem;
            color: var(--sxc-gold);
            margin-bottom: 15px;
        }

        .contact-item h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .contact-item a {
            color: var(--white);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            border-bottom: 1px solid transparent;
        }

        .contact-item a:hover {
            border-bottom-color: var(--sxc-gold);
        }

        /* ===== CTA SECTION ===== */
        .cta-section {
            background: var(--white);
            border-radius: var(--radius-xl);
            padding: 50px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-light);
            text-align: center;
            margin-bottom: 40px;
        }

        .cta-section h2 {
            font-size: 2rem;
            font-weight: 800;
            font-family: var(--font-heading);
            color: var(--text-dark);
            margin-bottom: 15px;
        }

        .cta-section p {
            color: var(--text-muted);
            font-size: 1.1rem;
            margin-bottom: 30px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 32px;
            border-radius: var(--radius-full);
            font-weight: 700;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--sxc-maroon);
            color: var(--white);
            box-shadow: 0 8px 20px rgba(6, 103, 164, 0.2);
        }

        .btn-primary:hover {
            background: var(--sxc-maroon-dark);
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(6, 103, 164, 0.3);
        }

        .btn-secondary {
            background: var(--white);
            color: var(--sxc-maroon);
            border: 2px solid var(--sxc-maroon);
        }

        .btn-secondary:hover {
            background: var(--sxc-maroon-light);
            transform: translateY(-3px);
        }

        /* ===== FOOTER ===== */
        .footer {
            background: linear-gradient(145deg, #1a2634, #1e2a36);
            color: white;
            padding: 30px 0;
            margin-top: 60px;
        }

        .footer-content {
            text-align: center;
            color: rgba(255,255,255,0.7);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 2.2rem;
            }
            
            .page-header p {
                font-size: 1rem;
            }
            
            .mission-section {
                padding: 30px 20px;
            }
            
            .mission-content h2 {
                font-size: 1.8rem;
            }
            
            .mission-content p {
                font-size: 1rem;
            }
            
            .contact-section {
                padding: 30px 20px;
            }
            
            .cta-section {
                padding: 30px 20px;
            }
            
            .cta-buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="../index.php" class="logo">
                <i class="fas fa-sync-alt"></i>
                Sync<span class="logo-gold">SXC</span>
            </a>
            
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="nav-links" id="navLinks">
                <a href="../index.php" class="nav-link">Home</a>
                <a href="events.php" class="nav-link">Events</a>
                <a href="clubs.php" class="nav-link">Clubs</a>
                <a href="schedule.php" class="nav-link">Schedule</a>
                <a href="about.php" class="nav-link active">About</a>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="../backend/login.php" class="nav-link">Login</a>
                <?php else: ?>
                    <a href="profile.php" class="nav-link">Profile</a>
                    <a href="../backend/logout.php" class="nav-link">Logout</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <span class="header-badge">
                <i class="fas fa-info-circle"></i> About the Platform
            </span>
            <h1>Welcome to SyncSXC</h1>
            <p>Your centralized hub for St. Xavier's College events, club activities, and campus engagement</p>
        </div>

        <!-- Stats Section -->
        <div class="stats-section">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number"><?php echo number_format($stats['total_users']); ?>+</div>
                    <div class="stat-label">Active Users</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-number"><?php echo number_format($stats['total_events']); ?>+</div>
                    <div class="stat-label">Events Hosted</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-flag"></i>
                    </div>
                    <div class="stat-number"><?php echo number_format($stats['total_clubs']); ?></div>
                    <div class="stat-label">Active Clubs</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <div class="stat-number"><?php echo number_format($stats['total_registrations']); ?>+</div>
                    <div class="stat-label">Registrations</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <div class="stat-number"><?php echo $stats['upcoming_events']; ?></div>
                    <div class="stat-label">Upcoming Events</div>
                </div>
            </div>
        </div>

        <!-- Mission Section -->
        <div class="mission-section">
            <div class="mission-content">
                <h2><i class="fas fa-bullseye"></i> Our Mission</h2>
                <p>SyncSXC was created to bridge the gap between St. Xavier's College students and the vibrant campus life that makes our institution special. We believe that every student should have equal access to discover, participate in, and contribute to the rich tapestry of events, clubs, and activities that define the Xavierian experience.</p>
                <p class="mission-tagline">"Stay in Sync with Your Campus Community"</p>
            </div>
        </div>

        <!-- Features Section -->
        <div class="features-section">
            <div class="section-title">
                <h2>Why Choose SyncSXC?</h2>
                <div class="underline"></div>
            </div>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3>Real-Time Updates</h3>
                    <p>Get instant notifications about event changes, new announcements, and important deadlines. Never miss out on campus activities again.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Club Management</h3>
                    <p>Powerful tools for club executives to manage members, create events, track registrations, and engage with their community.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3>Smart Scheduling</h3>
                    <p>Personalized event calendar that shows you relevant activities based on your interests and club affiliations.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Secure Authentication</h3>
                    <p>Institutional-grade security with @sxc.edu.np email verification and role-based access control for different user types.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3>Team Registration</h3>
                    <p>Easily register teams for group events with our dynamic team member management system and duplicate prevention.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Analytics Dashboard</h3>
                    <p>Comprehensive statistics for club admins to track participation, payments, and engagement metrics.</p>
                </div>
            </div>
        </div>

        <!-- How It Works Section -->
        <div class="how-it-works">
            <div class="section-title">
                <h2>How SyncSXC Works</h2>
                <div class="underline"></div>
            </div>
            
            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <div class="step-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h3>Sign Up</h3>
                    <p>Create your account using your official @sxc.edu.np email address. Set up a recovery email for account security.</p>
                </div>
                
                <div class="step-card">
                    <div class="step-number">2</div>
                    <div class="step-icon">
                        <i class="fas fa-compass"></i>
                    </div>
                    <h3>Discover</h3>
                    <p>Browse upcoming events, explore club directories, and find activities that match your interests.</p>
                </div>
                
                <div class="step-card">
                    <div class="step-number">3</div>
                    <div class="step-icon">
                        <i class="fas fa-pen-to-square"></i>
                    </div>
                    <h3>Register</h3>
                    <p>Sign up for individual events or register as a team. Track all your registrations in one place.</p>
                </div>
                
                <div class="step-card">
                    <div class="step-number">4</div>
                    <div class="step-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3>Participate</h3>
                    <p>Attend events, get checked in, and build your campus engagement portfolio.</p>
                </div>
            </div>
        </div>

        <!-- Team Section -->
        <div class="team-section">
            <div class="section-title">
                <h2>Meet the Team</h2>
                <div class="underline"></div>
            </div>
            
            <div class="team-grid">
                <div class="team-card">
                    <div class="team-avatar">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="team-info">
                        <h3>Kaushal Gautam</h3>
                        <div class="role">Student (2024-2026)</div>
                        <p>This was made as final year project of kaushal gautam</p>
                        <div class="team-social">
                            <a href="https://www.linkedin.com/in/kaushal-gautam-383401337/"><i class="fab fa-linkedin-in"></i></a>
                            <a href="mailto:noreply.lockedin@gmail.com"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="faq-section">
            <div class="section-title">
                <h2>Frequently Asked Questions</h2>
                <div class="underline"></div>
            </div>
            
            <div class="faq-grid">
                <div class="faq-item active">
                    <div class="faq-question">
                        <span><i class="fas fa-question-circle" style="color: var(--sxc-gold); margin-right: 10px;"></i> Who can use SyncSXC?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>SyncSXC is exclusively for St. Xavier's College students, faculty, and staff. All users must have a valid @sxc.edu.np email address to register. Club executives use their official club emails for admin access.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span><i class="fas fa-question-circle" style="color: var(--sxc-gold); margin-right: 10px;"></i> How do I register for an event?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Browse the Events page, click on any event to view details, and hit the "Register" button. For team events, you'll be prompted to add team members using their @sxc.edu.np emails.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span><i class="fas fa-question-circle" style="color: var(--sxc-gold); margin-right: 10px;"></i> What's the difference between individual and team events?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Individual events are for single participants. Team events allow you to register with multiple members. The event page will show the required team size range, and you can add members dynamically during registration.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span><i class="fas fa-question-circle" style="color: var(--sxc-gold); margin-right: 10px;"></i> How do I become a club admin?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Club admins are designated by the Student Council or faculty advisors. If you're a club executive, use your official club email to sign in, and admin privileges will be automatically granted.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span><i class="fas fa-question-circle" style="color: var(--sxc-gold); margin-right: 10px;"></i> Is there a mobile app?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Currently, SyncSXC is a web-based platform optimized for all devices. A mobile app is planned for future development to enhance accessibility and push notifications.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span><i class="fas fa-question-circle" style="color: var(--sxc-gold); margin-right: 10px;"></i> How are payments handled?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Payments for events are tracked through the platform. Free events are automatically marked as paid. For paid events, club admins can approve payments manually. Online payment integration is planned for future releases.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Section -->
        <div class="contact-section">
            <h2>Get in Touch</h2>
            <p>Have questions, suggestions, or need assistance? Reach out to us through any of these channels.</p>
            
            <div class="contact-grid">
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3>Visit Us</h3>
                    <p>St. Xavier's College<br>Maitighar, Kathmandu<br>Nepal</p>
                </div>
                
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <h3>Email</h3>
                    <p><a href="mailto:neb@sxc.edu.np">neb@sxc.edu.np</a></p>
                    <p><a href="mailto:noreply.lockedin@gmail.com">noreply.lockedin@gmail.com</a></p>
                </div>
                
                <div class="contact-item">
                    <i class="fas fa-phone-alt"></i>
                    <h3>Call</h3>
                    <p><a href="tel:+977015321365">+977-01-5321365, 5344636</a></p>
                    <p>Mon-Fri, 9:00 AM - 5:00 PM</p>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="cta-section">
            <h2>Ready to Get Started?</h2>
            <p>Join thousands of SXC students who are already using SyncSXC to discover, register, and participate in campus events.</p>
            
            <div class="cta-buttons">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="../backend/signup.php" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Sign Up Now
                    </a>
                    <a href="../backend/login.php" class="btn btn-secondary">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                <?php else: ?>
                    <a href="events.php" class="btn btn-primary">
                        <i class="fas fa-calendar-alt"></i> Browse Events
                    </a>
                    <a href="clubs.php" class="btn btn-secondary">
                        <i class="fas fa-users"></i> Explore Clubs
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <p>&copy; <?php echo date('Y'); ?> SyncSXC. All rights reserved. | St. Xavier's College, Maitighar</p>
            </div>
        </div>
    </footer>

    <!-- Mobile Menu JavaScript -->
    <script>
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
                });
            }
            
            // FAQ Accordion
            document.querySelectorAll('.faq-question').forEach(question => {
                question.addEventListener('click', function() {
                    const item = this.parentElement;
                    item.classList.toggle('active');
                });
            });
        });
    </script>
</body>
</html>