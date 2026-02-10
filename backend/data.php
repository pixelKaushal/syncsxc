<?php
// db connection 
$conn = new mysqli("localhost", "root", "", "syncsxc");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$totalEvents = 0;
$sqlCount = "SELECT COUNT(*) as total FROM events";
$resultCount = $conn->query($sqlCount);
if ($resultCount && $resultCount->num_rows > 0) {
    $row = $resultCount->fetch_assoc();
    $totalEvents = (int)$row['total'];
}

// 2. Club fetch function
function fetchClubs($num = 12){
    global $conn;
   
    $limit = (int)$num;
    $sql = "SELECT * FROM clubs LIMIT $limit";
    return $conn->query($sql);
}

// 3. Events fetch function (Fixed the default parameter error)
function fetchEvents($num = null){
    global $conn, $totalEvents;
    

    $limit = ($num === null) ? $totalEvents : (int)$num;
    

    if($limit <= 0) return false;


    $sql = "SELECT e.*, c.name, c.logo_path 
            FROM events e 
            INNER JOIN clubs c ON e.club_id = c.id 
            LIMIT $limit";
            
    return $conn->query($sql);
}
?>