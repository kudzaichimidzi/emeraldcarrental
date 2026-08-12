```php
<?php

session_start();

include('../includes/config.php');


if(empty($_SESSION['alogin'])){

    header("Location: login.php");

    exit();

}


if(!isset($_GET['email'])){

    header("Location: clients.php");

    exit();

}


$email = $_GET['email'];


/* =========================
   CLIENT INFORMATION
========================= */

$sql = "

SELECT

    name,
    email,
    phone,
    dob,
    LicenseNumber,
    CountryOfIssuance

FROM tblbooking

WHERE email = :email

LIMIT 1

";


$query = $dbh->prepare($sql);

$query->bindParam(':email',$email);

$query->execute();

$client = $query->fetch(PDO::FETCH_OBJ);


if(!$client){

    header("Location: clients.php");

    exit();

}


/* =========================
   CLIENT STATISTICS
========================= */

$statsQuery = $dbh->prepare("

SELECT

    COUNT(*) AS totalBookings,

    SUM(TotalAmount) AS totalSpent

FROM tblbooking

WHERE email = :email

");

$statsQuery->bindParam(':email',$email);

$statsQuery->execute();

$stats = $statsQuery->fetch(PDO::FETCH_OBJ);


$totalBookings = $stats->totalBookings;

$totalSpent = $stats->totalSpent;


if($totalSpent == ""){

    $totalSpent = 0;

}


/* =========================
   BOOKING HISTORY
========================= */

$bookingsQuery = $dbh->prepare("

SELECT

    b.*,

    v.VehiclesTitle,

    br.BrandName

FROM tblbooking b

LEFT JOIN tblvehicles v

    ON v.id = b.VehicleId

LEFT JOIN tblbrands br

    ON br.id = v.VehiclesBrand

WHERE b.email = :email

ORDER BY b.id DESC

");


$bookingsQuery->bindParam(':email',$email);

$bookingsQuery->execute();

$bookings = $bookingsQuery;


?>


<!DOCTYPE html>

<html lang="en">


<head>

<meta charset="UTF-8">

<title>

<?php echo htmlentities($client->name); ?>

- Client Details

</title>


<style>

/* =========================
   GENERAL
========================= */

*{

    box-sizing: border-box;

}


body{

    margin: 0;

    background: #0b1220;

    color: #ffffff;

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
   BACK BUTTON
========================= */

.back{

    display: inline-block;

    color: #9ca3af;

    margin-bottom: 20px;

}


.back:hover{

    color: white;

}


/* =========================
   HEADER
========================= */

.page-header{

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 25px;

}


.page-header h1{

    margin: 0;

}


.page-header p{

    color: #9ca3af;

    margin-top: 7px;

}


/* =========================
   PROFILE
========================= */

.profile{

    background: #111827;

    border: 1px solid #1f2937;

    border-radius: 16px;

    padding: 25px;

    display: flex;

    align-items: center;

    gap: 20px;

    margin-bottom: 25px;

}


.avatar{

    width: 75px;

    height: 75px;

    border-radius: 50%;

    background: #2563eb;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 30px;

    font-weight: bold;

}


.profile h2{

    margin: 0 0 8px;

}


.profile p{

    color: #9ca3af;

    margin: 5px 0;

}


/* =========================
   STATISTICS
========================= */

.stats{

    display: grid;

    grid-template-columns:
    repeat(2,1fr);

    gap: 20px;

    margin-bottom: 25px;

}


.stat{

    background: #111827;

    border: 1px solid #1f2937;

    border-radius: 15px;

    padding: 25px;

}


.stat p{

    color: #9ca3af;

    margin: 0 0 8px;

}


.stat h2{

    margin: 0;

    font-size: 27px;

}


/* =========================
   INFORMATION
========================= */

.info{

    background: #111827;

    border: 1px solid #1f2937;

    border-radius: 15px;

    padding: 25px;

    margin-bottom: 25px;

}


.info h2{

    margin-top: 0;

    margin-bottom: 20px;

}


.info-grid{

    display: grid;

    grid-template-columns:
    repeat(3,1fr);

    gap: 20px;

}


.info-item{

    background: #0b1220;

    border: 1px solid #1f2937;

    padding: 15px;

    border-radius: 10px;

}


.info-item label{

    display: block;

    color: #6b7280;

    font-size: 12px;

    margin-bottom: 6px;

}


.info-item span{

    color: #ffffff;

}


/* =========================
   BOOKINGS
========================= */

.section{

    background: #111827;

    border: 1px solid #1f2937;

    border-radius: 15px;

    padding: 25px;

}


.section h2{

    margin-top: 0;

}


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

    font-size: 13px;

    padding: 15px;

    border-bottom: 1px solid #374151;

}


td{

    padding: 16px 15px;

    border-bottom: 1px solid #1f2937;

}


tr:hover{

    background: #172033;

}


/* =========================
   STATUS
========================= */

.status{

    padding: 6px 11px;

    border-radius: 20px;

    font-size: 12px;

}


.approved{

    background: #14532d;

    color: #86efac;

}


.pending{

    background: #713f12;

    color: #fde68a;

}


.cancelled{

    background: #7f1d1d;

    color: #fca5a5;

}


/* =========================
   RESPONSIVE
========================= */

@media(max-width:900px){

    .sidebar{

        width: 190px;

    }


    .main{

        margin-left: 190px;

    }


    .info-grid{

        grid-template-columns: 1fr 1fr;

    }

}


@media(max-width:650px){

    .sidebar{

        position: static;

        width: 100%;

        height: auto;

    }


    .main{

        margin-left: 0;

        padding: 15px;

    }


    .profile{

        flex-direction: column;

        align-items: flex-start;

    }


    .stats{

        grid-template-columns: 1fr;

    }


    .info-grid{

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


<a href="clients.php" class="active">

Clients

</a>


<a href="#">

Drivers

</a>


<a href="#">

Payments

</a>


<a href="#">

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
     MAIN CONTENT
========================= -->

<div class="main">


<a href="clients.php" class="back">

← Back to Clients

</a>



<div class="page-header">

<div>

<h1>

Client Details

</h1>

<p>

Complete customer information and booking history

</p>

</div>

</div>



<!-- =========================
     PROFILE
========================= -->

<div class="profile">


<div class="avatar">

<?php

echo strtoupper(
    substr($client->name,0,1)
);

?>

</div>


<div>

<h2>

<?php echo htmlentities($client->name); ?>

</h2>


<p>

📧 <?php echo htmlentities($client->email); ?>

</p>


<p>

📱 <?php echo htmlentities($client->phone); ?>

</p>

</div>


</div>



<!-- =========================
     STATISTICS
========================= -->

<div class="stats">


<div class="stat">

<p>

Total Bookings

</p>

<h2>

<?php echo $totalBookings; ?>

</h2>

</div>


<div class="stat">

<p>

Total Spent

</p>

<h2>

$<?php echo number_format($totalSpent,2); ?>

</h2>

</div>


</div>



<!-- =========================
     CLIENT INFORMATION
========================= -->

<div class="info">


<h2>

Personal Information

</h2>


<div class="info-grid">


<div class="info-item">

<label>

Full Name

</label>

<span>

<?php echo htmlentities($client->name); ?>

</span>

</div>


<div class="info-item">

<label>

Email

</label>

<span>

<?php echo htmlentities($client->email); ?>

</span>

</div>


<div class="info-item">

<label>

Phone

</label>

<span>

<?php echo htmlentities($client->phone); ?>

</span>

</div>


<div class="info-item">

<label>

Date of Birth

</label>

<span>

<?php

if(!empty($client->dob)){

    echo date(
        "d M Y",
        strtotime($client->dob)
    );

}
else{

    echo "Not provided";

}

?>

</span>

</div>


<div class="info-item">

<label>

License Number

</label>

<span>

<?php

if(!empty($client->LicenseNumber)){

    echo htmlentities(
        $client->LicenseNumber
    );

}
else{

    echo "Not provided";

}

?>

</span>

</div>


<div class="info-item">

<label>

Country of Issuance

</label>

<span>

<?php

if(!empty($client->CountryOfIssuance)){

    echo htmlentities(
        $client->CountryOfIssuance
    );

}
else{

    echo "Not provided";

}

?>

</span>

</div>


</div>

</div>



<!-- =========================
     BOOKING HISTORY
========================= -->

<div class="section">


<h2>

Booking History

</h2>


<div class="table-container">


<table>


<thead>

<tr>

<th>Booking ID</th>

<th>Vehicle</th>

<th>Pickup</th>

<th>Drop-off</th>

<th>Dates</th>

<th>Payment</th>

<th>Total</th>

<th>Status</th>

</tr>

</thead>


<tbody>


<?php

while($booking = $bookings->fetch(PDO::FETCH_OBJ)){

?>


<tr>


<td>

<?php echo htmlentities(
    $booking->BookingNumber
); ?>

</td>


<td>

<?php

echo htmlentities(
    $booking->BrandName
    . " "
    . $booking->VehiclesTitle
);

?>

</td>


<td>

<?php echo htmlentities(
    $booking->PickupLocation
); ?>

</td>


<td>

<?php echo htmlentities(
    $booking->DropoffLocation
); ?>

</td>


<td>

<?php

echo date(
    "d M Y",
    strtotime($booking->FromDate)
);

?>

<br>

<small>

to

<?php

echo date(
    "d M Y",
    strtotime($booking->ToDate)
);

?>

</small>

</td>


<td>

<?php echo htmlentities(
    $booking->PaymentMethod
); ?>

</td>


<td>

<strong>

$<?php echo number_format(
    $booking->TotalAmount,
    2
); ?>

</strong>

</td>


<td>


<?php

if($booking->Status == 1){

    echo "<span class='status approved'>
    Approved
    </span>";

}

elseif($booking->Status == 2){

    echo "<span class='status cancelled'>
    Cancelled
    </span>";

}

else{

    echo "<span class='status pending'>
    Pending
    </span>";

}

?>


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


</body>

</html>
```
