<?php
session_start();
require_once '../backend/data.php';

// ===== SECURITY CHECK - Must be logged in =====
if (!isset($_SESSION['user_id'])) {
    header('Location: ../backend/login.php');
    exit;
}

// ===== GET TARGET USER ID =====
$viewing_uid = isset($_GET['uid']) ? (int)$_GET['uid'] : $_SESSION['user_id'];
$current_user_id = $_SESSION['user_id'];
$current_user_role = $_SESSION['role'] ?? 'student';

// ===== FETCH TARGET USER DATA =====
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $viewing_uid);
$stmt->execute();
$target_user = $stmt->get_result()->fetch_assoc();

// ===== IF USER DOESN'T EXIST =====
if (!$target_user) {
    die("<script>alert('User not found.'); window.location.href='../index.php';</script>");
}

// ===== FETCH USER'S CLUB (if any) =====
$target_club = null;
if (!empty($target_user['club_id'])) {
    $club_stmt = $conn->prepare("SELECT * FROM clubs WHERE id = ?");
    $club_stmt->bind_param("i", $target_user['club_id']);
    $club_stmt->execute();
    $target_club = $club_stmt->get_result()->fetch_assoc();
}

// ===== CHECK IF VIEWING OWN PROFILE =====
$is_own_profile = ($viewing_uid == $current_user_id);
$is_admin_view = ($current_user_role === 'admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php 
        if ($is_own_profile) {
            echo "My Profile | SyncSXC";
        } else {
            echo "User Profile | " . htmlspecialchars($target_user['primary_email']) . " | SyncSXC";
        }
        ?>
    </title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/profile.css">
    <link rel="stylesheet" href="../css/global.css">
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
                <a href="../index.php" class="nav-link">
                    <i class="fas fa-home"></i> Home
                </a>
                <a href="../public/events.php" class="nav-link">
                    <i class="fas fa-calendar-alt"></i> Events
                </a>
                <a href="../public/clubs.php" class="nav-link">
                    <i class="fas fa-users"></i> Clubs
                </a>
                <a href="profile.php" class="nav-link <?php echo $is_own_profile ? 'active' : ''; ?>">
                    <i class="fas fa-user"></i> My Profile
                </a>
                <?php if ($current_user_role === 'admin'): ?>
                <a href="../admin/dashboard.php" class="nav-link">
                    <i class="fas fa-chart-line"></i> Admin
                </a>
                <?php endif; ?>
                <a href="../backend/logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Back Button (shown when viewing other profiles) -->
        <?php if (!$is_own_profile): ?>
        <div class="back-nav">
            <a href="javascript:history.back()" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Go Back
            </a>
        </div>
        <?php endif; ?>

        <!-- Admin Viewing Badge -->
        <?php if (!$is_own_profile && $is_admin_view): ?>
        <div class="admin-view-badge">
            <i class="fas fa-shield-alt"></i>
            <strong>Admin View:</strong> You are viewing 
            <span style="font-weight: 700; color: var(--sxc-maroon);">
                <?php echo htmlspecialchars($target_user['primary_email']); ?>
            </span>'s profile
        </div>
        <?php endif; ?>

        <!-- ===== PROFILE HEADER ===== -->
        <div class="profile-header">
            <div class="profile-grid">
                <div class="profile-avatar">
                    <?php if ($target_user['role'] === 'admin'): ?>
                        <i class="fas fa-crown"></i>
                    <?php else: ?>
                        <i class="fas fa-user-graduate"></i>
                    <?php endif; ?>
                </div>
                
                <div class="profile-info">
                    <h1>
                        <?php 
                        if ($is_own_profile) {
                            echo "My Profile";
                        } else {
                            echo htmlspecialchars($target_user['primary_email']);
                        }
                        ?>
                    </h1>
                    
                    <div class="profile-badges">
                        <!-- Role Badge -->
                        <span class="badge <?php echo $target_user['role'] === 'admin' ? 'role-admin' : 'role'; ?>">
                            <i class="fas fa-<?php echo $target_user['role'] === 'admin' ? 'crown' : 'user'; ?>"></i>
                            <?php echo ucfirst($target_user['role']); ?>
                        </span>
                        
                        <!-- Club Badge (if member) -->
                        <?php if ($target_club): ?>
                        <span class="badge club">
                            <i class="fas fa-flag"></i>
                            <?php echo htmlspecialchars($target_club['name']); ?>
                        </span>
                        <?php endif; ?>
                        
                        <!-- Recovery Email Badge (only show if own profile) -->
                        <?php if ($is_own_profile): ?>
                        <span class="badge recovery">
                            <i class="fas fa-envelope"></i>
                            Recovery: <?php echo htmlspecialchars($target_user['recovery_email']); ?>
                        </span>
                        <?php endif; ?>
                        
                        <!-- Own Profile Badge -->
                        <?php if ($is_own_profile): ?>
                        <span class="badge own-profile">
                            <i class="fas fa-check-circle"></i>
                            You
                        </span>
                        <?php endif; ?>
                        
                        <!-- Viewing Badge -->
                        <?php if (!$is_own_profile): ?>
                        <span class="badge viewing">
                            <i class="fas fa-eye"></i>
                            Viewing
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="profile-meta">
                    <div class="member-since">
                        <i class="fas fa-calendar-alt"></i>
                        Member since: <?php echo date('F Y', strtotime($target_user['created_at'] ?? 'now')); ?>
                    </div>
                    
                    <!-- User ID Badge -->
                    <div style="display: flex; gap: 8px;">
                        <span style="background: var(--bg-light); padding: 8px 16px; border-radius: var(--radius-full); border: 1px solid var(--border-light);">
                            <i class="fas fa-hashtag" style="color: var(--sxc-maroon);"></i>
                            User ID: <strong><?php echo $target_user['id']; ?></strong>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== ACCOUNT INFORMATION ===== -->
        <div class="profile-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-id-card"></i>
                    Account Information
                </h2>
                
                <!-- Show Edit button only for own profile -->
                <?php if ($is_own_profile): ?>
                <a href="edit_profile.php?id=<?php echo $target_user['id']; ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit Profile
                </a>
                <?php endif; ?>
                
                <!-- Show Message button for other profiles -->
                <?php if (!$is_own_profile): ?>
                <a href="mailto:<?php echo htmlspecialchars($target_user['primary_email']); ?>" class="btn btn-outline">
                    <i class="fas fa-envelope"></i> Send Email
                </a>
                <?php endif; ?>
            </div>
            
            <div class="info-grid">
                <!-- Primary Information Card -->
                <div class="info-card">
                    <h3><i class="fas fa-user-circle"></i> Primary Details</h3>
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-id-badge"></i>
                            User ID
                        </span>
                        <span class="info-value code">#<?php echo $target_user['id']; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-envelope"></i>
                            Primary Email
                        </span>
                        <span class="info-value"><?php echo htmlspecialchars($target_user['primary_email']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-tag"></i>
                            Account Type
                        </span>
                        <span class="info-value" style="text-transform: capitalize;">
                            <?php echo ucfirst($target_user['role']); ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-clock"></i>
                            Created
                        </span>
                        <span class="info-value">
                            <?php echo date('M d, Y', strtotime($target_user['created_at'] ?? 'now')); ?>
                        </span>
                    </div>
                </div>
                
                <!-- Security Information Card (only show if own profile) -->
                <?php if ($is_own_profile): ?>
                <div class="info-card">
                    <h3><i class="fas fa-shield-alt"></i> Security & Recovery</h3>
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-envelope-open-text"></i>
                            Recovery Email
                        </span>
                        <span class="info-value"><?php echo htmlspecialchars($target_user['recovery_email']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-key"></i>
                            Password
                        </span>
                        <span class="info-value">
                            ••••••••
                            <?php if ($is_own_profile): ?>
                            <a href="../backend/forgotpw.php" style="margin-left: 10px; color: var(--sxc-maroon); text-decoration: none; font-size: 0.85rem;">
                                <i class="fas fa-edit"></i> Change
                            </a>
                            <?php endif; ?>
                        </span>
                    </div>
                    <?php if ($is_own_profile): ?>
                    <div style="margin-top: 20px; padding-top: 15px; border-top: 1px dashed var(--border-light);">
                        <span style="color: var(--text-muted); font-size: 0.9rem;">
                            <i class="fas fa-info-circle"></i>
                            Last login: Not tracked
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <!-- Club Information Card (if user is in a club) -->
                <?php if ($target_club): ?>
                <div class="info-card">
                    <h3><i class="fas fa-flag"></i> Club Affiliation</h3>
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-tag"></i>
                            Club Code
                        </span>
                        <span class="info-value code"><?php echo htmlspecialchars($target_club['club_code']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-building"></i>
                            Club Name
                        </span>
                        <span class="info-value"><?php echo htmlspecialchars($target_club['name']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-id-card"></i>
                            Club ID
                        </span>
                        <span class="info-value code">#<?php echo $target_club['id']; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-envelope"></i>
                            Contact Email
                        </span>
                        <span class="info-value"><?php echo htmlspecialchars($target_club['email']); ?></span>
                    </div>
                    
                    <?php if ($target_user['role'] === 'admin'): ?>
                    <div style="margin-top: 20px;">
                        <a href="../public/clubs.php?club_id=<?php echo $target_club['id']; ?>" class="btn btn-outline btn-sm" style="width: 100%;">
                            <i class="fas fa-eye"></i> View Club
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

    
        
        <!-- Back to Top Button -->
        <div style="text-align: center; margin-top: 20px;">
            <a href="#" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;" style="color: var(--sxc-maroon); text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-arrow-up"></i>
                Back to Top
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background: linear-gradient(145deg, #1a2634, #1e2a36); color: white; padding: 30px 0; margin-top: 50px;">
        <div class="container">
            <div style="text-align: center; color: rgba(255,255,255,0.7);">
                <p>&copy; <?php echo date('Y'); ?> SyncSXC. All rights reserved. | St. Xavier's College, Maitighar</p>
                <p style="margin-top: 10px; font-size: 0.85rem;">
                    <i class="fas fa-shield-alt"></i> Institutional Authentication System
                </p>
            </div>
        </div>
    </footer>

    <script src="profile.js">
    </script>
</body>
</html>