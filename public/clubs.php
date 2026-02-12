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
    
    <style>
        /* ===== GLOBAL DESIGN SYSTEM ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            /* Primary Colors */
            --sxc-maroon: #0667A4;
            --sxc-maroon-dark: #05558a;
            --sxc-maroon-light: rgba(6, 103, 164, 0.1);
            --sxc-maroon-soft: rgba(6, 103, 164, 0.05);
            
            /* Gold Accents */
            --sxc-gold: #d4af37;
            --sxc-gold-dark: #b8960c;
            --sxc-gold-light: rgba(212, 175, 55, 0.1);
            --sxc-gold-soft: rgba(212, 175, 55, 0.05);
            
            /* Neutral Colors */
            --white: #ffffff;
            --bg-light: #f8f9fa;
            --bg-dark: #1a2634;
            --text-dark: #2d3436;
            --text-muted: #636e72;
            --text-light: #7f8c8d;
            --border-light: #e9ecef;
            --border-dark: #dee2e6;
            
            /* Shadows */
            --shadow-sm: 0 5px 20px rgba(0,0,0,0.05);
            --shadow-md: 0 8px 30px rgba(0,0,0,0.08);
            --shadow-lg: 0 15px 40px rgba(0,0,0,0.12);
            --shadow-hover: 0 20px 50px rgba(6, 103, 164, 0.15);
            
            /* Transitions */
            --transition-fast: all 0.2s ease;
            --transition-base: all 0.3s ease;
            --transition-slow: all 0.5s ease;
            
            /* Border Radius */
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 24px;
            --radius-full: 9999px;
            
            /* Font Families */
            --font-primary: 'Inter', sans-serif;
            --font-heading: 'Outfit', sans-serif;
            
            /* Container Width */
            --container-max: 1280px;
            --container-padding: 24px;
        }

        body {
            font-family: var(--font-primary);
            background: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
            overflow-x: hidden;
        }

        .container {
            max-width: var(--container-max);
            margin: 0 auto;
            padding: 0 var(--container-padding);
        }

        /* ===== HEADER & NAVIGATION ===== */
        .navbar {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
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
            transition: var(--transition-base);
        }

        .logo i {
            color: var(--sxc-maroon);
            font-size: 1.8rem;
            transition: var(--transition-slow);
        }

        .logo:hover i {
            transform: rotate(180deg) scale(1.1);
        }

        .logo-gold {
            color: var(--sxc-gold);
            position: relative;
        }

        .logo-gold::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, var(--sxc-gold), transparent);
            transform: scaleX(0);
            transform-origin: left;
            transition: var(--transition-base);
        }

        .logo:hover .logo-gold::after {
            transform: scaleX(1);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .nav-link {
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 500;
            font-size: 0.95rem;
            padding: 0.6rem 1.2rem;
            border-radius: var(--radius-full);
            transition: var(--transition-base);
            display: flex;
            align-items: center;
            gap: 8px;
            position: relative;
        }

        .nav-link i {
            font-size: 0.9rem;
            transition: var(--transition-base);
        }

        .nav-link:hover {
            color: var(--sxc-maroon);
            background: var(--sxc-maroon-soft);
            transform: translateY(-2px);
        }

        .nav-link:hover i {
            transform: scale(1.1);
        }

        .nav-link.active {
            color: var(--sxc-maroon);
            background: var(--sxc-maroon-light);
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(6, 103, 164, 0.1);
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 3px;
            background: var(--sxc-maroon);
            border-radius: var(--radius-full);
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { width: 0; opacity: 0; }
            to { width: 20px; opacity: 1; }
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--sxc-maroon);
            cursor: pointer;
            padding: 0.5rem;
            transition: var(--transition-base);
        }

        .mobile-menu-btn:hover {
            transform: scale(1.1);
        }

        /* ===== PAGE HEADER ===== */
        .page-header {
            background: linear-gradient(135deg, var(--white) 0%, var(--bg-light) 100%);
            padding: 60px 0;
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid var(--border-light);
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(6, 103, 164, 0.03) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 0;
        }

        .page-header::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.03) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 0;
        }

        .page-header-content {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
        }

        .page-header h1 {
            font-size: 3rem;
            font-weight: 800;
            font-family: var(--font-heading);
            color: var(--sxc-maroon);
            margin-bottom: 15px;
            animation: fadeInUp 1s ease;
        }

        .page-header p {
            font-size: 1.1rem;
            color: var(--text-muted);
            margin-bottom: 20px;
            animation: fadeInUp 1s ease 0.1s both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ===== SEARCH SECTION ===== */
        .search-section {
            background: var(--white);
            padding: 30px 0;
            border-bottom: 1px solid var(--border-light);
            position: sticky;
            top: 80px;
            z-index: 99;
            box-shadow: var(--shadow-sm);
        }

        .search-bar {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            max-width: 600px;
            margin: 0 auto;
        }

        .search-box {
            display: flex;
            align-items: center;
            background: var(--white);
            border-radius: var(--radius-full);
            padding: 5px;
            border: 1px solid var(--border-light);
            box-shadow: var(--shadow-sm);
            transition: var(--transition-base);
            width: 100%;
        }

        .search-box:focus-within {
            border-color: var(--sxc-maroon);
            box-shadow: 0 0 0 3px rgba(6, 103, 164, 0.1);
        }

        .search-box input {
            border: none;
            padding: 12px 24px;
            font-size: 0.95rem;
            width: 100%;
            outline: none;
            background: transparent;
            border-radius: var(--radius-full);
        }

        .search-box button {
            background: var(--sxc-maroon);
            border: none;
            color: var(--white);
            padding: 12px 28px;
            border-radius: var(--radius-full);
            cursor: pointer;
            transition: var(--transition-base);
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            white-space: nowrap;
        }

        .search-box button:hover {
            background: var(--sxc-maroon-dark);
            transform: scale(0.98);
        }

        /* ===== CLUBS SECTION ===== */
        .clubs-section {
            padding: 60px 0;
            background: var(--bg-light);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .section-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            font-family: var(--font-heading);
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-header h2 i {
            color: var(--sxc-gold);
        }

        .club-count {
            background: var(--sxc-maroon-light);
            color: var(--sxc-maroon);
            padding: 6px 16px;
            border-radius: var(--radius-full);
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .club-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
        }

        .club-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 35px 25px;
            text-align: center;
            border: 1px solid var(--border-light);
            box-shadow: var(--shadow-sm);
            transition: var(--transition-base);
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        .club-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--sxc-maroon), var(--sxc-gold));
            transform: scaleX(0);
            transition: var(--transition-base);
        }

        .club-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
            border-color: transparent;
        }

        .club-card:hover::before {
            transform: scaleX(1);
        }

        .logo-wrapper {
            width: 120px;
            height: 120px;
            margin: 0 auto 20px;
            background: var(--sxc-maroon-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 4px solid var(--white);
            box-shadow: var(--shadow-md);
            transition: var(--transition-base);
            padding: 15px;
        }

        .club-card:hover .logo-wrapper {
            transform: rotate(10deg) scale(1.05);
            border-color: var(--sxc-gold);
            background: var(--sxc-gold-light);
        }

        .club-logo {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            transition: var(--transition-base);
        }

        .club-card:hover .club-logo {
            transform: scale(1.1);
        }

        .club-code-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--sxc-gold-light);
            color: var(--sxc-gold-dark);
            padding: 6px 16px;
            border-radius: var(--radius-full);
            font-size: 0.75rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: 1px solid var(--sxc-gold);
            align-self: center;
        }

        .club-name {
            font-size: 1.4rem;
            font-weight: 700;
            font-family: var(--font-heading);
            margin-bottom: 12px;
            color: var(--text-dark);
        }

        .club-desc {
            font-size: 0.95rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 25px;
            flex-grow: 1;
        }

        .club-footer {
            border-top: 1px solid var(--border-light);
            padding-top: 20px;
            margin-top: auto;
        }

        .club-email {
            font-size: 0.9rem;
            color: var(--sxc-maroon);
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-weight: 600;
            transition: var(--transition-base);
            padding: 10px;
            border-radius: var(--radius-full);
            background: var(--sxc-maroon-soft);
        }

        .club-email i {
            font-size: 0.95rem;
            transition: var(--transition-base);
        }

        .club-email:hover {
            background: var(--sxc-maroon);
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(6, 103, 164, 0.2);
        }

        .club-email:hover i {
            transform: scale(1.1);
        }

        .empty-state {
            grid-column: 1/-1;
            text-align: center;
            padding: 80px 20px;
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--border-dark);
            margin-bottom: 20px;
        }

        .empty-state p {
            color: var(--text-muted);
            font-size: 1.1rem;
            margin-bottom: 20px;
        }

        /* ===== FOOTER ===== */
        .footer {
            background: linear-gradient(145deg, #1a2634 0%, #1e2a36 100%);
            color: var(--white);
            padding: 80px 0 30px;
            position: relative;
            overflow: hidden;
            margin-top: 60px;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--sxc-maroon), var(--sxc-gold), var(--sxc-maroon));
            background-size: 200% 100%;
            animation: gradientMove 3s ease infinite;
        }

        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 60px;
            margin-bottom: 50px;
            position: relative;
            z-index: 1;
        }

        .footer-logo {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 20px;
            color: var(--white);
            font-family: var(--font-heading);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .footer-logo i {
            color: var(--sxc-gold);
        }

        .footer-gold {
            color: var(--sxc-gold);
            position: relative;
        }

        .footer-description {
            color: rgba(255,255,255,0.7);
            line-height: 1.7;
            margin-bottom: 25px;
            font-size: 0.95rem;
        }

        .social-links {
            display: flex;
            gap: 15px;
        }

        .social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            color: var(--white);
            text-decoration: none;
            transition: var(--transition-base);
            border: 1px solid transparent;
        }

        .social-link:hover {
            background: var(--sxc-maroon);
            transform: translateY(-5px);
            border-color: var(--sxc-gold);
        }

        .social-link i {
            font-size: 1.1rem;
            transition: var(--transition-base);
        }

        .social-link:hover i {
            transform: scale(1.1);
        }

        .footer-heading {
            color: var(--white);
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 12px;
            font-family: var(--font-heading);
        }

        .footer-heading::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--sxc-gold);
            border-radius: var(--radius-full);
        }

        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li {
            margin-bottom: 15px;
        }

        .footer-links a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: var(--transition-base);
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
        }

        .footer-links a i {
            width: 18px;
            font-size: 0.9rem;
            color: var(--sxc-gold);
            transition: var(--transition-base);
        }

        .footer-links a:hover {
            color: var(--white);
            transform: translateX(8px);
        }

        .footer-links a:hover i {
            color: var(--sxc-gold);
            transform: scale(1.2);
        }

        .copyright {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 30px;
            text-align: center;
            color: rgba(255,255,255,0.6);
            font-size: 0.9rem;
            position: relative;
            z-index: 1;
        }

        .copyright i {
            color: var(--sxc-gold);
        }

        /* ===== SCROLL TO TOP ===== */
        .scroll-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--sxc-maroon);
            color: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: none;
            box-shadow: var(--shadow-lg);
            opacity: 0;
            visibility: hidden;
            transition: var(--transition-base);
            z-index: 999;
        }

        .scroll-top.visible {
            opacity: 1;
            visibility: visible;
        }

        .scroll-top:hover {
            background: var(--sxc-maroon-dark);
            transform: translateY(-5px);
        }

        /* ===== RESPONSIVE DESIGN ===== */
        @media (max-width: 1024px) {
            .footer-content {
                grid-template-columns: repeat(2, 1fr);
                gap: 40px;
            }
        }

        @media (max-width: 768px) {
            :root {
                --container-padding: 20px;
            }

            .mobile-menu-btn {
                display: block;
            }

            .nav-links {
                position: fixed;
                top: 80px;
                left: 0;
                right: 0;
                background: var(--white);
                flex-direction: column;
                padding: 25px;
                gap: 10px;
                box-shadow: var(--shadow-lg);
                transform: translateY(-150%);
                opacity: 0;
                visibility: hidden;
                transition: var(--transition-base);
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

            .nav-link.active::before {
                display: none;
            }

            .page-header h1 {
                font-size: 2.2rem;
            }

            .search-bar {
                padding: 0 20px;
            }

            .search-box button span {
                display: none;
            }

            .search-box button {
                padding: 12px 20px;
            }

            .section-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .club-grid {
                grid-template-columns: 1fr;
            }

            .footer-content {
                grid-template-columns: 1fr;
                gap: 30px;
                text-align: center;
            }

            .footer-heading::after {
                left: 50%;
                transform: translateX(-50%);
            }

            .footer-links a {
                justify-content: center;
            }

            .social-links {
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .page-header h1 {
                font-size: 1.8rem;
            }

            .page-header p {
                font-size: 1rem;
            }

            .scroll-top {
                bottom: 20px;
                right: 20px;
            }
        }

        /* ===== CUSTOM SCROLLBAR ===== */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-light);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--sxc-maroon);
            border-radius: var(--radius-full);
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--sxc-maroon-dark);
        }
    </style>
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

    <script>
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

        // Reset search on input clear
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
    </script>
</body>
</html>