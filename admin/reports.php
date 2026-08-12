```php
<?php

session_start();

include('../includes/config.php');


if(empty($_SESSION['alogin'])){

    header("Location: login.php");

    exit();

}


/* =========================
   BASIC STATISTICS
========================= */


$totalBookings = $dbh->query("

SELECT COUNT(*)

FROM tblbooking

")->fetchColumn();


$approvedBookings = $dbh->query("

SELECT COUNT(*)

FROM tblbooking

WHERE Status = 1

")->fetchColumn();


$pendingBookings = $dbh->query("

SELECT COUNT(*)

FROM tblbooking

WHERE Status = 0

")->fetchColumn();


$cancelledBookings = $dbh->query("

SELECT COUNT(*)

FROM tblbooking

WHERE Status = 2

")->fetchColumn();


$totalRevenue = $dbh->query("

SELECT SUM(TotalAmount)

FROM tblbooking

WHERE Status = 1

")->fetchColumn();


if($totalRevenue == ""){

    $totalRevenue = 0;

}


/* =========================
   TOTAL CLIENTS
========================= */


$totalClients = $dbh->query("

SELECT COUNT(DISTINCT email)

FROM tblbooking

")->fetchColumn();


/* =========================
   TOTAL VEHICLES
========================= */


$totalVehicles = $dbh->query("

SELECT COUNT(*)

FROM tblvehicles

")->fetchColumn();


/* =========================
   MONTHLY REVENUE
========================= */


$monthlyData = array_fill(1,12,0);


$monthlyQuery = $dbh->query("

SELECT

MONTH(PostingDate) AS month,

SUM(TotalAmount) AS total

FROM tblbooking

WHERE Status = 1

GROUP BY MONTH(PostingDate)

");


while($row = $monthlyQuery->fetch(PDO::FETCH_ASSOC)){

    $monthlyData[$row['month']] = $row['total'];

}


/* =========================
   POPULAR VEHICLES
========================= */


$popularVehicles = $dbh->query("

SELECT

v.VehiclesTitle,

br.BrandName,

COUNT(b.id) AS bookings

FROM tblbooking b

LEFT JOIN tblvehicles v

ON v.id = b.VehicleId

LEFT JOIN tblbrands br

ON br.id = v.VehiclesBrand

GROUP BY b.VehicleId

ORDER BY bookings DESC

LIMIT 5

");


/* =========================
   TOP CLIENTS
========================= */


$topClients = $dbh->query("

SELECT

email,

MAX(name) AS name,

COUNT(*) AS bookings,

SUM(TotalAmount) AS spending

FROM tblbooking

WHERE Status = 1

GROUP BY email

ORDER BY spending DESC

LIMIT 5

");


/* =========================
   MONTH NAMES
========================= */


$months = [

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


?>


<!DOCTYPE html>

<html lang="en">


<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>

Emerald Car Rental - Reports

</title>


<style>


*{

    box-sizing: border-box;

}


body{

    margin: 0;

    background: #0b1220;

    color: white;

    font-family: Arial, sans-serif;

}


a{

    text-decoration: none;

}


/* =========================
   SIDEBAR
========================= */


.sidebar{

    position: fixed;

    left: 0;

    top: 0;

    width: 230px;

    height: 100vh;

    background: #111827;

    border-right: 1px solid #1f2937;

    padding: 25px 15px;

}


.sidebar h2{

    text-align: center;

    margin-bottom: 35px;

}


.menu-title{

    color: #6b7280;

    font-size: 12px;

    padding-left: 15px;

    margin-bottom: 10px;

}


.sidebar a{

    display: block;

    padding: 13px 15px;

    margin-bottom: 5px;

    color: #9ca3af;

    border-radius: 8px;

}


.sidebar a:hover{

    background: #1f2937;

    color: white;

}


.sidebar a.active{

    background: #2563eb;

    color: white;

}


.sidebar .logout{

    margin-top: 30px;

    color: #ef4444;

}


/* =========================
   MAIN
========================= */


.main{

    margin-left: 230px;

    padding: 30px;

}


/* =========================
   HEADER
========================= */


.top{

    margin-bottom: 30px;

}


.top h1{

    margin: 0;

}


.top p{

    color: #9ca3af;

    margin-top: 8px;

}


/* =========================
   STAT CARDS
========================= */


.cards{

    display: grid;

    grid-template-columns:

    repeat(4,1fr);

    gap: 20px;

    margin-bottom: 25px;

}


.card{

    background: #111827;

    border: 1px solid #1f2937;

    border-radius: 15px;

    padding: 22px;

}


.card p{

    margin: 0 0 10px;

    color: #9ca3af;

}


.card h2{

    margin: 0;

    font-size: 27px;

}


.green{

    color: #22c55e;

}


.blue{

    color: #60a5fa;

}


.yellow{

    color: #fbbf24;

}


.red{

    color: #f87171;

}


/* =========================
   SECTIONS
========================= */


.section{

    background: #111827;

    border: 1px solid #1f2937;

    border-radius: 15px;

    padding: 25px;

    margin-bottom: 25px;

}


.section h2{

    margin-top: 0;

    margin-bottom: 20px;

}


/* =========================
   CHART
========================= */


.chart{

    height: 300px;

    display: flex;

    align-items: flex-end;

    justify-content: space-between;

    gap: 12px;

    padding-top: 30px;

}


.bar-column{

    height: 100%;

    flex: 1;

    display: flex;

    flex-direction: column;

    justify-content: flex-end;

    align-items: center;

}


.bar{

    width: 100%;

    max-width: 55px;

    background: #2563eb;

    border-radius: 7px 7px 0 0;

    min-height: 3px;

}


.bar:hover{

    background: #3b82f6;

}


.bar-label{

    margin-top: 8px;

    color: #9ca3af;

    font-size: 12px;

}


/* =========================
   TWO COLUMNS
========================= */


.two-columns{

    display: grid;

    grid-template-columns:

    repeat(2,1fr);

    gap: 25px;

}


/* =========================
   TABLE
========================= */


.table-container{

    overflow-x: auto;

}


table{

    width: 100%;

    border-collapse: collapse;

}


th{

    color: #9ca3af;

    text-align: left;

    padding: 13px;

    font-size: 13px;

    border-bottom: 1px solid #374151;

}


td{

    padding: 14px 13px;

    border-bottom: 1px solid #1f2937;

}


tr:hover{

    background: #172033;

}


/* =========================
   PROGRESS
========================= */


.progress{

    height: 7px;

    background: #1f2937;

    border-radius: 10px;

    margin-top: 8px;

}


.progress span{

    display: block;

    height: 100%;

    background: #2563eb;

    border-radius: 10px;

}


/* =========================
   RESPONSIVE
========================= */


@media(max-width:1000px){

    .cards{

        grid-template-columns:

        repeat(2,1fr);

    }


    .two-columns{

        grid-template-columns: 1fr;

    }

}


@media(max-width:700px){

    .sidebar{

        position: static;

        width: 100%;

        height: auto;

    }


    .main{

        margin-left: 0;

        padding: 15px;

    }


    .cards{

        grid-template-columns: 1fr;

    }

}


</style>

</head>


<body>


<!-- =========================
     SIDEBAR
========================= -->


<div class="sidebar">


<h2>

Emerald Cars

</h2>


<p class="menu-title">

MAIN MENU

</p>


<a href="index.php">

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


<a href="reports.php" class="active">

Reports

</a>


<a href="#">

Settings

</a>


<a href="logout.php" class="logout">

Logout

</a>


</div>



<!-- =========================
     MAIN
========================= -->


<div class="main">


<div class="top">


<h1>

Reports

</h1>


<p>

Business performance and rental statistics

</p>


</div>



<!-- =========================
     STATISTICS
========================= -->


<div class="cards">


<div class="card">


<p>

💰 Total Revenue

</p>


<h2 class="green">

$<?php echo number_format($totalRevenue,2); ?>

</h2>


</div>



<div class="card">


<p>

📅 Total Bookings

</p>


<h2 class="blue">

<?php echo $totalBookings; ?>

</h2>


</div>



<div class="card">


<p>

👥 Total Clients

</p>


<h2 class="yellow">

<?php echo $totalClients; ?>

</h2>


</div>



<div class="card">


<p>

🚗 Total Vehicles

</p>


<h2 class="green">

<?php echo $totalVehicles; ?>

</h2>


</div>


</div>



<!-- =========================
     BOOKING STATUS
========================= -->


<div class="section">


<h2>

Booking Overview

</h2>


<div class="cards">


<div class="card">


<p>

Approved

</p>


<h2 class="green">

<?php echo $approvedBookings; ?>

</h2>


</div>



<div class="card">


<p>

Pending

</p>


<h2 class="yellow">

<?php echo $pendingBookings; ?>

</h2>


</div>



<div class="card">


<p>

Cancelled

</p>


<h2 class="red">

<?php echo $cancelledBookings; ?>

</h2>


</div>


</div>


</div>



<!-- =========================
     MONTHLY REVENUE
========================= -->


<div class="section">


<h2>

Monthly Revenue

</h2>


<div class="chart">


<?php


$maxRevenue = max($monthlyData);


foreach($monthlyData as $index => $money){


$height = 3;


if($maxRevenue > 0){

    $height =

    ($money / $maxRevenue) * 100;

}


?>


<div class="bar-column">


<div

class="bar"

style="height:<?php echo $height; ?>%;"

title="$<?php echo number_format($money,2); ?>"

>

</div>


<div class="bar-label">

<?php echo $months[$index - 1]; ?>

</div>


</div>


<?php

}

?>


</div>


</div>



<!-- =========================
     TWO COLUMNS
========================= -->


<div class="two-columns">



<!-- =========================
     POPULAR VEHICLES
========================= -->


<div class="section">


<h2>

🚗 Popular Vehicles

</h2>


<div class="table-container">


<table>


<thead>


<tr>

<th>Vehicle</th>

<th>Bookings</th>

</tr>


</thead>


<tbody>


<?php


while($vehicle = $popularVehicles->fetch(PDO::FETCH_OBJ)){


?>


<tr>


<td>


<strong>

<?php

echo htmlentities(

    $vehicle->BrandName

    . " "

    . $vehicle->VehiclesTitle

);

?>

</strong>


</td>


<td>

<?php echo $vehicle->bookings; ?>

</td>


</tr>


<?php

}

?>


</tbody>


</table>


</div>


</div>



<!-- =========================
     TOP CLIENTS
========================= -->


<div class="section">


<h2>

👑 Top Clients

</h2>


<div class="table-container">


<table>


<thead>


<tr>

<th>Client</th>

<th>Bookings</th>

<th>Spent</th>

</tr>


</thead>


<tbody>


<?php


while($client = $topClients->fetch(PDO::FETCH_OBJ)){


?>


<tr>


<td>


<strong>

<?php echo htmlentities($client->name); ?>

</strong>


<br>


<small style="color:#6b7280;">

<?php echo htmlentities($client->email); ?>

</small>


</td>


<td>

<?php echo $client->bookings; ?>

</td>


<td>

<strong style="color:#22c55e;">

$<?php echo number_format(
    $client->spending,
    2
); ?>

</strong>


</td>


</tr>


<?php

}

?>


</tbody>


</table>


</div>


</div>


</div>



<!-- =========================
     REPORT SUMMARY
========================= -->


<div class="section">


<h2>

📊 Business Summary

</h2>


<p style="color:#9ca3af; line-height:1.8;">

Emerald Car Rental currently has

<strong style="color:white;">

<?php echo $totalVehicles; ?>

</strong>

vehicles and

<strong style="color:white;">

<?php echo $totalClients; ?>

</strong>

registered clients.

A total of

<strong style="color:white;">

<?php echo $totalBookings; ?>

</strong>

bookings have been recorded.

Approved bookings have generated

<strong style="color:#22c55e;">

$<?php echo number_format(
    $totalRevenue,
    2
); ?>

</strong>

in revenue.

</p>


</div>


</div>


</body>

</html>

