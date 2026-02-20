<?php
session_start();
require_once 'data.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 1. Domain Validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !str_ends_with($email, '@sxc.edu.np')) {
        die("<script>alert('Access Denied: Use your institutional @sxc.edu.np account.'); window.history.back();</script>");
    }


    $query = "SELECT u.id AS user_uid, u.password, u.role, c.id AS club_uid, c.name AS club_display_name 
              FROM users u 
              LEFT JOIN clubs c ON u.primary_email = c.email 
              WHERE u.primary_email = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("<script>alert('Account not found. Students must Sign Up. Executives, contact Master Admin.'); window.location.href='../signup.php';</script>");
    }

    $user = $result->fetch_assoc();

    // 3. Password Verification
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_uid'];
        $_SESSION['user_email'] = $email;
        $_SESSION['role'] = $user['role'];

        // 4. Role-Based Redirection
        if ($user['role'] === 'admin') {
            
            $_SESSION['club_name'] = $user['club_display_name']; 
            $_SESSION['club_id'] = $user['club_uid'];
            
            header('Location: ../admin/dashboard.php');
        } else {
            header('Location: ../index.php');
        }
        exit;
    } else {
        die("<script>alert('Invalid password.'); window.history.back();</script>");
    }
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | SyncSXC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="css/global.css">
</head>
<body>

    <div class="login-container">
        <div class="login-side-content">
            <div class="side-logo">Sync<span>SXC</span></div>
            <div class="side-text">
                <h2>Welcome Back!</h2>
                <p>Enter your credentials to access the administrative dashboard and keep the campus in sync.</p>
            </div>
            <i class="fa-solid fa-shield-halved" style="font-size: 8rem; position: absolute; bottom: 40px; right: 40px; opacity: 0.1;"></i>
        </div>

        <div class="login-form-section">
            <div class="form-header">
                <h1>Login</h1>
                <p>Secure access for club presidents & coordinators</p>
            </div>

            <form method="POST">
                <div class="input-group">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" name="email" placeholder="college email" required>
                </div>

                <div class="input-group">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                    <a href="forgotpw.php" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); font-size: 0.8rem; color: var(--sxc-blue); text-decoration: none;">Forgot?</a>
                </div>

                <button type="submit" class="login-btn">Secure Login</button>
            </form>

            <div class="back-link">
                <a href = "signup.php"><i class="fa-solid fa-user-plus"></i> Create an Account</a> |
                <a href="../index.php"><i class="fa-solid fa-arrow-left"></i> Back to Home</a>
            </div>
        </div>
    </div>

</body>
</html>