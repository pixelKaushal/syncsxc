<?php
require_once '../backend/data.php';
session_start();

// ===== CHECK LOGIN =====
if (!isset($_SESSION['user_id'])) {
    die("<script>alert('Please login to register.'); window.location.href='../backend/login.php';</script>");
}

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'];

// ===== CHECK EVENT ID =====
if (!isset($_GET['id'])) {
    die("<script>alert('Event ID is missing.'); window.location.href='events.php';</script>");
}

$eventId = (int)$_GET['id'];

// ===== FETCH EVENT =====
$eventResult = eventbyid($eventId);
if (!$eventResult || $eventResult->num_rows === 0) {
    die("<script>alert('Event not found.'); window.location.href='events.php';</script>");
}
$event = $eventResult->fetch_assoc();

// ===== CHECK IF EVENT IS INDIVIDUAL EVENT =====
if ($event['is_team_event'] == 1) {
    die("<script>alert('This is a team event. Please use team registration.'); window.location.href='team_register.php?id=$eventId';</script>");
}

// ===== CHECK IF EVENT IS APPROVED =====
if (strtolower($event['approval_status']) !== 'approved') {
    die("<script>alert('This event is not approved yet.'); window.location.href='event-details.php?id=$eventId';</script>");
}

// ===== CHECK IF USER ALREADY REGISTERED =====
$checkStmt = $conn->prepare("SELECT registration_id FROM registrations WHERE user_id = ? AND event_id = ? AND status != 'cancelled'");
$checkStmt->bind_param("ii", $user_id, $eventId);
$checkStmt->execute();
if ($checkStmt->get_result()->num_rows > 0) {
    echo "<script>
        alert('You have already registered for this event.');
        window.location.href='../public/registrations.php';
    </script>";
    exit;
}

// ===== HANDLE REGISTRATION =====
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // ===== INSERT REGISTRATION =====
    $payment_status = ($event['price'] == 0) ? 'paid' : 'pending';
    
    $insert = $conn->prepare("
        INSERT INTO registrations 
        (user_id, event_id, club_id, payment_amount, payment_status) 
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $insert->bind_param("iiids", 
        $user_id, 
        $eventId, 
        $event['club_id'],
        $event['price'],
        $payment_status
    );
    
    if ($insert->execute()) {
        $success_message = "Registration successful!";
        // Redirect to registrations page after 2 seconds
        header("refresh:2;url=../public/registrations.php");
    } else {
        $error_message = "Registration failed: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | <?php echo htmlspecialchars($event['title']); ?></title>
    
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
            background: linear-gradient(135deg, var(--bg-light), #e9ecef);
            color: var(--text-dark);
            line-height: 1.6;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 2.2rem;
            font-weight: 800;
            font-family: var(--font-heading);
            color: var(--sxc-maroon);
            margin-bottom: 10px;
        }

        .header p {
            color: var(--text-muted);
            font-size: 1rem;
        }

        .event-badge {
            display: inline-block;
            background: var(--sxc-maroon-light);
            color: var(--sxc-maroon);
            padding: 6px 20px;
            border-radius: var(--radius-full);
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 15px;
            border: 1px solid var(--sxc-maroon);
        }

        /* Main Card */
        .register-card {
            background: var(--white);
            border-radius: var(--radius-xl);
            padding: 35px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-light);
            position: relative;
            overflow: hidden;
        }

        .register-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--sxc-maroon), var(--sxc-gold));
        }

        /* Event Info */
        .event-info {
            background: var(--bg-light);
            border-radius: var(--radius-lg);
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid var(--border-light);
            text-align: center;
        }

        .event-title {
            font-size: 1.8rem;
            font-weight: 800;
            font-family: var(--font-heading);
            color: var(--text-dark);
            margin-bottom: 15px;
        }

        .event-meta {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .meta-item i {
            color: var(--sxc-gold);
        }

        .price-tag {
            font-size: 2rem;
            font-weight: 800;
            color: var(--sxc-maroon);
            margin: 15px 0;
        }

        .price-tag small {
            font-size: 0.9rem;
            font-weight: 400;
            color: var(--text-muted);
        }

        /* User Info */
        .user-info {
            background: linear-gradient(135deg, var(--sxc-maroon-light), var(--bg-light));
            border-radius: var(--radius-lg);
            padding: 20px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            border: 1px solid var(--sxc-maroon);
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            background: var(--sxc-maroon);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-avatar i {
            font-size: 1.5rem;
            color: white;
        }

        .user-details {
            flex: 1;
        }

        .user-details h4 {
            font-size: 0.9rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .user-details p {
            font-weight: 600;
            color: var(--text-dark);
            word-break: break-word;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 16px 32px;
            border-radius: var(--radius-full);
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        .btn-primary {
            background: var(--sxc-maroon);
            color: var(--white);
            box-shadow: 0 8px 20px rgba(6, 103, 164, 0.2);
            margin-bottom: 15px;
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
            transform: translateY(-2px);
        }

        /* Alert Messages */
        .alert {
            padding: 16px 20px;
            border-radius: var(--radius-lg);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .alert i {
            font-size: 1.2rem;
        }

        /* Terms */
        .terms-note {
            background: var(--bg-light);
            border-radius: var(--radius-lg);
            padding: 15px;
            margin-top: 20px;
            font-size: 0.9rem;
            color: var(--text-muted);
            text-align: center;
            border: 1px solid var(--border-light);
        }

        .terms-note i {
            color: var(--sxc-gold);
            margin-right: 5px;
        }

        .terms-note a {
            color: var(--sxc-maroon);
            text-decoration: none;
            font-weight: 600;
        }

        .terms-note a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .register-card {
                padding: 25px;
            }
            
            .header h1 {
                font-size: 1.8rem;
            }
            
            .event-title {
                font-size: 1.5rem;
            }
            
            .price-tag {
                font-size: 1.8rem;
            }
            
            .event-meta {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }
        }

        @media (max-width: 480px) {
            .register-card {
                padding: 20px;
            }
            
            .user-info {
                flex-direction: column;
                text-align: center;
            }
            
            .btn {
                padding: 14px 24px;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="event-badge">
                <i class="fas fa-user"></i> Individual Registration
            </div>
            <h1>Register for Event</h1>
            <p>Complete your registration below</p>
        </div>

        <!-- Main Card -->
        <div class="register-card">
            <!-- Event Info -->
            <div class="event-info">
                <h2 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h2>
                
                <div class="event-meta">
                    <div class="meta-item">
                        <i class="fas fa-calendar"></i>
                        <span><?php echo date('F j, Y', strtotime($event['proposed_date'])); ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?php echo htmlspecialchars($event['venue']); ?></span>
                    </div>
                </div>
                
                <div class="price-tag">
                    <?php echo $event['price'] > 0 ? 'NPR ' . number_format($event['price']) : 'FREE'; ?>
                    <?php if ($event['price'] == 0): ?>
                        <small>(No payment required)</small>
                    <?php else: ?>
                        <small>(Payment pending)</small>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Success/Error Messages -->
            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong>Success!</strong><br>
                        <?php echo $success_message; ?>
                        <p style="margin-top: 10px; font-size: 0.9rem;">Redirecting to your registrations...</p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        <strong>Error!</strong><br>
                        <?php echo $error_message; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Registration Form -->
            <?php if (!$success_message): ?>
                <!-- User Info -->
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-details">
                        <h4>Registering as</h4>
                        <p><?php echo htmlspecialchars($user_email); ?></p>
                    </div>
                </div>

                <form method="POST">
                    <button type="submit" class="btn btn-primary" id="registerBtn">
                        <i class="fas fa-check-circle"></i>
                        Confirm Registration
                    </button>
                    
                    <a href="event-details.php?id=<?php echo $eventId; ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                </form>

                <!-- Terms Note -->
                <div class="terms-note">
                    <i class="fas fa-info-circle"></i>
                    By registering, you agree to the event terms and conditions.
                    <?php if ($event['price'] > 0): ?>
                        <br><strong>Note:</strong> You will need to complete payment to confirm your registration.
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <a href="../public/registrations.php" class="btn btn-primary">
                    <i class="fas fa-eye"></i>
                    View My Registrations
                </a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Prevent double submission
        document.getElementById('registerBtn')?.addEventListener('click', function(e) {
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            this.form.submit();
        });

        // Warn if leaving page
        window.addEventListener('beforeunload', function(e) {
            e.preventDefault();
            e.returnValue = '';
        });
    </script>
</body>
</html>