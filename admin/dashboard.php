<?php
require_once '../backend/data.php';
session_start();
if($_SESSION['role'] !== 'admin') {
    die("<script>alert('Access Denied: Admins only.'); window.location.href='../index.php';</script>");
}
$club_name = $_SESSION['club_name'] ?? 'Unknown Club';
$club_id = $_SESSION['club_id'] ?? null;
if (!$club_id) {
    die("Session error: Club ID not found. Please log in again.");
}

function fetchClubMembers($club_id) {
    global $conn;
    $query = $conn->prepare("SELECT * FROM `users` where club_id = ?");
    $query->bind_param("i", $club_id);
    $query->execute();
    return $query->get_result()->fetch_all(MYSQLI_ASSOC);
}

function fetchClubEvents($club_id) {
    global $conn;
    $query = $conn->prepare("SELECT * FROM `events` where club_id = ?");
    $query->bind_param("i", $club_id);
    $query->execute();
    return $query->get_result()->fetch_all(MYSQLI_ASSOC);
}

$members = fetchClubMembers($club_id);
$events = fetchClubEvents($club_id);
$members_count = count($members);
$events_count = count($events);

// post =======================================================================================
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $proposed_date = $_POST['proposed_date'];
    $description = trim($_POST['description']);
    $venue = trim($_POST['venue']);
    $approval_status = $_POST['approval_status'];
    $is_team_event = $_POST['is_team_event'];
    $min_team_size = $_POST['min_team_size'];
    $max_team_size = $_POST['max_team_size'];
    $price = $_POST['price'];
    
    if ($min_team_size > $max_team_size) {
        die("<script>alert('Error: Minimum team size cannot be greater than maximum.'); window.history.back();</script>");
    }
    
    $insert_query = $conn->prepare("INSERT INTO `events` (club_id, title, proposed_date, description, venue, approval_status, is_team_event, min_team_size, max_team_size, price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insert_query->bind_param("isssssiiii", $club_id, $title, $proposed_date, $description, $venue, $approval_status, $is_team_event, $min_team_size, $max_team_size, $price);
    
    if($insert_query->execute()) {
        echo "<script>alert('Event created successfully.'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error creating event: " . addslashes($conn->error) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | SyncSXC · <?php echo htmlspecialchars($club_name); ?></title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    
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
            
            <div class="nav-links">
                <a href="../index.php" class="nav-link">
                    <i class="fas fa-home"></i> Home
                </a>
                <a href="dashboard.php" class="nav-link active">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
                <a href="../admin/event_manager.php" class="nav-link">
                    <i class="fas fa-calendar-alt"></i> Event Manager
                </a>
                
                <div class="user-badge">
                    <i class="fas fa-user-shield"></i>
                    <span><?php echo htmlspecialchars($_SESSION['role']); ?></span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="container">
            <div class="header-content">
                <div class="header-title">
                    <h1>
                        <i class="fas fa-tachometer-alt"></i>
                        Admin Dashboard
                    </h1>
                    <div class="club-badge">
                        <i class="fas fa-flag"></i>
                        <span><?php echo htmlspecialchars($club_name); ?></span>
                        <i class="fas fa-hashtag" style="margin-left: 5px;"></i>
                        <span style="color: var(--text-muted);">ID: <?php echo htmlspecialchars($club_id); ?></span>
                    </div>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Members</h3>
                            <p><?php echo $members_count; ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Events</h3>
                            <p><?php echo $events_count; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            
            <!-- Create Event Section -->
            <div class="section" id="create_event">
                <div class="section-header">
                    <h2>
                        <i class="fas fa-plus-circle"></i>
                        Create New Event
                        <span>New</span>
                    </h2>
                </div>
                
                <form method="post" class="event-form">
                    <div class="form-group">
                        <label>
                            <i class="fas fa-heading"></i>
                            Event Title
                        </label>
                        <input type="text" name="title" placeholder="e.g. Annual Tech Fest" required>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <i class="fas fa-calendar"></i>
                            Proposed Date
                        </label>
                        <input type="date" name="proposed_date" required>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <i class="fas fa-map-marker-alt"></i>
                            Venue
                        </label>
                        <input type="text" name="venue" placeholder="e.g. Main Auditorium" required>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <i class="fas fa-check-circle"></i>
                            Approval Status
                        </label>
                        <select name="approval_status" required>
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                            <option value="TBD">TBD</option>
                            <option value="Draft">Draft</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <i class="fas fa-users"></i>
                            Team Event?
                        </label>
                        <select name="is_team_event" required>
                            <option value="" hidden>Select option</option>
                            <option value="1">Yes - Team Event</option>
                            <option value="0">No - Individual Event</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <i class="fas fa-tag"></i>
                            Event Price (NPR)
                        </label>
                        <input type="number" name="price" placeholder="0 for free" min="0" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-user-minus"></i>
                                Min Team Size
                            </label>
                            <input type="number" name="min_team_size" placeholder="1" min="1" required>
                        </div>
                        
                        <div class="form-group">
                            <label>
                                <i class="fas fa-user-plus"></i>
                                Max Team Size
                            </label>
                            <input type="number" name="max_team_size" placeholder="5" min="1" required>
                        </div>
                    </div>
                    
                    <div class="form-group full-width">
                        <label>
                            <i class="fas fa-align-left"></i>
                            Description
                        </label>
                        <textarea name="description" placeholder="Describe your event in detail..." required></textarea>
                    </div>
                    
                    <div class="form-group full-width">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i>
                            Create Event
                        </button>
                    </div>
                </form>
            </div>
            
            
            <!-- Club Events Section -->
            <div class="section">
                <div class="section-header">
                    <h2>
                        <i class="fas fa-calendar-alt"></i>
                        Club Events
                        <span><?php echo $events_count; ?></span>
                    </h2>
                </div>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Event ID</th>
                                <th>Event Name</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($events)): ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 40px;">
                                        <i class="fas fa-calendar-times" style="font-size: 2rem; color: var(--text-muted); margin-bottom: 10px;"></i>
                                        <p style="color: var(--text-muted);">No events created yet</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($events as $event): ?>
                                <tr>
                                    <td><strong>#<?php echo htmlspecialchars($event['event_id']); ?></strong></td>
                                    <td>
                                        <i class="fas fa-calendar" style="color: var(--sxc-gold); margin-right: 8px;"></i>
                                        <?php echo htmlspecialchars($event['title']); ?>
                                    </td>
                                    <td>
                                        <i class="far fa-calendar-alt" style="color: var(--sxc-maroon); margin-right: 8px;"></i>
                                        <?php echo date('M d, Y', strtotime($event['proposed_date'])); ?>
                                    </td>
                                    <td>
                                        <?php
                                        $status = $event['approval_status'] ?? 'Pending';
                                        $status_class = strtolower($status);
                                        ?>
                                        <span class="event-badge <?php echo $status_class; ?>">
                                            <i class="fas fa-circle" style="font-size: 8px;"></i>
                                            <?php echo htmlspecialchars($status); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn edit" data-event-id="<?php echo htmlspecialchars($event['event_id']); ?>">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="action-btn delete" data-event-id="<?php echo htmlspecialchars($event['event_id']); ?>">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Club Members Section -->
            <div class="section">
                <div class="section-header">
                    <h2>
                        <i class="fas fa-users"></i>
                        Club Members
                        <span><?php echo $members_count; ?></span>
                    </h2>
                </div>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Email</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($members)): ?>
                                <tr>
                                    <td colspan="3" style="text-align: center; padding: 40px;">
                                        <i class="fas fa-users-slash" style="font-size: 2rem; color: var(--text-muted); margin-bottom: 10px;"></i>
                                        <p style="color: var(--text-muted);">No members found</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($members as $member): ?>
                                <tr>
                                    <td><strong>#<?php echo htmlspecialchars($member['id']); ?></strong></td>
                                    <td>
                                        <i class="fas fa-envelope" style="color: var(--sxc-maroon); margin-right: 8px;"></i>
                                        <?php echo htmlspecialchars($member['primary_email']); ?>
                                    </td>
                                    <td>
                                        <?php if($member['role'] === 'admin'): ?>
                                            <span style="background: var(--sxc-maroon-light); color: var(--sxc-maroon); padding: 4px 12px; border-radius: var(--radius-full); font-weight: 600;">
                                                <i class="fas fa-crown"></i> Admin
                                            </span>
                                        <?php else: ?>
                                            <span style="background: var(--sxc-gold-light); color: var(--sxc-gold-dark); padding: 4px 12px; border-radius: var(--radius-full); font-weight: 600;">
                                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($member['role']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Button -->
    <div class="logout-container">
        <a href="../backend/logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            Logout
        </a>
    </div>

    <!-- Scroll to Top Button -->
    <div class="scroll-top" id="scrollTop">
        <i class="fas fa-arrow-up"></i>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <p>&copy; <?php echo date('Y'); ?> SyncSXC. All rights reserved. | St. Xavier's College, Maitighar</p>
            </div>
        </div>
    </footer>
    <script src="../js/dashboard.js"></script>
</body>
</html>