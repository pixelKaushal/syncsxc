<?php
require_once '../backend/data.php';
session_start();

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

/// ===== FETCH CLUB DETAILS =====
$club_name = 'Unknown Club';
$club_logo = null;
if (!empty($event['club_id'])) {
    $club = clubbyid($event['club_id']);
    
    // FIX: Check if $club is an array before using it
    if ($club && is_array($club)) {
        $club_name = $club['name'] ?? 'Unknown Club';
        $club_logo = $club['logo_path'] ?? null;
    } else {
        // If getClubById returns mysqli_result, fetch it properly
        if ($club && $club->num_rows > 0) {
            $club_data = $club->fetch_assoc();
            $club_name = $club_data['name'] ?? 'Unknown Club';
            $club_logo = $club_data['logo_path'] ?? null;
        }
    }
}
// ===== PROCESS EVENT DATA =====
$teamevent = $event['is_team_event'] ? "Yes" : "No";

$minmember = 1;
$maxmember = 1;

if ($event['is_team_event'] == 1) {
    $minmember = (int)($event['min_team_size'] ?? 1);
    $maxmember = (int)($event['max_team_size'] ?? 1);
}

// ===== PRICE FORMATTING =====
$price_value = (float)($event['price'] ?? 0);
$price_display = ($price_value <= 0) ? "Free" : "NPR " . number_format($price_value, 0);

// ===== STATUS CHECK =====
$status = strtolower($event['approval_status'] ?? 'pending');
$is_approved = ($status === 'approved');

// ===== DATE FORMATTING =====
$event_date = date('F j, Y', strtotime($event['proposed_date']));
$event_time = date('g:i A', strtotime($event['proposed_date']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event['title']); ?> | SyncSXC</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/event-details.css">
    
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="../index.php" class="logo">
                <i class="fas fa-sync-alt"></i>
                Sync<span class="logo-gold">SXC</span>
            </a>
            
            <div class="nav-links">
                <a href="../index.php" class="nav-link">Home</a>
                <a href="events.php" class="nav-link active">Events</a>
                <a href="clubs.php" class="nav-link">Clubs</a>
                <a href="schedule.php" class="nav-link">Schedule</a>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="../backend/login.php" class="nav-link">Login</a>
                <?php else: ?>
                    <a href="profile.php" class="nav-link">Profile</a>
                    <a href="../backend/logout.php" class="nav-link">Logout</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Back Link -->
        <div class="back-nav">
            <a href="events.php" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Back to Events
            </a>
        </div>

        <!-- Event Hero -->
        <div class="event-hero">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 20px;">
                <span class="event-category">
                    <i class="fas fa-<?php echo $teamevent === 'Yes' ? 'users' : 'user'; ?>"></i>
                    <?php echo $teamevent === 'Yes' ? 'Team Event' : 'Individual Event'; ?>
                </span>
                
                <span class="status-badge status-<?php echo $is_approved ? 'approved' : 'pending'; ?>">
                    <i class="fas fa-circle" style="font-size: 8px;"></i>
                    <?php echo $is_approved ? 'Approved' : 'Pending Approval'; ?>
                </span>
            </div>
            
            <h1 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h1>
            
            <div class="event-meta-grid">
                <div class="meta-card">
                    <div class="meta-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="meta-content">
                        <h4>Date</h4>
                        <p><?php echo $event_date; ?></p>
                    </div>
                </div>
                
                <div class="meta-card">
                    <div class="meta-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="meta-content">
                        <h4>Time</h4>
                        <p><?php echo $event_time; ?></p>
                    </div>
                </div>
                
                <div class="meta-card">
                    <div class="meta-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="meta-content">
                        <h4>Venue</h4>
                        <p><?php echo htmlspecialchars($event['venue']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="event-content">
            <!-- Left Column -->
            <div class="event-main">
                <h2 class="section-title">
                    <i class="fas fa-info-circle"></i>
                    About This Event
                </h2>
                
                <div class="event-description">
                    <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                </div>
                
                <?php if ($teamevent === "Yes"): ?>
                <div style="margin-top: 40px;">
                    <h2 class="section-title">
                        <i class="fas fa-users"></i>
                        Team Details
                    </h2>
                    
                    <div class="team-info">
                        <h4>
                            <i class="fas fa-info-circle"></i>
                            Team Size Requirements
                        </h4>
                        <div class="team-size">
                            <div class="team-size-item">
                                <div class="label">Minimum</div>
                                <div class="value"><?php echo $minmember; ?></div>
                            </div>
                            <div class="team-size-item">
                                <div class="label">Maximum</div>
                                <div class="value"><?php echo $maxmember; ?></div>
                            </div>
                        </div>
                        <p style="color: var(--text-muted); font-size: 0.9rem; text-align: center;">
                            <i class="fas fa-info-circle"></i> 
                            You can register with <?php echo $minmember - 1; ?> to <?php echo $maxmember - 1; ?> additional team members.
                        </p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Right Column -->
            <div class="event-sidebar">
                <!-- Club Information -->
                <div class="club-card">
                    <div class="club-logo">
                        <?php if ($club_logo && file_exists("../img/{$club_logo}")): ?>
                            <img src="../img/<?php echo htmlspecialchars($club_logo); ?>" alt="<?php echo htmlspecialchars($club_name); ?>">
                        <?php else: ?>
                            <i class="fas fa-users"></i>
                        <?php endif; ?>
                    </div>
                    <div class="club-info">
                        <h3><?php echo htmlspecialchars($club_name); ?></h3>
                        <p>Organizing Club</p>
                    </div>
                </div>
                
                <!-- Registration Card -->
                <div class="registration-card">
                    <div class="price-tag">
                        <div class="amount"><?php echo $price_display; ?></div>
                        <?php if ($price_value > 0): ?>
                            <div class="period">per <?php echo $teamevent === 'Yes' ? 'team' : 'person'; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!$is_approved): ?>
                        <div class="alert">
                            <i class="fas fa-clock"></i>
                            <div>
                                <strong>Event Pending Approval</strong>
                                <p style="font-size: 0.9rem; margin-top: 5px;">Registration will open once approved by college council.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <button id="enroll" class="btn btn-primary" <?php echo !$is_approved ? 'disabled' : ''; ?>>
                        <i class="fas fa-<?php echo $teamevent === 'Yes' ? 'users' : 'check-circle'; ?>"></i>
                        <?php echo $teamevent === 'Yes' ? 'Register Team' : 'Register Now'; ?>
                    </button>
                </div>
                
                <!-- Event Details Table -->
                <table class="info-table">
                    <tr>
                        <td>Event ID</td>
                        <td>#<?php echo $eventId; ?></td>
                    </tr>
                    <tr>
                        <td>Registration Type</td>
                        <td><?php echo $teamevent === 'Yes' ? 'Team' : 'Individual'; ?></td>
                    </tr>
                    <tr>
                        <td>Available Spots</td>
                        <td>Unlimited</td>
                    </tr>
                    <tr>
                        <td>Last Updated</td>
                        <td><?php echo date('M d, Y'); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <p>&copy; <?php echo date('Y'); ?> SyncSXC. All rights reserved. | St. Xavier's College, Maitighar</p>
            </div>
        </div>
    </footer>

    <script>
        var isApproved = <?php echo json_encode($is_approved); ?>;
        var isTeamEvent = <?php echo json_encode($teamevent === "Yes"); ?>;
        
        document.getElementById('enroll').addEventListener('click', function() {
            if (!isApproved) {
                alert('This event is not approved yet. Enrollment is not allowed.');
                return;
            }
            
            if (isTeamEvent) {
                window.location.href = '../backend/team_register.php?id=<?php echo $eventId; ?>';
            } else {
                window.location.href = '../backend/register.php?id=<?php echo $eventId; ?>';
            }
        });
    </script>
</body>
</html>