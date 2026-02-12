<?php
require_once 'data.php'; 
session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $primary_email = trim($_POST['primary_email']);
    $recovery_email = trim($_POST['recovery_email']);
    $password = $_POST['password'];
    $terms_accepted = isset($_POST['terms']) ? true : false;

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


    $sql = $conn->prepare("INSERT INTO users (primary_email, recovery_email, password, role) VALUES (?, ?, ?, 'student')");
    

    $hashed_pw = password_hash($password, PASSWORD_DEFAULT);
    
    $sql->bind_param("sss", $primary_email, $recovery_email, $hashed_pw);
    
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
    <style>
        :root {
            --sxc-blue: #0667A4;
            --sxc-dark: #002147;
            --sxc-gold: #d4af37;
            --white: #ffffff;
            --error: #e74c3c;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }

        body {
            background: #f0f2f5;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 1100px;
            height: 650px;
            background: var(--white);
            display: flex;
            border-radius: 24px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.12);
            overflow: hidden;
        }

        /* Brand Side */
        .brand-side {
            flex: 1;
            background: var(--sxc-dark);
            background-image: linear-gradient(135deg, rgba(6,103,164,0.8) 0%, rgba(0,33,71,0.9) 100%), 
                              url('https://www.transparenttextures.com/patterns/cubes.png');
            padding: 60px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .brand-side h1 { font-size: 3.5rem; margin-bottom: 20px; }
        .brand-side h1 span { color: var(--sxc-gold); }
        .brand-side p { font-size: 1.1rem; line-height: 1.8; opacity: 0.8; font-weight: 300; }

        /* Form Side */
        .form-side {
            flex: 1.2;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .header { margin-bottom: 30px; }
        .header h2 { color: var(--sxc-dark); font-size: 2rem; }
        .header p { color: #666; font-size: 0.95rem; }

        .input-box { margin-bottom: 20px; position: relative; }
        .input-box label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem; color: var(--sxc-dark); }
        
        .input-field { position: relative; }
        .input-field i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--sxc-blue); }
        
        .input-field input {
            width: 100%;
            padding: 14px 15px 14px 45px;
            border: 2px solid #e1e1e1;
            border-radius: 12px;
            outline: none;
            transition: 0.3s;
            font-size: 1rem;
        }

        .input-field input:focus { border-color: var(--sxc-blue); box-shadow: 0 0 10px rgba(6,103,164,0.1); }

        .btn-submit {
            background: var(--sxc-blue);
            color: white;
            padding: 16px;
            border: none;
            border-radius: 12px;
            width: 100%;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 10px;
            transition: 0.3s;
        }

        .btn-submit:hover { background: var(--sxc-dark); transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0,33,71,0.2); }

        .domain-note { font-size: 0.8rem; color: var(--sxc-gold); margin-top: 5px; font-weight: 500; }
          .back-link {
            text-align: center;
            margin-top: 25px;
            font-size: 0.9rem;
        }
         .back-link a { color: var(--sxc-blue); text-decoration: none; font-weight: 600; }
    </style>
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