<?php
include('includes/config.php');

header('Content-Type: application/json');

// COUNT PENDING BOOKINGS
$query = $dbh->query("SELECT COUNT(*) as total FROM tblbooking WHERE Status=0");
$result = $query->fetch(PDO::FETCH_ASSOC);

echo json_encode([
  "pending" => (int)$result['total']
]);