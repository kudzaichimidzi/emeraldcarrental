<?php
include('../includes/config.php');

// Force download as Excel file
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=bookings.xls");

// Column headers
echo "BookingID\tClient Email\tVehicle\tFrom\tTo\tStatus\n";

// Query with proper aliases
$query = $dbh->query("
    SELECT 
        tblbooking.BookingNumber,
        tblbooking.EmailId AS userEmail,
        tblvehicles.VehiclesTitle,
        tblbooking.FromDate,
        tblbooking.ToDate,
        tblbooking.Status
    FROM tblbooking
    LEFT JOIN tblvehicles ON tblvehicles.id = tblbooking.VehicleId
");

while($row = $query->fetch(PDO::FETCH_ASSOC)){
    echo $row['BookingNumber']."\t";
    echo $row['userEmail']."\t";
    echo $row['VehiclesTitle']."\t";
    echo $row['FromDate']."\t";
    echo $row['ToDate']."\t";

    // Handle all status codes
    if($row['Status'] == 1){
        echo "Approved\n";
    } elseif($row['Status'] == 2){
        echo "Cancelled\n";
    } else {
        echo "Pending\n";
    }
}
?>
