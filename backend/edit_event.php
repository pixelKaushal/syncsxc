<?php
require_once '../backend/data.php';
session_start();

// 1. Security Check
if($_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$club_id = $_SESSION['club_id'] ?? null;
$event_id = $_GET['event_id'] ?? null;

if (!$event_id || !$club_id) {
    die("<script>alert('Invalid Request.'); window.location.href='dashboard.php';</script>");
}

// 2. Fetch Current Event Data
$stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ? AND club_id = ?");
$stmt->bind_param("ii", $event_id, $club_id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
    die("<script>alert('Event not found or unauthorized access.'); window.location.href='../admin/dashboard.php';</script>");
}

// 3. Handle the Update (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $date = $_POST['proposed_date'];
    $desc = trim($_POST['description']);
    $venue = trim($_POST['venue']);
    $status = $_POST['approval_status'];
    $is_team = $_POST['is_team_event'];
    $min_size = (int)$_POST['min_team_size'];
    $max_size = (int)$_POST['max_team_size'];
    $price = (float)$_POST['price'];
    
    if ($min_size > $max_size) {
        die("<script>alert('Error: Minimum team size cannot be greater than maximum.'); window.history.back();</script>");
    }
    
    $update = $conn->prepare("UPDATE events SET title=?, proposed_date=?, description=?, venue=?, approval_status=?, is_team_event=?, min_team_size=?, max_team_size=?, price=? WHERE event_id=? AND club_id=?");
    $update->bind_param("sssssiiiidi", $title, $date, $desc, $venue, $status, $is_team, $min_size, $max_size, $price, $event_id, $club_id);

    if ($update->execute()) {
        echo "<script>alert(' Event updated successfully!'); window.location.href='../admin/dashboard.php';</script>";
    } else {
        echo "<script>alert('Error updating record: " . addslashes($conn->error) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event | SyncSXC · <?php echo htmlspecialchars($event['title']); ?></title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="../css/edit_event.css">
</head>
<body>
    <div class="edit-wrapper">
        <div class="edit-container">
            
            <!-- Header with Icon -->
            <div class="edit-header">
                <div class="header-icon">
                    <i class="fas fa-edit"></i>
                </div>
                <div class="header-title">
                    <h1>Edit Event</h1>
                    <p>
                        <i class="fas fa-calendar-alt"></i>
                        <?php echo date('F d, Y', strtotime($event['proposed_date'])); ?>
                    </p>
                </div>
            </div>

            <!-- Event Meta Information -->
            <div class="event-meta">
                <div class="event-id">
                    <i class="fas fa-hashtag"></i>
                    Event ID: <strong>#<?php echo htmlspecialchars($event['event_id']); ?></strong>
                </div>
                <div class="event-club">
                    <i class="fas fa-flag"></i>
                    Club ID: <?php echo htmlspecialchars($club_id); ?>
                </div>
            </div>

            <!-- Current Status Display -->
            <div class="current-status">
                <span style="color: var(--text-muted);">Current Status:</span>
                <?php
                $status = $event['approval_status'] ?? 'Pending';
                $status_class = strtolower($status);
                ?>
                <span class="status-badge <?php echo $status_class; ?>">
                    <i class="fas fa-circle" style="font-size: 8px;"></i>
                    <?php echo htmlspecialchars($status); ?>
                </span>
            </div>

            <!-- Edit Form -->
            <form method="POST" class="event-form">
                <!-- Event Title -->
                <div class="form-group">
                    <label>
                        <i class="fas fa-heading"></i>
                        Event Title
                        <span>*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           class="form-control" 
                           value="<?php echo htmlspecialchars($event['title']); ?>" 
                           placeholder="Enter event title"
                           required>
                </div>

                <!-- Date and Venue Row -->
                <div class="form-row">
                    <div class="form-group">
                        <label>
                            <i class="fas fa-calendar"></i>
                            Proposed Date
                            <span>*</span>
                        </label>
                        <input type="date" 
                               name="proposed_date" 
                               class="form-control" 
                               value="<?php echo $event['proposed_date']; ?>" 
                               min="<?php echo date('Y-m-d'); ?>"
                               required>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-map-marker-alt"></i>
                            Venue
                            <span>*</span>
                        </label>
                        <input type="text" 
                               name="venue" 
                               class="form-control" 
                               value="<?php echo htmlspecialchars($event['venue']); ?>" 
                               placeholder="e.g. Main Auditorium"
                               required>
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label>
                        <i class="fas fa-align-left"></i>
                        Description
                        <span>*</span>
                    </label>
                    <textarea name="description" 
                              class="form-control" 
                              placeholder="Describe your event in detail..."
                              required><?php echo htmlspecialchars($event['description']); ?></textarea>
                </div>

                <!-- Status and Team Type Row -->
                <div class="form-row">
                    <div class="form-group">
                        <label>
                            <i class="fas fa-check-circle"></i>
                            Approval Status
                        </label>
                        <select name="approval_status" class="form-control">
                            <?php 
                            $statuses = ['Pending', 'Approved', 'Rejected', 'TBD', 'Draft'];
                            foreach($statuses as $s) {
                                $selected = ($event['approval_status'] == $s) ? 'selected' : '';
                                echo "<option value='$s' $selected>$s</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-users"></i>
                            Team Event?
                        </label>
                        <select name="is_team_event" class="form-control" id="teamEventSelect">
                            <option value="1" <?php echo $event['is_team_event'] == 1 ? 'selected' : ''; ?>>👥 Yes - Team Event</option>
                            <option value="0" <?php echo $event['is_team_event'] == 0 ? 'selected' : ''; ?>>👤 No - Individual Event</option>
                        </select>
                    </div>
                </div>

                <!-- Team Size Row -->
                <div class="form-row">
                    <div class="form-group">
                        <label>
                            <i class="fas fa-user-minus"></i>
                            Minimum Team Size
                        </label>
                        <input type="number" 
                            name="min_team_size" 
                            id="minTeamSize"
                            class="form-control" 
                            value="<?php echo $event['min_team_size']; ?>" 
                            min="1"
                            <?php echo $event['is_team_event'] == 0 ? 'readonly' : ''; ?>>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fas fa-user-plus"></i>
                            Maximum Team Size
                        </label>
                        <input type="number" 
                            name="max_team_size" 
                            id="maxTeamSize"
                            class="form-control" 
                            value="<?php echo $event['max_team_size']; ?>" 
                            min="1"
                            <?php echo $event['is_team_event'] == 0 ? 'readonly' : ''; ?>>
                    </div>
                </div>

                <!-- Price -->
                <div class="form-group">
                    <label>
                        <i class="fas fa-tag"></i>
                        Price (NPR)
                    </label>
                    <input type="number" 
                        name="price" 
                        class="form-control" 
                        value="<?php echo $event['price']; ?>" 
                        step="0.01" 
                        min="0"
                        placeholder="0 for free events">
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">
                    <span>Update Event Details</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
                
                <!-- Cancel Link -->
                <a href="../admin/dashboard.php" class="cancel-link">
                    <i class="fas fa-times"></i>
                    Cancel and Return to Dashboard
                </a>
            </form> 
        </div>
    </div>

    <script src="../js/edit_event.js"></script></script>
</body>
</html>