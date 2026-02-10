    <?php
session_start();
// Redirect if already logged in
if(isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
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
    <style>
        :root {
            --sxc-blue: #0667A4;
            --sxc-dark-blue: #044d7a;
            --sxc-gold: #d4af37;
            --white: #ffffff;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }

        body {
            background: #f0f2f5;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .login-container {
            width: 1000px;
            height: 600px;
            background: var(--white);
            display: flex;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
            overflow: hidden;
        }

        /* Left Side - Visual Aesthetic */
        .login-side-content {
            flex: 1.2;
            background: linear-gradient(135deg, var(--sxc-blue) 0%, var(--sxc-dark-blue) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            color: var(--white);
            position: relative;
        }

        .login-side-content::before {
            content: "";
            position: absolute;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            top: -50px;
            left: -50px;
        }

        .side-logo {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .side-logo span { color: var(--sxc-gold); }

        .side-text h2 { font-size: 2rem; margin-bottom: 10px; }
        .side-text p { opacity: 0.8; font-weight: 300; line-height: 1.6; }

        /* Right Side - Form */
        .login-form-section {
            flex: 1;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header { margin-bottom: 40px; }
        .form-header h1 { font-size: 2rem; color: var(--sxc-dark-blue); }
        .form-header p { color: #888; margin-top: 5px; }

        .input-group { margin-bottom: 25px; position: relative; }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--sxc-blue);
        }

        .input-group input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #eee;
            border-radius: 12px;
            outline: none;
            transition: 0.3s;
            font-size: 1rem;
        }

        .input-group input:focus { border-color: var(--sxc-blue); }

        .login-btn {
            background: var(--sxc-blue);
            color: white;
            padding: 15px;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 10px 20px rgba(6, 103, 164, 0.2);
        }

        .login-btn:hover { background: var(--sxc-dark-blue); transform: translateY(-2px); }

        .back-link {
            text-align: center;
            margin-top: 25px;
            font-size: 0.9rem;
        }

        .back-link a { color: var(--sxc-blue); text-decoration: none; font-weight: 600; }

        /* Mobile Responsive */
        @media (max-width: 900px) {
            .login-side-content { display: none; }
            .login-container { width: 90%; height: auto; }
        }
    </style>
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

            <form action="backend/login_process.php" method="POST">
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