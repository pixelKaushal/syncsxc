<?php
session_start();
require_once '../backend/data.php';

// ===== SECURITY CHECK - Must be logged in =====
if (!isset($_SESSION['user_id'])) {
    header('Location: ../backend/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$current_user_email = $_SESSION['user_email'];
$is_admin = ($_SESSION['role'] === 'admin');

// ===== CHECK IF EDITING OWN PROFILE OR ADMIN EDITING OTHERS =====
$edit_id = isset($_GET['id']) ? (int)$_GET['id'] : $user_id;

// Security: Regular users can only edit their own profile
if ( $edit_id !== $user_id) {
    die("<script>alert('You can only edit your own profile.'); window.location.href='profile.php';</script>");
}

// ===== FETCH USER DATA =====
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $edit_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    die("<script>alert('User not found.'); window.location.href='profile.php';</script>");
}

// ===== FETCH CLUBS FOR ADMIN DROPDOWN =====


// ===== HANDLE FORM SUBMISSION =====
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $primary_email = trim($_POST['primary_email'] ?? '');
    $recovery_email = trim($_POST['recovery_email'] ?? '');
    $role = $_POST['role'] ?? $user['role'];
    $club_id = isset($_POST['club_id']) && $_POST['club_id'] !== '' ? (int)$_POST['club_id'] : null;
    $is_club_admin = isset($_POST['is_club_admin']) ? 1 : 0;
    
    // ===== VALIDATION =====
    $errors = [];
    
    // Email validation
    if (empty($primary_email)) {
        $errors[] = "Primary email is required.";
    } elseif (!filter_var($primary_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } elseif (!str_ends_with($primary_email, '@sxc.edu.np')) {
        $errors[] = "Primary email must be an @sxc.edu.np address.";
    }
    
    // Recovery email validation (optional but must be valid if provided)
    if (!empty($recovery_email) && !filter_var($recovery_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid recovery email format.";
    }
    
    // Check if email already exists (excluding current user)
    if ($primary_email !== $user['primary_email']) {
        $check = $conn->prepare("SELECT id FROM users WHERE primary_email = ? AND id != ?");
        $check->bind_param("si", $primary_email, $edit_id);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $errors[] = "This email is already registered by another user.";
        }
    }
    
    // ===== UPDATE DATABASE =====
    if (empty($errors)) {    
            
            $update = $conn->prepare("UPDATE users SET recovery_email = ? WHERE id = ?");
            $update->bind_param("si", $recovery_email, $edit_id);
        
        
        if ($update->execute()) {
            $success_message = "Profile updated successfully!";
            
            // If user updated their own email, update session
            if ($edit_id === $user_id && $primary_email !== $current_user_email) {
                $_SESSION['user_email'] = $primary_email;
            }
            
            // Refresh user data
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->bind_param("i", $edit_id);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
        } else {
            $error_message = "Update failed: " . $conn->error;
        }
    } else {
        $error_message = implode("<br>", $errors);
    }
}

$is_own_profile = ($edit_id === $user_id);
$page_title = $is_own_profile ? "Edit My Profile" : "Edit User Profile (Admin)";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> | SyncSXC</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/edit_profile.css">
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
                <a href="about.php" class="nav-link">About</a>
                <a href="profile.php?uid=<?php echo $user_id; ?>" class="nav-link active">Profile</a>
                <a href="../backend/logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            
            <h1>
                <i class="fas fa-user-edit"></i>
                <?php echo $page_title; ?>
            </h1>
            <p>Update your account information below</p>
        </div>

        <!-- Main Card -->
        <div class="edit-card">
            <!-- User Info Summary -->
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-details">
                    <h3>Editing Profile For</h3>
                    <p><?php echo htmlspecialchars($user['primary_email']); ?></p>
                </div>
            </div>

            <!-- Success/Error Messages -->
            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo $success_message; ?></span>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo $error_message; ?></span>
                </div>
            <?php endif; ?>

            <!-- Edit Form -->
            <form method="POST" id="editForm">
                <div class="form-grid">
                    <!-- Primary Email (always shown, but disabled for non-admins) -->
                    <div class="form-group full-width">
                        <label>
                            <i class="fas fa-envelope"></i>
                            Primary Email
                            <?php if (!$is_admin && !$is_own_profile): ?>
                                <span>(read-only)</span>
                            <?php endif; ?>
                        </label>
                        <input 
                            type="email" 
                            name="primary_email" 
                            class="form-control <?php echo (!$is_admin && !$is_own_profile) ? 'readonly' : ''; ?>"
                            value="<?php echo htmlspecialchars($user['primary_email']); ?>"
                            <?php echo (!$is_admin && !$is_own_profile) ? 'readonly' : ''; ?>
                            <?php echo ($is_own_profile || $is_admin) ? 'required' : ''; ?>
                        >
                        <div class="domain-hint">
                            <i class="fas fa-shield-alt"></i>
                            Must be @sxc.edu.np
                        </div>
                    </div>

                    <!-- Recovery Email (editable by everyone) -->
                    <div class="form-group full-width">
                        <label>
                            <i class="fas fa-envelope-open-text"></i>
                            Recovery Email
                            <span>(optional, for account recovery)</span>
                        </label>
                        <input 
                            type="email" 
                            name="recovery_email" 
                            class="form-control"
                            value="<?php echo htmlspecialchars($user['recovery_email'] ?? ''); ?>"
                            placeholder="your.backup@email.com"
                        >
                    </div>                   
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i>
                        Save Changes
                    </button>
                    
                    <a href="profile.php?uid=<?php echo $edit_id; ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                </div>
            </form>

            <!-- Password Change Link (only for own profile) -->
            <?php if ($is_own_profile): ?>
                <div class="password-section">
                    <a href="../backend/forgotpw.php">
                        <i class="fas fa-key"></i>
                        Want to change your password? Click here
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer" style="background: linear-gradient(145deg, #1a2634, #1e2a36); color: white; padding: 30px 0; margin-top: 60px;">
        <div class="container">
            <div style="text-align: center; color: rgba(255,255,255,0.7);">
                <p>&copy; <?php echo date('Y'); ?> SyncSXC. All rights reserved. | St. Xavier's College, Maitighar</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile Menu Toggle
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

            // Form submission handling
            const form = document.getElementById('editForm');
            const submitBtn = document.getElementById('submitBtn');

            if (form) {
                form.addEventListener('submit', function(e) {
                    // Disable submit button to prevent double submission
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                });
            }

            // Auto-dismiss alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>