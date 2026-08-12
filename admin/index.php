<?php
session_start();
include('../includes/config.php');

if(empty($_SESSION['alogin'])){
    header("Location: login.php");
    exit();
}


/* ================= DASHBOARD DATA ================= */

// Total bookings
$totalBookings = $dbh->query("
SELECT COUNT(*) FROM tblbooking
")->fetchColumn();


// Total vehicles

$totalVehicles = $dbh->query("
SELECT COUNT(*)
FROM tblvehicles
")->fetchColumn();


// Vehicles currently rented

$rentedCars = $dbh->query("
SELECT COUNT(DISTINCT VehicleId)
FROM tblbooking
WHERE Status = 1
AND CURDATE() BETWEEN FromDate AND ToDate
")->fetchColumn();

// Available vehicles

$totalVehicles = $dbh->query("
SELECT COUNT(*)
FROM tblvehicles
")->fetchColumn();


$availableCars = $totalVehicles - $rentedCars;

// Revenue
$revenueQuery = $dbh->query("
SELECT 
SUM(
GREATEST(DATEDIFF(ToDate, FromDate),1) 
* tblvehicles.PricePerDay
) AS total

FROM tblbooking

LEFT JOIN tblvehicles

ON tblvehicles.id = tblbooking.VehicleId

WHERE tblbooking.Status = 1
");


$revenue = $revenueQuery->fetch(PDO::FETCH_ASSOC)['total'];

if($revenue == ""){
    $revenue = 0;
}



// Recent bookings

$recentBookings = $dbh->query("
SELECT 

tblbooking.*,
tblvehicles.VehiclesTitle,
tblvehicles.PricePerDay

FROM tblbooking

LEFT JOIN tblvehicles

ON tblvehicles.id = tblbooking.VehicleId

ORDER BY tblbooking.id DESC

LIMIT 5
");


// Recent activity

$logs = $dbh->query("
SELECT * 
FROM admin_logs
ORDER BY id DESC
LIMIT 5
");


/* ================= EXTRA DASHBOARD DATA ================= */


// PENDING BOOKING NOTIFICATIONS

$notificationCount = $dbh->query("
SELECT COUNT(*)
FROM tblbooking
WHERE Status = 0
")->fetchColumn();



// MONTHLY EARNINGS

$monthlyData = array_fill(1,12,0);


$monthlyQuery = $dbh->query("

SELECT

MONTH(b.PostingDate) AS month,

SUM(
GREATEST(DATEDIFF(b.ToDate,b.FromDate),1)
*
v.PricePerDay
)

AS total


FROM tblbooking b


LEFT JOIN tblvehicles v

ON v.id = b.VehicleId


WHERE b.Status = 1


GROUP BY MONTH(b.PostingDate)

");



while($row=$monthlyQuery->fetch(PDO::FETCH_ASSOC)){


$monthlyData[$row['month']] = $row['total'];


}





// VEHICLE DISPLAY


$vehicles = $dbh->query("

SELECT *

FROM tblvehicles

ORDER BY id DESC

LIMIT 5

");


?>

<!DOCTYPE html>
<html>

<head>

<title>
Emerald Car Rental - Dashboard
</title>


<link rel="stylesheet" href="css/dashboard.css">


</head>


<body>


<!-- SIDEBAR -->

<div class="sidebar">


<h2>
Emerald Cars
</h2>


<p class="menu-title">
MAIN MENU
</p>


<a href="index.php" class="active">
Dashboard
</a>


<a href="manage-booking.php">
Bookings
</a>


<a href="manage-vehicles.php">
Vehicles
</a>


<a href="clients.php">
Clients
</a>




<a href="payments.php">
Payments
</a>


<a href="reports.php">
Reports
</a>


<a href="settings.php">
Settings
</a>


<a href="admin-login.php" class="logout">
Logout
</a>


</div>





<!-- MAIN -->

<div class="main">


<!-- TOP BAR -->


<div class="topbar">


<div>

<h2>
Dashboard
</h2>

<p>
Welcome back, <?php echo $_SESSION['alogin']; ?>
</p>

</div>


<div>

<div class="notification">

🔔

<span>

<?php echo $notificationCount; ?>

</span>

</div>


<button onclick="darkMode()">
🌙
</button>


</div>


</div>

<div class="profile-card">


<div class="profile-image">

👤

</div>


<div>


<h3>

<?php echo $_SESSION['alogin']; ?>

</h3>


<p>
Administrator
</p>


<p>
Emerald Car Rental
</p>


</div>


</div>

<!-- CARDS -->


<div class="cards">


<div class="card">

<h3>
💰 Revenue
</h3>

<h1>
$<?php echo $revenue; ?>
</h1>


</div>



<div class="card">

<h3>
📘 Bookings
</h3>

<h1>
<?php echo $totalBookings; ?>
</h1>


</div>




<div class="card">

<h3>
🚘 Rented Cars
</h3>

<h1>
<?php echo $rentedCars; ?>
</h1>


</div>




<div class="card">

<h3>
🚗 Available Cars
</h3>

<h1>
<?php echo $availableCars; ?>
</h1>


</div>


</div>

<!-- RECENT BOOKINGS -->

<div class="section">


<h2>
Recent Bookings
</h2>


<table>


<thead>

<tr>

<th>Booking ID</th>
<th>Client</th>
<th>Vehicle</th>
<th>Days</th>
<th>Payment</th>
<th>Status</th>


</tr>

</thead>


<tbody>


<?php

while($row = $recentBookings->fetch(PDO::FETCH_ASSOC)){


$start = new DateTime($row['FromDate']);
$end = new DateTime($row['ToDate']);

$days = $start->diff($end)->days;


if($days == 0){
    $days = 1;
}


$payment = $days * $row['PricePerDay'];


?>


<tr>


<td>
<?php echo $row['BookingNumber']; ?>
</td>


<td>
<?php echo $row['email']; ?>
</td>


<td>
<?php echo $row['VehiclesTitle']; ?>
</td>


<td>
<?php echo $days; ?> days
</td>


<td>
$<?php echo $payment; ?>
</td>


<td>


<?php

if($row['Status']==1){

echo "<span class='approved'>Approved</span>";

}

else{

echo "<span class='pending'>Pending</span>";

}

?>


</td>


</tr>



<?php } ?>


</tbody>


</table>



</div>





<!-- ACTIVITY -->


<div class="section">


<h2>
Recent Activity
</h2>


<ul class="activity">


<?php

while($log = $logs->fetch(PDO::FETCH_ASSOC)){


?>


<li>

<?php echo $log['action']; ?>

<br>

<small>
<?php echo $log['created_at']; ?>
</small>


</li>


<?php } ?>


</ul>


</div>






<!-- CAR PREFERENCE -->


<div class="section">


<h2>
Car Type Preference
</h2>


<div class="cars">


<div class="car-box">

<h3>Sedan</h3>

<div class="bar">
<span style="width:70%">
</span>
</div>

<p>
70%
</p>


</div>





<div class="car-box">

<h3>SUV</h3>

<div class="bar">

<span style="width:55%">
</span>

</div>


<p>
55%
</p>


</div>





<div class="car-box">

<h3>Hatchback</h3>

<div class="bar">

<span style="width:40%">
</span>

</div>


<p>
40%
</p>


</div>





<div class="car-box">

<h3>Truck</h3>


<div class="bar">

<span style="width:25%">
</span>

</div>


<p>
25%
</p>


</div>


</div>


</div>


<div class="section">


<h2>
Latest Vehicles
</h2>



<div class="vehicle-grid">


<?php

while($vehicle=$vehicles->fetch(PDO::FETCH_ASSOC)){


?>


<div class="vehicle-card">


<?php

$image="";

if(!empty($vehicle['Vimage1'])){

$image="../cars/".$vehicle['Vimage1'];

}

else{

$image="../cars/Ford Mustang.jpg";

}

?>


<img src="<?php echo $image; ?>">



<h3>

<?php echo $vehicle['VehiclesTitle']; ?>

</h3>



<p>

Price:

$<?php echo $vehicle['PricePerDay']; ?>

/day

</p>


</div>



<?php } ?>


</div>


</div>


<!-- OFFLINE CHART -->

<div class="section">


<h2>
Monthly Earnings
</h2>


<div class="chart">


<?php


$months=[

"Jan",
"Feb",
"Mar",
"Apr",
"May",
"Jun",
"Jul",
"Aug",
"Sep",
"Oct",
"Nov",
"Dec"

];


foreach($monthlyData as $index=>$money){


$height=0;


if($revenue>0){

$height=($money/$revenue)*100;

}


?>


<div>


<p>

<?php echo $months[$index-1]; ?>

</p>



<span style="height:<?php echo $height; ?>%">

</span>


</div>



<?php } ?>


</div>


</div>


</div>


</div>




</div>



<script src="js/dashboard.js"></script>


</body>

</html>