<?php
require_once '../backend/data.php';
session_start();

// ===== SECURITY =====
if($_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = 'Access Denied: Admins only.';
    header('Location: ../index.php');
    exit;
}

$club_id = $_SESSION['club_id'] ?? null;
$event_id = $_GET['event_id'] ?? null;

if(!$event_id || !$club_id) {
    $_SESSION['error'] = 'Invalid Event ID.';
    header('Location: ../admin/dashboard.php');
    exit;
}

// First, get event details for success message
$stmt = $conn->prepare("SELECT title FROM events WHERE event_id = ? AND club_id = ?");
$stmt->bind_param("ii", $event_id, $club_id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();
$event_title = $event ? $event['title'] : "Event #$event_id";

// Delete with club_id validation for security
$delete_query = $conn->prepare("DELETE FROM events WHERE event_id = ? AND club_id = ?");
$delete_query->bind_param("ii", $event_id, $club_id);

if($delete_query->execute()) {
    if($delete_query->affected_rows > 0) {
        $_SESSION['success'] = "Event '$event_title' deleted successfully.";
    } else {
        $_SESSION['error'] = "Event not found or already deleted.";
    }
} else {
    $_SESSION['error'] = "Error deleting event: " . $conn->error;
}

header('Location: ../admin/dashboard.php');
exit;
?>