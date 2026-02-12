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
            font-size: 3.2rem;
            font-weight: 900;
            font-family: var(--font-heading);
            color: var(--sxc-maroon);
            margin-bottom: 15px;
            animation: fadeInUp 1s ease;
            letter-spacing: -1px;
        }

        .page-header p {
            font-size: 1.1rem;
            color: var(--text-muted);
            margin-bottom: 20px;
            animation: fadeInUp 1s ease 0.1s both;
        }

        .page-badge {
            display: inline-block;
            background: var(--sxc-gold-light);
            color: var(--sxc-gold-dark);
            padding: 8px 24px;
            border-radius: var(--radius-full);
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 20px;
            border: 1px solid var(--sxc-gold);
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

        /* ===== PROTOCOL CARD ===== */
        .protocol-card {
            background: var(--white);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            padding: 60px;
            margin: 40px 0 60px;
            border: 1px solid var(--border-light);
            position: relative;
            overflow: hidden;
        }

        .protocol-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, var(--sxc-maroon), var(--sxc-gold));
        }

        /* ===== IMPORTANT NOTE ===== */
        .important-note {
            background: linear-gradient(135deg, #fff8e1, #fffdf7);
            border-left: 8px solid var(--sxc-gold);
            border-radius: var(--radius-lg);
            padding: 30px 35px;
            margin: 40px 0;
            color: #856404;
            display: flex;
            align-items: center;
            gap: 25px;
            box-shadow: var(--shadow-md);
            position: relative;
            overflow: hidden;
        }

        .important-note::after {
            content: '🔒';
            position: absolute;
            bottom: -20px;
            right: -20px;
            font-size: 8rem;
            opacity: 0.03;
            transform: rotate(-15deg);
        }

        .important-note i {
            font-size: 2.8rem;
            color: var(--sxc-gold);
            background: rgba(212, 175, 55, 0.1);
            padding: 20px;
            border-radius: 50%;
        }

        .important-note div {
            flex: 1;
        }

        .important-note strong {
            color: #7a5c00;
            font-size: 1.1rem;
        }

        /* ===== LEGAL SECTIONS ===== */
        .legal-section {
            margin-bottom: 50px;
        }

        .legal-section h2 {
            color: var(--sxc-dark);
            font-size: 1.8rem;
            font-weight: 700;
            font-family: var(--font-heading);
            margin: 50px 0 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border-light);
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .legal-section h2 i {
            color: var(--sxc-gold);
            font-size: 1.8rem;
        }

        .legal-section h2::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 80px;
            height: 2px;
            background: var(--sxc-gold);
        }

        .legal-section p {
            color: var(--text-muted);
            font-size: 1.05rem;
            margin-bottom: 20px;
            line-height: 1.7;
        }

        .legal-section ul {
            list-style: none;
            padding: 0;
        }

        .legal-section li {
            color: var(--text-muted);
            font-size: 1.05rem;
            margin-bottom: 18px;
            padding-left: 30px;
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .legal-section li::before {
            content: '•';
            color: var(--sxc-gold);
            font-size: 1.8rem;
            position: absolute;
            left: 0;
            top: -10px;
        }

        .role-tag {
            padding: 6px 16px;
            border-radius: var(--radius-full);
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-left: 10px;
        }

        .tag-executive {
            background: var(--sxc-maroon-light);
            color: var(--sxc-maroon);
            border: 1px solid var(--sxc-maroon);
        }

        .tag-student {
            background: var(--bg-light);
            color: var(--text-muted);
            border: 1px solid var(--border-dark);
        }

        code {
            background: var(--bg-light);
            padding: 4px 12px;
            border-radius: var(--radius-full);
            font-size: 0.9rem;
            color: var(--sxc-maroon);
            font-weight: 600;
            border: 1px solid var(--border-light);
        }

        /* ===== FAQ SECTION ===== */
        .faq-section {
            margin-top: 80px;
        }

        .faq-section h2 {
            color: var(--sxc-dark);
            font-size: 2rem;
            font-weight: 800;
            font-family: var(--font-heading);
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            gap: 15px;
            border: none;
        }

        .faq-section h2 i {
            color: var(--sxc-gold);
        }

        .faq-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .faq-item {
            background: var(--white);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-lg);
            transition: var(--transition-base);
            overflow: hidden;
        }

        .faq-item:hover {
            border-color: var(--sxc-maroon-light);
            box-shadow: var(--shadow-md);
        }

        .faq-item.active {
            border-color: var(--sxc-maroon);
            box-shadow: 0 15px 30px rgba(6, 103, 164, 0.1);
        }

        .faq-question {
            padding: 25px 30px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 700;
            color: var(--sxc-maroon);
            font-size: 1.1rem;
            background: var(--white);
            transition: var(--transition-base);
        }

        .faq-question:hover {
            background: var(--sxc-maroon-soft);
        }

        .faq-question i {
            color: var(--sxc-gold);
            font-size: 1.1rem;
            transition: var(--transition-base);
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .faq-answer {
            padding: 0 30px;
            max-height: 0;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            color: var(--text-muted);
            font-size: 0.98rem;
            line-height: 1.7;
            background: var(--white);
            border-top: 1px solid transparent;
        }

        .faq-item.active .faq-answer {
            padding: 0 30px 30px;
            max-height: 500px;
            border-top-color: var(--border-light);
        }

        /* ===== BACK LINK ===== */
        .back-link {
            text-align: center;
            margin-top: 70px;
            margin-bottom: 30px;
            position: relative;
        }

        .back-link a {
            color: var(--sxc-maroon);
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 16px 32px;
            border-radius: var(--radius-full);
            background: var(--sxc-maroon-soft);
            transition: var(--transition-base);
            border: 1px solid transparent;
        }

        .back-link a:hover {
            background: var(--sxc-maroon);
            color: var(--white);
            transform: translateX(-5px);
            box-shadow: 0 10px 25px rgba(6, 103, 164, 0.2);
        }

        .back-link a i {
            transition: var(--transition-base);
        }

        .back-link a:hover i {
            transform: translateX(-5px);
        }

        /* ===== FOOTER ===== */
        .footer {
            background: linear-gradient(145deg, #1a2634 0%, #1e2a36 100%);
            color: var(--white);
            padding: 80px 0 30px;
            position: relative;
            overflow: hidden;
            margin-top: 80px;
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
            
            .protocol-card {
                padding: 40px;
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
                font-size: 2.4rem;
            }

            .protocol-card {
                padding: 30px 25px;
            }

            .important-note {
                flex-direction: column;
                text-align: center;
                padding: 25px;
            }

            .important-note i {
                margin-bottom: 10px;
            }

            .legal-section h2 {
                font-size: 1.5rem;
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

            .protocol-card {
                padding: 25px 20px;
            }

            .legal-section li {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .role-tag {
                margin-left: 0;
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
                        <li><a href="about.php"><i class="fas fa-shield-alt"></i> Protocol</a></li>
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

        // FAQ Accordion
        document.querySelectorAll('.faq-question').forEach(q => {
            q.addEventListener('click', () => {
                const item = q.parentElement;
                item.classList.toggle('active');
                const icon = q.querySelector('i:last-child');
                icon.classList.toggle('fa-plus');
                icon.classList.toggle('fa-minus');
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

        // Auto-expand first FAQ
        window.addEventListener('load', function() {
            const firstFaq = document.querySelector('.faq-item');
            if (firstFaq) {
                firstFaq.classList.add('active');
                const icon = firstFaq.querySelector('.faq-question i:last-child');
                icon.classList.remove('fa-plus');
                icon.classList.add('fa-minus');
            }
        });
    </script>
</body>
</html>