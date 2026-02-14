<?php
require_once '../backend/data.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../backend/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'];

// ===== AUTO-UPDATE FREE EVENTS TO PAID IN DATABASE =====
$conn->query("
    UPDATE registrations r 
    JOIN events e ON r.event_id = e.event_id 
    SET r.payment_status = 'paid' 
    WHERE e.price = 0 AND r.payment_status != 'paid'
");

// ===== GET REGISTRATIONS WHERE USER IS LEADER =====
$leader_query = $conn->prepare("
    SELECT r.*, 
           e.title, 
           e.proposed_date, 
           e.venue, 
           e.is_team_event, 
           e.price,
           c.name as club_name,
           'leader' as member_type
    FROM registrations r
    JOIN events e ON r.event_id = e.event_id
    JOIN clubs c ON r.club_id = c.id
    WHERE r.user_id = ? AND r.status != 'cancelled'
    ORDER BY r.registration_date DESC
");
$leader_query->bind_param("i", $user_id);
$leader_query->execute();
$leader_registrations = $leader_query->get_result();

// ===== GET REGISTRATIONS WHERE USER IS A TEAM MEMBER =====
$member_query = $conn->prepare("
    SELECT r.*, 
           e.title, 
           e.proposed_date, 
           e.venue, 
           e.is_team_event, 
           e.price,
           c.name as club_name,
           'member' as member_type,
           u.primary_email as leader_email,
           u.id as leader_id
    FROM registrations r
    JOIN events e ON r.event_id = e.event_id
    JOIN clubs c ON r.club_id = c.id
    JOIN users u ON r.user_id = u.id
    WHERE r.team_members IS NOT NULL 
      AND JSON_SEARCH(r.team_members, 'one', ?) IS NOT NULL
      AND r.status != 'cancelled'
    ORDER BY r.registration_date DESC
");
$member_query->bind_param("s", $user_email);
$member_query->execute();
$member_registrations = $member_query->get_result();

// Combine results (store in arrays)
$all_registrations = [];

while ($row = $leader_registrations->fetch_assoc()) {
    $row['team_members_data'] = json_decode($row['team_members'], true);
    // No need to modify payment_status here - it's already updated in DB
    $all_registrations[] = $row;
}

while ($row = $member_registrations->fetch_assoc()) {
    $row['team_members_data'] = json_decode($row['team_members'], true);
    // No need to modify payment_status here - it's already updated in DB
    $all_registrations[] = $row;
}

// Sort by registration date (NEWEST FIRST - DESCENDING)
usort($all_registrations, function($a, $b) {
    return strtotime($b['registration_date']) - strtotime($a['registration_date']);
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Registrations | SyncSXC</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/registrations.css">

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
                <a href="registrations.php" class="nav-link active">My Registrations</a>
                <a href="profile.php" class="nav-link">Profile</a>
                <a href="../backend/logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1>
                <i class="fas fa-ticket-alt"></i>
                My Event Registrations
            </h1>
            <p>View all events you're participating in as leader or member</p>
        </div>
        
        <!-- Sort Indicator -->
        <div class="sort-indicator">
            <i class="fas fa-arrow-down"></i>
            <span>Newest registrations first (most recent at top)</span>
        </div>
        
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo count($all_registrations); ?></h3>
                    <p>Total Registrations</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="stat-content">
                    <h3>
                        <?php 
                        $leader_count = 0;
                        foreach ($all_registrations as $reg) {
                            if ($reg['member_type'] === 'leader') $leader_count++;
                        }
                        echo $leader_count;
                        ?>
                    </h3>
                    <p>As Team Leader</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo count($all_registrations) - $leader_count; ?></h3>
                    <p>As Team Member</p>
                </div>
            </div>
        </div>
        
        <?php if (empty($all_registrations)): ?>
            <!-- Empty State -->
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <h3>No Registrations Yet</h3>
                <p>You haven't registered for any events or been added to any teams.</p>
                <a href="events.php" class="btn btn-primary">
                    <i class="fas fa-calendar-alt"></i> Browse Events
                </a>
            </div>
        <?php else: ?>
            <!-- Registration Cards -->
            <?php foreach ($all_registrations as $index => $reg): ?>
                <div class="registration-card <?php echo $reg['member_type']; ?>-card" style="animation-delay: <?php echo $index * 0.1; ?>s">
                    <!-- Role Badge -->
                    <span class="role-badge <?php echo $reg['member_type']; ?>">
                        <i class="fas fa-<?php echo $reg['member_type'] === 'leader' ? 'crown' : 'user'; ?>"></i>
                        Team <?php echo $reg['member_type'] === 'leader' ? 'Leader' : 'Member'; ?>
                    </span>
                    
                    <!-- Registration Date -->
                    <div class="registration-date-badge">
                        <i class="fas fa-clock"></i>
                        Registered: <?php echo date('M d, Y \a\t g:i A', strtotime($reg['registration_date'])); ?>
                    </div>
                    
                    <!-- Team Name (if exists) -->
                    <?php if ($reg['team_name']): ?>
                        <span class="team-badge">
                            <i class="fas fa-users"></i> 
                            <?php echo htmlspecialchars($reg['team_name']); ?>
                        </span>
                    <?php endif; ?>
                    
                    <!-- Event Title -->
                    <h2 class="event-title"><?php echo htmlspecialchars($reg['title']); ?></h2>
                    
                    <!-- Event Info Grid -->
                    <div class="info-grid">
                        <div class="info-item">
                            <i class="fas fa-calendar"></i>
                            <span>Date: <strong><?php echo date('F j, Y', strtotime($reg['proposed_date'])); ?></strong></span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Venue: <strong><?php echo htmlspecialchars($reg['venue']); ?></strong></span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-flag"></i>
                            <span>Club: <strong><?php echo htmlspecialchars($reg['club_name']); ?></strong></span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-tag"></i>
                            <span>Payment: 
                                <strong class="payment-status status-<?php echo $reg['payment_status']; ?>">
                                    <?php echo ucfirst($reg['payment_status']); ?>
                                    <?php if ($reg['price'] == 0 && $reg['payment_status'] == 'paid'): ?>
                                        <i class="fas fa-check-circle" style="margin-left: 3px;"></i>
                                    <?php endif; ?>
                                </strong>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Enhanced Team Members Section -->
                    <?php if ($reg['team_members_data'] || $reg['member_type'] === 'leader'): ?>
                        <details class="team-section" <?php echo $index === 0 ? 'open' : ''; ?>>
                            <summary class="team-header">
                                <div class="team-header-left">
                                    <i class="fas fa-users"></i>
                                    <h4>Team Members</h4>
                                    <span><?php echo ($reg['team_members_data'] ? count($reg['team_members_data']) : 0) + 1; ?> members</span>
                                </div>
                                <i class="fas fa-chevron-down"></i>
                            </summary>
                            
                            <div class="team-content">
                                <!-- Team Leader Card (Clickable) -->
                                <div class="team-leader-card" onclick="window.location.href='profile.php?uid=<?php echo $reg['member_type'] === 'leader' ? $user_id : $reg['leader_id']; ?>'">
                                    <div class="leader-avatar">
                                        <i class="fas fa-crown"></i>
                                    </div>
                                    <div class="leader-info">
                                        <h5>Team Leader</h5>
                                        <p>
                                            <?php 
                                            if ($reg['member_type'] === 'leader') {
                                                echo '<span class="leader-email-link">' . htmlspecialchars($user_email) . '</span>';
                                                echo ' <span class="current-user-badge"><i class="fas fa-check-circle"></i> You</span>';
                                            } else {
                                                echo '<span class="leader-email-link">' . htmlspecialchars($reg['leader_email']) . '</span>';
                                                if ($reg['leader_email'] === $user_email) {
                                                    echo ' <span class="current-user-badge"><i class="fas fa-check-circle"></i> You</span>';
                                                }
                                            }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Team Members Grid -->
                                <?php if ($reg['team_members_data']): ?>
                                    <h5 style="margin-bottom: 15px; color: var(--text-muted);">
                                        <i class="fas fa-user-friends"></i> Team Members
                                    </h5>
                                    <div class="team-members-grid">
                                        <?php foreach ($reg['team_members_data'] as $index => $member): 
                                            // Get user ID from email
                                            $member_id_query = $conn->prepare("SELECT id FROM users WHERE primary_email = ?");
                                            $member_id_query->bind_param("s", $member['email']);
                                            $member_id_query->execute();
                                            $member_result = $member_id_query->get_result();
                                            $member_user = $member_result->fetch_assoc();
                                            $member_id = $member_user['id'] ?? 0;
                                        ?>
                                            <div class="team-member-card" onclick="window.location.href='profile.php?uid=<?php echo $member_id; ?>'">
                                                <span class="member-number"><?php echo $index + 1; ?></span>
                                                <div class="member-avatar">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <div class="member-details">
                                                    <span class="member-email-link"><?php echo htmlspecialchars($member['email']); ?></span>
                                                    <?php if ($member['email'] === $user_email): ?>
                                                        <span class="member-badge"><i class="fas fa-check-circle"></i> You</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </details>
                    <?php endif; ?>
                    
                    <!-- View Event Link -->
                    <a href="event-details.php?id=<?php echo $reg['event_id']; ?>" class="view-link">
                        View Event Details <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <p>&copy; <?php echo date('Y'); ?> SyncSXC. All rights reserved. | St. Xavier's College, Maitighar</p>
            </div>
        </div>
    </footer>

    <!-- Mobile Menu JavaScript -->
    <script src="../js/registrations.js">
    </script>
</body>
</html>