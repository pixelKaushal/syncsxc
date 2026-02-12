<?php
require_once 'data.php'; 
session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $primary_email = trim($_POST['primary_email']);
    $recovery_email = trim($_POST['recovery_email']);
    $password = $_POST['password'];
    $terms_accepted = isset($_POST['terms']) ? true : false;
    $club_id = $_POST['club'];

    // 1. Terms validation
    if(!$terms_accepted) {
        die("<script>alert('You must agree to the terms to proceed.'); window.history.back();</script>");
    }

    // 2. Domain validation
    if (!filter_var($primary_email, FILTER_VALIDATE_EMAIL) || !str_ends_with($primary_email, '@sxc.edu.np')) {
        die("<script>alert('Access Denied: Only @sxc.edu.np emails allowed.'); window.history.back();</script>");
    }


    $check_stmt = $conn->prepare("SELECT id FROM users WHERE primary_email = ?");
    $check_stmt->bind_param("s", $primary_email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        die("<script>alert('Account already exists. Please Sign In.'); window.location.href='../login.php';</script>");
    }


    $sql = $conn->prepare("INSERT INTO users (primary_email, recovery_email, password, role, club_id) VALUES (?, ?, ?, 'student',?)");
    

    $hashed_pw = password_hash($password, PASSWORD_DEFAULT);
    
    $sql->bind_param("sssi", $primary_email, $recovery_email, $hashed_pw, $club_id);
    
    if($sql->execute()) {
       
        $_SESSION['user_email'] = $primary_email;
        $_SESSION['role'] = 'student'; 
        header('Location: ../index.php'); 
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Access | SyncSXC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/signup.css">  
</head>
<body>

    <div class="login-container">
        <div class="brand-side">
            <h1>Sync<span>SXC</span></h1>
            <p>Enter the administrative portal. Please use your official college-issued credentials to ensure synchronization across all club activities.</p>
        </div>

        <div class="form-side">
            <div class="header">
                <h2>Sign In</h2>
                <p>Verify your official identity to continue.</p>
            </div>

            <form  method="POST" id="signinForm">
                
                <div class="input-box">
                    <label>Primary Institutional Email</label>
                    <div class="input-field">
                        <i class="fa-solid fa-graduation-cap"></i>
                        <input type="email" name="primary_email" id="primaryEmail" 
                               placeholder="name@sxc.edu.np" required>
                    </div>
                    <p class="domain-note">* Must be a valid @sxc.edu.np address</p>
                </div>

                <div class="input-box">
                    <label>Recovery Email</label>
                    <div class="input-field">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="email" name="recovery_email" placeholder="Personal email for recovery" required>
                    </div>
                </div>

                <div class="input-box">
                    <label>Secure Password</label>
                    <div class="input-field">
                        <i class="fa-solid fa-key"></i>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>
                </div><div class="input-box">
                    <label>Select your club</label>
                    <div class="input-field">
                        <i class="fa-solid fa-users"></i>
                        <select name="club" required>
                            <option value="" disabled selected>Select your club</option>
                            <option value="1">The set club</option>
                            <option value="2">sxc sports club</option>
                            <option value="3">sxc computer club</option>
                            <option value="4">Magis Club SXC</option>
                            <option value="5">Art and Culture club</option>
                            <option value="6">chemistry club</option>
                            <option value="7">Alumini club</option>
                            <option value="8">Physics club</option>
                            <option value="9">USMN</option>
                            <option value="10">literary club</option>
                            <option value="11">Maths club</option>
                            <option value="12">Ecosphere club</option>
                        </select>
                </div>
                <input type="checkbox" name="terms" required> I agree to the <a href="../public/terms.php" target="_blank">terms and conditions</a>
                <button type="submit" class="btn-submit">Initialize Session</button>
                <div class="back-link">
                <a href = "login.php"><i class="fa-solid fa-user-plus"></i> Login</a> |
                <a href="../index.php"><i class="fa-solid fa-arrow-left"></i> Back to Home</a>
            </div>
            </form>
        </div>
    </div>

    <script>
        // JS validation to ensure institutional email domain
        document.getElementById('signinForm').onsubmit = function(e) {
            const email = document.getElementById('primaryEmail').value;
            if (!email.endsWith('@sxc.edu.np')) {
                alert('Access Denied: Please use your official @sxc.edu.np email address.');
                e.preventDefault();
                return false;
            }
        };
    </script>
</body>
</html>