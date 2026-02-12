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

        /* ===== HERO SECTION ===== */
        .hero {
            background: linear-gradient(135deg, var(--white) 0%, var(--bg-light) 100%);
            padding: 80px 0;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
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

        .hero::after {
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

        .hero-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .hero-content {
            animation: fadeInUp 1s ease;
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

        .hero-content h1 {
            font-size: 3.2rem;
            font-weight: 800;
            font-family: var(--font-heading);
            line-height: 1.2;
            margin-bottom: 20px;
            color: var(--text-dark);
        }

        .hero-highlight {
            color: var(--sxc-maroon);
            position: relative;
            display: inline-block;
        }

        .hero-highlight::after {
            content: '';
            position: absolute;
            bottom: 5px;
            left: 0;
            width: 100%;
            height: 8px;
            background: rgba(6, 103, 164, 0.1);
            z-index: -1;
            border-radius: var(--radius-full);
        }

        .hero-gold {
            color: var(--sxc-gold);
            position: relative;
            display: inline-block;
        }

        .hero-gold::after {
            content: '';
            position: absolute;
            bottom: 5px;
            left: 0;
            width: 100%;
            height: 8px;
            background: rgba(212, 175, 55, 0.1);
            z-index: -1;
            border-radius: var(--radius-full);
        }

        .hero-description {
            font-size: 1.1rem;
            color: var(--text-muted);
            margin-bottom: 30px;
            line-height: 1.7;
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
            margin-bottom: 40px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px 28px;
            border-radius: var(--radius-full);
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            transition: var(--transition-base);
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
            border-color: var(--sxc-maroon-dark);
        }

        .hero-stats {
            display: flex;
            gap: 40px;
        }

        .stat-item {
            display: flex;
            flex-direction: column;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--sxc-maroon);
            font-family: var(--font-heading);
            line-height: 1;
        }

        .stat-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 5px;
        }

        .hero-visual {
            position: relative;
            height: 400px;
            animation: fadeInRight 1s ease;
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .floating-card {
            position: absolute;
            background: var(--white);
            padding: 25px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            width: 220px;
            transition: var(--transition-base);
            border: 1px solid var(--border-light);
            backdrop-filter: blur(10px);
        }

        .floating-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-hover);
        }

        .card-1 {
            top: 20px;
            right: 50px;
            animation: float 6s ease-in-out infinite;
        }

        .card-2 {
            bottom: 40px;
            left: 20px;
            animation: float 7s ease-in-out infinite;
        }

        .card-3 {
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            animation: float 8s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        .card-icon {
            width: 50px;
            height: 50px;
            background: var(--sxc-maroon-light);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .card-icon i {
            font-size: 1.5rem;
            color: var(--sxc-maroon);
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text-dark);
        }

        .card-desc {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        /* ===== SECTION STYLES ===== */
        .section {
            padding: 80px 0;
            position: relative;
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-size: 2.2rem;
            font-weight: 800;
            font-family: var(--font-heading);
            color: var(--text-dark);
            margin-bottom: 15px;
        }

        .title-underline {
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--sxc-maroon), var(--sxc-gold));
            margin: 0 auto;
            border-radius: var(--radius-full);
            position: relative;
        }

        .title-underline::before {
            content: '';
            position: absolute;
            top: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 10px;
            height: 10px;
            background: var(--sxc-gold);
            border-radius: 50%;
        }

        /* ===== EVENTS SECTION ===== */
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 20px;
        }

        .event-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            overflow: hidden;
            border: 1px solid var(--border-light);
            box-shadow: var(--shadow-sm);
            transition: var(--transition-base);
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .event-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
            border-color: transparent;
        }

        .event-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--sxc-maroon), var(--sxc-gold));
            opacity: 0;
            transition: var(--transition-base);
        }

        .event-card:hover::before {
            opacity: 1;
        }

        .event-header {
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(to right, var(--white), var(--bg-light));
            border-bottom: 1px solid var(--border-light);
        }

        .event-club {
            color: var(--sxc-gold);
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--sxc-gold-light);
            padding: 4px 14px;
            border-radius: var(--radius-full);
        }

        .event-date {
            color: var(--text-muted);
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 6px;
            background: var(--bg-light);
            padding: 4px 14px;
            border-radius: var(--radius-full);
        }

        .event-body {
            padding: 25px;
            flex-grow: 1;
        }

        .event-title {
            font-size: 1.4rem;
            margin-bottom: 15px;
            color: var(--text-dark);
            font-weight: 700;
            font-family: var(--font-heading);
            line-height: 1.3;
        }

        .event-desc {
            font-size: 0.95rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 0;
        }

        .event-footer {
            padding: 20px;
            background: var(--bg-light);
            border-top: 1px solid var(--border-light);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .event-venue {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .event-venue i {
            color: var(--sxc-maroon);
        }

        .view-all {
            text-align: center;
            margin-top: 50px;
        }

        /* ===== CLUBS SECTION ===== */
        .clubs-preview {
            background: var(--white);
            position: relative;
            overflow: hidden;
        }

        .clubs-preview::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--sxc-gold), transparent);
        }

        .clubs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
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
        }

        .club-card:hover::before {
            transform: scaleX(1);
        }

        .club-logo-wrapper {
            width: 100px;
            height: 100px;
            margin: 0 auto 20px;
            background: var(--sxc-maroon-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid var(--white);
            box-shadow: var(--shadow-md);
            transition: var(--transition-base);
        }

        .club-card:hover .club-logo-wrapper {
            transform: rotate(10deg) scale(1.05);
            border-color: var(--sxc-gold);
        }

        .club-logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        .club-name {
            font-size: 1.3rem;
            font-weight: 700;
            font-family: var(--font-heading);
            margin-bottom: 12px;
            color: var(--text-dark);
        }

        .club-desc {
            font-size: 0.95rem;
            color: var(--text-muted);
            margin-bottom: 20px;
            line-height: 1.6;
        }

        /* ===== CTA SECTION ===== */
        .cta-section {
            background: linear-gradient(135deg, var(--sxc-maroon) 0%, #05558a 100%);
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .cta-section::after {
            content: '';
            position: absolute;
            bottom: -50%;
            left: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(212,175,55,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .cta-content {
            text-align: center;
            color: var(--white);
            position: relative;
            z-index: 1;
            max-width: 800px;
            margin: 0 auto;
        }

        .cta-content h2 {
            font-size: 2.5rem;
            font-weight: 800;
            font-family: var(--font-heading);
            margin-bottom: 20px;
        }

        .cta-content p {
            font-size: 1.1rem;
            margin-bottom: 35px;
            opacity: 0.95;
            line-height: 1.7;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
        }

        .btn-cta-primary {
            background: var(--sxc-gold);
            color: var(--text-dark);
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.2);
        }

        .btn-cta-primary:hover {
            background: var(--sxc-gold-dark);
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(212, 175, 55, 0.3);
        }

        .btn-cta-secondary {
            background: transparent;
            color: var(--white);
            border: 2px solid var(--white);
        }

        .btn-cta-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-3px);
        }

        /* ===== FOOTER ===== */
        .footer {
            background: linear-gradient(145deg, #1a2634 0%, #1e2a36 100%);
            color: var(--white);
            padding: 80px 0 30px;
            position: relative;
            overflow: hidden;
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
            .hero-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .hero-visual {
                height: 350px;
            }

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

            .hero-content h1 {
                font-size: 2.5rem;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .hero-stats {
                justify-content: space-around;
            }

            .hero-visual {
                display: none;
            }

            .events-grid,
            .clubs-grid {
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

            .cta-buttons {
                flex-direction: column;
            }

            .section-title h2 {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 480px) {
            .hero-content h1 {
                font-size: 2rem;
            }

            .hero-stats {
                flex-direction: column;
                gap: 20px;
                align-items: center;
            }

            .stat-item {
                align-items: center;
            }

            .event-footer {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .event-venue {
                width: 100%;
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
        <div class="container nav-container">
            <a href="index.php" class="logo">
                <i class="fas fa-sync-alt"></i>
                Sync<span class="logo-gold">SXC</span>
            </a>
            
            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="nav-links" id="navLinks">
                <a href="index.php" class="nav-link active">
                    <i class="fas fa-home"></i> Home
                </a>
                <a href="public/events.php" class="nav-link">
                    <i class="fas fa-calendar-alt"></i> Events
                </a>
                <a href="public/clubs.php" class="nav-link">
                    <i class="fas fa-users"></i> Clubs
                </a>
                <a href="public/schedule.php" class="nav-link">
                    <i class="fas fa-clock"></i> Schedule
                </a>
                <?php if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email'])): ?>
                    <a href="backend/login.php" class="nav-link">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                <?php else: ?>
                    <a href="backend/logout.php" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-grid">
                <div class="hero-content">
                    <h1>
                        Stay in <span class="hero-highlight">Sync</span> with 
                        <span class="hero-gold">Maitighar</span>
                    </h1>
                    <p class="hero-description">
                        The centralized platform for all St. Xavier's College events, club activities, 
                        and official schedules. Never miss another campus event again.
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
                                    echo $eventsCount ? $eventsCount->num_rows : '0';
                                ?>+
                            </span> 
                            <span class="stat-label">Events</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">
                                <?php
                                    echo '12+';
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
                        <span class="event-club">
                            <i class="fas fa-users"></i>
                            <?php echo htmlspecialchars($event['name']); ?>
                        </span>
                        <span class="event-date">
                            <i class="fas fa-calendar"></i>
                            <?php echo date('M d, Y', strtotime($event['proposed_date'])); ?>
                        </span>
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
                        <a href="public/events.php#event-<?php echo $event['event_id']; ?>" class="btn btn-primary">
                            View Details <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <?php
                        endwhile;
                    else:
                ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 60px;">
                    <i class="fas fa-calendar-times" style="font-size: 3rem; color: var(--border-dark); margin-bottom: 15px;"></i>
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
                            <img src="img/<?php echo htmlspecialchars($club['logo_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($club['name']); ?>" 
                                 class="club-logo">
                        <?php else: ?>
                            <i class="fas fa-users" style="font-size: 2.5rem; color: var(--sxc-maroon);"></i>
                        <?php endif; ?>
                    </div>
                    
                    <h3 class="club-name"><?php echo htmlspecialchars($club['name']); ?></h3>
                    <p class="club-desc">
                        <?php 
                            $desc = htmlspecialchars($club['description']);
                            echo (strlen($desc) > 100) ? substr($desc, 0, 97) . '...' : $desc;
                        ?>
                    </p>
                    
                    <a href="public/clubs.php#club-<?php echo $club['club_code']; ?>" class="btn btn-primary">
                        <i class="fas fa-eye"></i> View Club
                    </a>
                </div>
                <?php
                        endwhile;
                    else:
                ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 60px;">
                    <i class="fas fa-users-slash" style="font-size: 3rem; color: var(--border-dark); margin-bottom: 15px;"></i>
                    <p style="color: var(--text-muted); font-size: 1.1rem;">Club information coming soon!</p>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="view-all">
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
                        <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                        <li><a href="public/events.php"><i class="fas fa-calendar-alt"></i> Events</a></li>
                        <li><a href="public/clubs.php"><i class="fas fa-users"></i> Clubs</a></li>
                        <li><a href="public/schedule.php"><i class="fas fa-clock"></i> Schedule</a></li>
                        <li><a href="about.php"><i class="fas fa-info-circle"></i> About</a></li>
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

        // Dynamic copyright year
        document.addEventListener('DOMContentLoaded', function() {
            const yearElements = document.querySelectorAll('.copyright-year');
            yearElements.forEach(el => {
                el.textContent = new Date().getFullYear();
            });
        });
    </script>
</body>
</html>