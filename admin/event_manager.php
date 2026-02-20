<?php
require_once '../backend/data.php';
require_once '../backend/club_auth.php'; // Create this file (see below)
session_start();

// ===== PROTECT PAGE - ONLY CLUB ADMINS =====
requireClubAdmin();

$club_id = getClubId();
$club = getClubById($club_id);
$club_name = $club['name'] ?? 'Your Club';

// ===== GET FILTERS FROM URL =====
$status_filter = $_GET['status'] ?? 'all';
$event_filter = $_GET['event_id'] ?? 'all';
$payment_filter = $_GET['payment'] ?? 'all';
$date_from = $_GET['date_from'] ?? date('Y-m-d', strtotime('-30 days'));
$date_to = $_GET['date_to'] ?? date('Y-m-d');

// ===== FETCH ALL CLUB EVENTS FOR DROPDOWN =====
$events_query = $conn->prepare("
    SELECT event_id, title, proposed_date 
    FROM events 
    WHERE club_id = ? 
    ORDER BY proposed_date DESC
");
$events_query->bind_param("i", $club_id);
$events_query->execute();
$club_events = $events_query->get_result();

// ===== FETCH REGISTRATIONS WITH FILTERS =====
$sql = "
    SELECT 
        r.*,
        e.title as event_title,
        e.proposed_date as event_date,
        e.price as event_price,
        e.venue as event_venue,
        u.primary_email as user_email,
        u.id as user_id
    FROM registrations r
    JOIN events e ON r.event_id = e.event_id
    JOIN users u ON r.user_id = u.id
    WHERE r.club_id = ?";

$params = [$club_id];
$types = "i";

// Apply event filter
if ($event_filter !== 'all') {
    $sql .= " AND r.event_id = ?";
    $params[] = (int)$event_filter;
    $types .= "i";
}

// Apply payment status filter
if ($payment_filter !== 'all') {
    $sql .= " AND r.payment_status = ?";
    $params[] = $payment_filter;
    $types .= "s";
}

// Apply date range filter
$sql .= " AND DATE(r.registration_date) BETWEEN ? AND ?";
$params[] = $date_from;
$params[] = $date_to;
$types .= "ss";

// Apply registration status filter (exclude cancelled)
if ($status_filter === 'active') {
    $sql .= " AND r.status = 'registered'";
} elseif ($status_filter === 'attended') {
    $sql .= " AND r.status = 'attended'";
} elseif ($status_filter === 'cancelled') {
    $sql .= " AND r.status = 'cancelled'";
} else {
    $sql .= " AND r.status != 'cancelled'"; 
}

$sql .= " ORDER BY r.registration_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$registrations = $stmt->get_result();

// ===== GET STATISTICS =====
$stats = [];

// Total registrations
$stats_query = $conn->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid,
        SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'attended' THEN 1 ELSE 0 END) as attended,
        SUM(payment_amount) as revenue
    FROM registrations 
    WHERE club_id = ? AND status != 'cancelled'
");
$stats_query->bind_param("i", $club_id);
$stats_query->execute();
$stats = $stats_query->get_result()->fetch_assoc();

// ===== HANDLE PAYMENT UPDATE =====
$update_message = '';
$update_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    if ($_POST['action'] === 'update_payment') {
        $registration_id = (int)$_POST['registration_id'];
        $new_status = $_POST['payment_status'];
        
        // Verify this registration belongs to this club
        $verify = $conn->prepare("
            SELECT r.registration_id 
            FROM registrations r
            JOIN events e ON r.event_id = e.event_id
            WHERE r.registration_id = ? AND e.club_id = ?
        ");
        $verify->bind_param("ii", $registration_id, $club_id);
        $verify->execute();
        
        if ($verify->get_result()->num_rows > 0) {
            $update = $conn->prepare("UPDATE registrations SET payment_status = ? WHERE registration_id = ?");
            $update->bind_param("si", $new_status, $registration_id);
            
            if ($update->execute()) {
                $update_message = "Payment status updated successfully!";
                // Refresh page to show changes
                echo "<script>window.location.href='event_manager.php?" . http_build_query($_GET) . "';</script>";
                exit;
            } else {
                $update_error = "Failed to update payment status.";
            }
        } else {
            $update_error = "Unauthorized access to this registration.";
        }
    }
    
    if ($_POST['action'] === 'update_attendance') {
        $registration_id = (int)$_POST['registration_id'];
        $new_status = $_POST['attendance_status'];
        
        // Verify this registration belongs to this club
        $verify = $conn->prepare("
            SELECT r.registration_id 
            FROM registrations r
            JOIN events e ON r.event_id = e.event_id
            WHERE r.registration_id = ? AND e.club_id = ?
        ");
        $verify->bind_param("ii", $registration_id, $club_id);
        $verify->execute();
        
        if ($verify->get_result()->num_rows > 0) {
            $update = $conn->prepare("UPDATE registrations SET status = ?, checked_in_at = NOW() WHERE registration_id = ?");
            $update->bind_param("si", $new_status, $registration_id);
            
            if ($update->execute()) {
                $update_message = "Attendance status updated successfully!";
                echo "<script>window.location.href='event_manager.php?" . http_build_query($_GET) . "';</script>";
                exit;
            } else {
                $update_error = "Failed to update attendance status.";
            }
        } else {
            $update_error = "Unauthorized access to this registration.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Manager | <?php echo htmlspecialchars($club_name); ?></title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/event_manager.css">
    <link rel="stylesheet" href="../css/global.css">
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
                <a href="../admin/dashboard.php" class="nav-link">Dashboard</a>
                <a href="event_manager.php" class="nav-link active">Event Manager</a>
                <a href="../public/events.php" class="nav-link">View Events</a>
                <a href="../backend/logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>
                <i class="fas fa-calendar-check"></i>
                Event Manager
                <span class="club-badge">
                    <i class="fas fa-flag"></i> <?php echo htmlspecialchars($club_name); ?>
                </span>
            </h1>
            <p>Manage registrations, approve payments, and track attendance for your club's events</p>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo number_format($stats['total'] ?? 0); ?></h3>
                    <p>Total Registrations</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo number_format($stats['paid'] ?? 0); ?></h3>
                    <p>Payments Received</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo number_format($stats['pending'] ?? 0); ?></h3>
                    <p>Pending Payments</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-ticket"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo number_format($stats['attended'] ?? 0); ?></h3>
                    <p>Attended</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-rupee-sign"></i>
                </div>
                <div class="stat-content">
                    <h3>NPR <?php echo number_format($stats['revenue'] ?? 0); ?></h3>
                    <p>Total Revenue</p>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-title">
                <i class="fas fa-filter"></i>
                <span>Filter Registrations</span>
            </div>
            
            <form method="GET" class="filter-grid">
                <div class="filter-group">
                    <label>Event</label>
                    <select name="event_id" class="filter-select">
                        <option value="all">All Events</option>
                        <?php while($ev = $club_events->fetch_assoc()): ?>
                            <option value="<?php echo $ev['event_id']; ?>" 
                                <?php echo $event_filter == $ev['event_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($ev['title']); ?> 
                                (<?php echo date('M d', strtotime($ev['proposed_date'])); ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Payment Status</label>
                    <select name="payment" class="filter-select">
                        <option value="all">All Payments</option>
                        <option value="paid" <?php echo $payment_filter == 'paid' ? 'selected' : ''; ?>>Paid</option>
                        <option value="pending" <?php echo $payment_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Registration Status</label>
                    <select name="status" class="filter-select">
                        <option value="all">All Active</option>
                        <option value="registered" <?php echo $status_filter == 'registered' ? 'selected' : ''; ?>>Registered</option>
                        <option value="attended" <?php echo $status_filter == 'attended' ? 'selected' : ''; ?>>Attended</option>
                        <option value="cancelled" <?php echo $status_filter == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>From Date</label>
                    <input type="date" name="date_from" class="filter-input" value="<?php echo $date_from; ?>">
                </div>
                
                <div class="filter-group">
                    <label>To Date</label>
                    <input type="date" name="date_to" class="filter-input" value="<?php echo $date_to; ?>">
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="btn-filter">
                        <i class="fas fa-search"></i> Apply Filters
                    </button>
                    <a href="event_manager.php" class="btn-reset">
                        <i class="fas fa-redo-alt"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Messages -->
        <?php if ($update_message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?php echo $update_message; ?></span>
            </div>
        <?php endif; ?>

        <?php if ($update_error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo $update_error; ?></span>
            </div>
        <?php endif; ?>

        <!-- Export Button -->
        <button class="export-btn" onclick="exportToCSV()">
            <i class="fas fa-file-csv"></i> Export to CSV
        </button>

        <!-- Registrations Table -->
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Event</th>
                        <th>Date</th>
                        <th>User</th>
                        <th>Team</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Registered On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($registrations->num_rows === 0): ?>
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 40px;">
                                <i class="fas fa-inbox" style="font-size: 2rem; color: #ccc; margin-bottom: 10px;"></i>
                                <br>No registrations found
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php while($reg = $registrations->fetch_assoc()): 
                            $team_members = json_decode($reg['team_members'], true);
                            $is_team = !empty($team_members);
                        ?>
                            <tr>
                                <td><strong>#<?php echo $reg['registration_id']; ?></strong></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($reg['event_title']); ?></strong>
                                    <br>
                                    <small style="color: var(--text-muted);"><?php echo htmlspecialchars($reg['event_venue']); ?></small>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($reg['event_date'])); ?></td>
                                <td>
                                    <a href="profile.php?uid=<?php echo $reg['user_id']; ?>" style="color: var(--sxc-maroon); text-decoration: none;">
                                        <?php echo htmlspecialchars($reg['user_email']); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if ($is_team): ?>
                                        <div class="team-popup">
                                            <span class="team-badge">
                                                <i class="fas fa-users"></i> Team (<?php echo count($team_members) + 1; ?>)
                                            </span>
                                            <div class="team-popup-content">
                                                <div style="font-weight: 700; margin-bottom: 10px; color: var(--sxc-maroon);">
                                                    <i class="fas fa-users"></i> Team Members
                                                </div>
                                                <div class="team-member-item">
                                                    <i class="fas fa-crown team-leader-icon"></i>
                                                    <span class="team-member-email"><?php echo htmlspecialchars($reg['user_email']); ?> (Leader)</span>
                                                </div>
                                                <?php foreach($team_members as $member): ?>
                                                    <div class="team-member-item">
                                                        <i class="fas fa-user" style="color: var(--text-muted);"></i>
                                                        <span class="team-member-email"><?php echo htmlspecialchars($member['email']); ?></span>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span class="team-badge" style="background: var(--sxc-maroon-light); color: var(--sxc-maroon);">
                                            <i class="fas fa-user"></i> Individual
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($reg['payment_amount'] > 0): ?>
                                        NPR <?php echo number_format($reg['payment_amount']); ?>
                                    <?php else: ?>
                                        <span style="color: var(--sxc-gold-dark);">Free</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="payment-badge payment-<?php echo $reg['payment_status']; ?>">
                                        <i class="fas fa-<?php echo $reg['payment_status'] === 'paid' ? 'check-circle' : 'clock'; ?>"></i>
                                        <?php echo ucfirst($reg['payment_status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $reg['status']; ?>">
                                        <?php if ($reg['status'] === 'registered'): ?>
                                            <i class="fas fa-clock"></i> Registered
                                        <?php elseif ($reg['status'] === 'attended'): ?>
                                            <i class="fas fa-check-circle"></i> Attended
                                        <?php elseif ($reg['status'] === 'cancelled'): ?>
                                            <i class="fas fa-times-circle"></i> Cancelled
                                        <?php else: ?>
                                            <?php echo ucfirst($reg['status']); ?>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo date('M d, Y', strtotime($reg['registration_date'])); ?>
                                    <br>
                                    <small style="color: var(--text-muted);"><?php echo date('g:i A', strtotime($reg['registration_date'])); ?></small>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <!-- Payment Actions -->
                                        <?php if ($reg['payment_status'] === 'pending'): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="update_payment">
                                                <input type="hidden" name="registration_id" value="<?php echo $reg['registration_id']; ?>">
                                                <input type="hidden" name="payment_status" value="paid">
                                                <button type="submit" class="action-btn approve" onclick="return confirm('Mark payment as received?')">
                                                    <i class="fas fa-check"></i> Approve Payment
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <!-- Attendance Actions -->
                                        <?php if ($reg['status'] === 'registered'): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="update_attendance">
                                                <input type="hidden" name="registration_id" value="<?php echo $reg['registration_id']; ?>">
                                                <input type="hidden" name="attendance_status" value="attended">
                                                <button type="submit" class="action-btn attended" onclick="return confirm('Mark as attended?')">
                                                    <i class="fas fa-check-circle"></i> Check In
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <!-- View User Profile -->
                                        <a href="../public/profile.php?uid=<?php echo $reg['user_id']; ?>" class="action-btn view">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
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
        // Export to CSV
        function exportToCSV() {
            const rows = [];
            const table = document.querySelector('table');
            const headers = [];
            
            // Get headers
            table.querySelectorAll('thead th').forEach(th => {
                headers.push(th.innerText.trim());
            });
            rows.push(headers.join(','));
            
            // Get data rows
            table.querySelectorAll('tbody tr').forEach(tr => {
                const row = [];
                tr.querySelectorAll('td').forEach((td, index) => {
                    // Skip actions column (last column)
                    if (index < 9) {
                        let text = td.innerText.trim().replace(/,/g, ';');
                        row.push(text);
                    }
                });
                rows.push(row.join(','));
            });
            
            // Create CSV file
            const csvContent = rows.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'registrations_<?php echo date('Y-m-d'); ?>.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        }
    </script>
</body>
</html>