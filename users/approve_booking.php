<?php
session_start();
include('../includes/config.php');

// Check if admin is logged in
if(!isset($_SESSION['alogin'])){
    header("Location: index.php");
    exit();
}

if(isset($_GET['id']) && isset($_GET['action'])){
    $id = intval($_GET['id']);
    // Approve = 1, Cancel = 2
    $status = ($_GET['action'] == 'approve') ? 1 : 2;

    $sql = "UPDATE tblbooking SET Status=:status WHERE id=:id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':status', $status, PDO::PARAM_INT);
    $query->bindParam(':id', $id, PDO::PARAM_INT);

    if($query->execute()){
        // ✅ Generate and send updated receipt AFTER status change
        // Use require instead of include, so it runs fully
        require('receipt_pdf.php?id='.$id);

        // Redirect back to manage bookings
        header("Location: manage-bookings.php");
        exit();
    } else {
        echo "Failed to update booking";
    }
}
?>
