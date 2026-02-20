<?php
// ===== CLUB ADMIN AUTHENTICATION =====


function requireClubAdmin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../backend/login.php');
        exit;
    }
    
    if ($_SESSION['role'] !== 'admin' && (!isset($_SESSION['is_club_admin']) || $_SESSION['is_club_admin'] !== 1)) {
        die("<script>alert('Access Denied: Club Admin privileges required.'); window.location.href='../index.php';</script>");
    }
    
    if (!isset($_SESSION['club_id']) || empty($_SESSION['club_id'])) {
        die("<script>alert('No club associated with your account.'); window.location.href='../index.php';</script>");
    }
}

function getClubId() {
    return $_SESSION['club_id'] ?? 0;
}

function isMasterAdmin() {
    return $_SESSION['role'] === 'admin';
}

function getClubById($club_id) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM clubs WHERE id = ?");
    $stmt->bind_param("i", $club_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

function canEditEvent($event_id) {
    global $conn;
    
    $club_id = getClubId();
    
    if (isMasterAdmin()) {
        return true;
    }
    
    $stmt = $conn->prepare("SELECT club_id FROM events WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();
    
    return $event && $event['club_id'] == $club_id;
}
?>