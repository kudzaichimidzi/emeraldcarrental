<?php
include('../includes/config.php');

$total = $dbh->query("SELECT COUNT(*) FROM tblbooking")->fetchColumn();
$approved = $dbh->query("SELECT COUNT(*) FROM tblbooking WHERE Status=1")->fetchColumn();
$pending = $dbh->query("SELECT COUNT(*) FROM tblbooking WHERE Status=0")->fetchColumn();

echo json_encode([
    "total"=>$total,
    "approved"=>$approved,
    "pending"=>$pending
]);
?>