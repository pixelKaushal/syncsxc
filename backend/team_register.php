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

// ===== CHECK IF EVENT IS TEAM EVENT =====
if ($event['is_team_event'] != 1) {
    die("<script>alert('This is not a team event.'); window.location.href='event-details.php?id=$eventId';</script>");
}

// ===== CHECK IF EVENT IS APPROVED =====
if (strtolower($event['approval_status']) !== 'approved') {
    die("<script>alert('This event is not approved yet.'); window.location.href='event-details.php?id=$eventId';</script>");
}

// ===== CHECK IF USER ALREADY REGISTERED (AS LEADER) =====
$checkStmt = $conn->prepare("SELECT registration_id FROM registrations WHERE user_id = ? AND event_id = ? AND status != 'cancelled'");
$checkStmt->bind_param("ii", $user_id, $eventId);
$checkStmt->execute();
if ($checkStmt->get_result()->num_rows > 0) {
    echo "<script>
        alert('You have already registered for this event as a team leader.');
        window.location.href='../public/registrations.php';
    </script>";
    exit;
}

// ===== CHECK IF USER ALREADY REGISTERED (AS MEMBER) =====
$memberCheck = $conn->prepare("
    SELECT registration_id, team_name FROM registrations 
    WHERE event_id = ? 
    AND team_members IS NOT NULL 
    AND JSON_SEARCH(team_members, 'one', ?) IS NOT NULL
    AND status != 'cancelled'
");
$memberCheck->bind_param("is", $eventId, $user_email);
$memberCheck->execute();
$memberResult = $memberCheck->get_result();

if ($memberResult->num_rows > 0) {
    $memberData = $memberResult->fetch_assoc();
    $teamName = $memberData['team_name'] ? 'in team "' . $memberData['team_name'] . '"' : '';
    echo "<script>
        alert('You are already a team member $teamName in this event. You cannot register again.');
        window.location.href='../public/registrations.php';
    </script>";
    exit;
}

// ===== GET TEAM SIZE LIMITS =====
$min_team = (int)$event['min_team_size'];
$max_team = (int)$event['max_team_size'];

// ===== HANDLE FORM SUBMISSION =====
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $team_name = trim($_POST['team_name']);
    $member_emails = $_POST['member_email'] ?? [];
    
    // ===== VALIDATION =====
    $errors = [];
    
    // Team name validation
    if (empty($team_name)) {
        $errors[] = "Team name is required.";
    }
    
    // Count total members (including leader)
    $total_members = count($member_emails) + 1;
    
    // Check team size
    if ($total_members < $min_team) {
        $errors[] = "Team must have at least $min_team members (including you).";
    }
    if ($total_members > $max_team) {
        $errors[] = "Team cannot have more than $max_team members (including you).";
    }
    
    // Validate emails and check @sxc.edu.np domain
    $all_emails = [$user_email];
    foreach ($member_emails as $email) {
        $email = trim($email);
        if (empty($email)) {
            $errors[] = "All email fields are required.";
            break;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format: $email";
        }
        // ===== @sxc.edu.np RESTRICTION =====
        if (!str_ends_with($email, '@sxc.edu.np')) {
            $errors[] = "Only @sxc.edu.np emails are allowed. '$email' is not valid.";
        }
        $all_emails[] = $email;
    }
    
    // Check for duplicate emails in the team
    if (count($all_emails) !== count(array_unique($all_emails))) {
        $errors[] = "Duplicate emails found in team. Each member must have unique email.";
    }
    
    // Check if any team member is already registered for this event
    if (empty($errors)) {
        $placeholders = implode(',', array_fill(0, count($all_emails), '?'));
        $types = str_repeat('s', count($all_emails));
        
        // First, get user_ids for these emails
        $userQuery = "SELECT id, primary_email FROM users WHERE primary_email IN ($placeholders)";
        $userStmt = $conn->prepare($userQuery);
        $userStmt->bind_param($types, ...$all_emails);
        $userStmt->execute();
        $users = $userStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $found_emails = array_column($users, 'primary_email');
        $not_found = array_diff($all_emails, $found_emails);
        
        if (!empty($not_found)) {
            $errors[] = "These emails are not registered with SyncSXC: " . implode(', ', $not_found);
        } else {
            // Check if any of these users are already registered (as leaders)
            $user_ids = array_column($users, 'id');
            $id_placeholders = implode(',', array_fill(0, count($user_ids), '?'));
            $id_types = str_repeat('i', count($user_ids));
            
            $regCheck = $conn->prepare("
                SELECT u.primary_email 
                FROM registrations r
                JOIN users u ON r.user_id = u.id
                WHERE r.event_id = ? AND r.user_id IN ($id_placeholders) AND r.status != 'cancelled'
            ");
            
            $params = array_merge([$eventId], $user_ids);
            $regCheck->bind_param("i" . $id_types, ...$params);
            $regCheck->execute();
            $registered_leaders = $regCheck->get_result()->fetch_all(MYSQLI_ASSOC);
            
            if (!empty($registered_leaders)) {
                $registered_emails = array_column($registered_leaders, 'primary_email');
                $errors[] = "These users are already registered as leaders for this event: " . implode(', ', $registered_emails);
            } else {
                // Check if any of these emails are already members in another team for this event
                foreach ($all_emails as $check_email) {
                    // Skip checking the current user's email (already checked at the top)
                    if ($check_email === $user_email) continue;
                    
                    $emailCheck = $conn->prepare("
                        SELECT registration_id FROM registrations 
                        WHERE event_id = ? 
                        AND team_members IS NOT NULL 
                        AND JSON_SEARCH(team_members, 'one', ?) IS NOT NULL
                        AND status != 'cancelled'
                    ");
                    $emailCheck->bind_param("is", $eventId, $check_email);
                    $emailCheck->execute();
                    if ($emailCheck->get_result()->num_rows > 0) {
                        $errors[] = "$check_email is already a member of another team for this event.";
                        break;
                    }
                }
            }
        }
    }
    
    // ===== SAVE TO DATABASE =====
    if (empty($errors)) {
        // Prepare team members JSON (excluding leader)
        $team_members = [];
        foreach ($member_emails as $email) {
            $team_members[] = ['email' => trim($email)];
        }
        $team_members_json = json_encode($team_members);
        
        // Determine payment status (free events are paid immediately)
        $payment_status = ($event['price'] == 0) ? 'paid' : 'pending';
        
        // Insert registration
        $insert = $conn->prepare("
            INSERT INTO registrations 
            (user_id, event_id, club_id, team_name, team_members, payment_amount, payment_status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $insert->bind_param("iiissds", 
            $user_id, 
            $eventId, 
            $event['club_id'], 
            $team_name, 
            $team_members_json,
            $event['price'],
            $payment_status
        );
        
        if ($insert->execute()) {
            $success_message = "Team registered successfully!";
            // Redirect to registrations page after 2 seconds
            header("refresh:2;url=../public/registrations.php");
        } else {
            $error_message = "Registration failed: " . $conn->error;
        }
    } else {
        $error_message = implode("<br>", $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Registration | <?php echo htmlspecialchars($event['title']); ?></title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/team_register.css">
    <link rel="stylesheet" href="../css/global.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="event-badge">
                <i class="fas fa-users"></i> Team Registration
            </div>
            <h1><?php echo htmlspecialchars($event['title']); ?></h1>
            <p>Register your team for this event</p>
        </div>

        <!-- Main Card -->
        <div class="register-card">
            <!-- Event Info -->
            <div class="event-info">
                <h2><i class="fas fa-info-circle" style="color: var(--sxc-gold);"></i> Event Details</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <i class="fas fa-calendar"></i>
                        <span><?php echo date('M d, Y', strtotime($event['proposed_date'])); ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?php echo htmlspecialchars($event['venue']); ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-tag"></i>
                        <span><?php echo $event['price'] > 0 ? 'NPR ' . number_format($event['price']) : 'Free'; ?></span>
                    </div>
                </div>
            </div>

            <!-- Domain Notice -->
            <div class="domain-notice">
                <i class="fas fa-shield-alt"></i>
                <div>
                    <strong>Institutional Email Required:</strong> All team members must use their <strong>@sxc.edu.np</strong> email addresses.
                </div>
            </div>

            <!-- Team Size Indicator -->
            <div class="team-size-indicator">
                <i class="fas fa-users" style="margin-right: 10px;"></i>
                <span>Team Size: <?php echo $min_team; ?> - <?php echo $max_team; ?> members</span>
                <p style="margin-top: 10px; font-size: 0.9rem; color: var(--text-muted);">
                    (including you as team leader)
                </p>
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
            <form method="POST" id="teamForm">
                <!-- Team Name -->
                <div class="form-group">
                    <label>
                        <i class="fas fa-flag"></i>
                        Team Name
                    </label>
                    <input type="text" name="team_name" class="form-control" placeholder="Enter your team name" required>
                </div>

                <!-- Team Leader (Current User) -->
                <div class="leader-info">
                    <h3>
                        <i class="fas fa-crown" style="color: var(--sxc-gold);"></i>
                        Team Leader (You)
                    </h3>
                    <div class="leader-email">
                        <i class="fas fa-envelope" style="color: var(--sxc-maroon); margin-right: 10px;"></i>
                        <?php echo htmlspecialchars($_SESSION['user_email']); ?>
                    </div>
                </div>

                <!-- Team Members -->
                <div class="members-section">
                    <h3>
                        <i class="fas fa-user-plus"></i>
                        Team Members
                        <span style="font-size: 0.9rem; color: var(--text-muted); margin-left: auto;" id="memberCount">
                            (0 added)
                        </span>
                    </h3>

                    <div id="members-container">
                        <!-- Members will be added here dynamically -->
                    </div>

                    <button type="button" class="btn btn-add" id="addMemberBtn" onclick="addMember()">
                        <i class="fas fa-plus"></i> Add Team Member
                    </button>

                    <div class="member-counter" id="sizeWarning" style="display: none; color: #dc3545;">
                        <i class="fas fa-exclamation-triangle"></i>
                        Maximum team size reached
                    </div>
                </div>

                <!-- Submit Buttons -->
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-check-circle"></i>
                    Register Team
                </button>
                
                <a href="event-details.php?id=<?php echo $eventId; ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
            </form>
            <?php else: ?>
                <a href="../public/registrations.php" class="btn btn-primary">
                    <i class="fas fa-eye"></i>
                    View My Registrations
                </a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        let memberCount = 0;
        const minTeam = <?php echo $min_team; ?>;
        const maxTeam = <?php echo $max_team; ?>;
        
        function addMember() {
            if (memberCount >= maxTeam - 1) {
                alert('Maximum team size reached! You can only add ' + (maxTeam - 1) + ' members.');
                return;
            }
            
            memberCount++;
            
            const container = document.getElementById('members-container');
            const memberDiv = document.createElement('div');
            memberDiv.className = 'member-entry';
            memberDiv.id = 'member-' + memberCount;
            
            memberDiv.innerHTML = `
                <div class="member-header">
                    <h4><i class="fas fa-user"></i> Team Member ${memberCount}</h4>
                    <button type="button" class="remove-member" onclick="removeMember(${memberCount})">
                        <i class="fas fa-times-circle"></i>
                    </button>
                </div>
                <div class="form-group">
                    <label>Email Address <span style="color: var(--sxc-maroon); font-size: 0.8rem;">(@sxc.edu.np)</span></label>
                    <input type="email" name="member_email[]" class="form-control" placeholder="member${memberCount}@sxc.edu.np" required>
                </div>
            `;
            
            container.appendChild(memberDiv);
            updateUI();
        }
        
        function removeMember(id) {
            const member = document.getElementById('member-' + id);
            if (member) {
                member.remove();
                memberCount--;
                
                // Renumber remaining members
                const members = document.querySelectorAll('.member-entry');
                members.forEach((member, index) => {
                    const newNum = index + 1;
                    member.id = 'member-' + newNum;
                    const header = member.querySelector('h4');
                    if (header) {
                        header.innerHTML = `<i class="fas fa-user"></i> Team Member ${newNum}`;
                    }
                    const removeBtn = member.querySelector('.remove-member');
                    if (removeBtn) {
                        removeBtn.setAttribute('onclick', `removeMember(${newNum})`);
                    }
                });
                
                memberCount = members.length;
                updateUI();
            }
        }
        
        function updateUI() {
            const totalMembers = memberCount + 1;
            const memberCountSpan = document.getElementById('memberCount');
            const sizeWarning = document.getElementById('sizeWarning');
            const addBtn = document.getElementById('addMemberBtn');
            
            memberCountSpan.innerHTML = `(${memberCount} added)`;
            
            if (totalMembers < minTeam) {
                memberCountSpan.style.color = '#dc3545';
            } else {
                memberCountSpan.style.color = 'var(--text-muted)';
            }
            
            if (memberCount >= maxTeam - 1) {
                addBtn.style.display = 'none';
                sizeWarning.style.display = 'block';
                sizeWarning.innerHTML = `<i class="fas fa-exclamation-triangle"></i> Maximum team size reached (${maxTeam} members including you)`;
            } else {
                addBtn.style.display = 'block';
                sizeWarning.style.display = 'none';
            }
        }
        
        // Add initial members based on min team size
        for (let i = 0; i < Math.max(0, minTeam - 1); i++) {
            addMember();
        }
        
        // Form validation
        document.getElementById('teamForm')?.addEventListener('submit', function(e) {
            const totalMembers = memberCount + 1;
            
            if (totalMembers < minTeam) {
                e.preventDefault();
                alert('Your team must have at least ' + minTeam + ' members (including you). Please add more members.');
                return;
            }
            
            if (totalMembers > maxTeam) {
                e.preventDefault();
                alert('Your team cannot have more than ' + maxTeam + ' members (including you). Please remove some members.');
                return;
            }
            
            // Validate @sxc.edu.np domain for all member emails
            const emailInputs = document.querySelectorAll('input[name="member_email[]"]');
            for (let input of emailInputs) {
                if (!input.value.endsWith('@sxc.edu.np')) {
                    e.preventDefault();
                    alert('All team members must have @sxc.edu.np email addresses.\nInvalid: ' + input.value);
                    input.focus();
                    return;
                }
            }
        });
    </script>
</body>
</html>