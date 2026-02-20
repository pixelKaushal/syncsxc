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
    die("<script>alert('This event is not approved yet.'); window.location.href='../public/event-details.php?id=$eventId';</script>");
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
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/register.css">

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
                    
                    <a href="../public/event-details.php?id=<?php echo $eventId; ?>" class="btn btn-secondary">
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