```php
<?php

session_start();

include('../includes/config.php');


if(empty($_SESSION['alogin'])){

    header("Location: login.php");

    exit();

}


/* =========================
   PAYMENT STATISTICS
========================= */


$totalRevenue = $dbh->query("

SELECT SUM(TotalAmount)

FROM tblbooking

WHERE Status = 1

")->fetchColumn();


if($totalRevenue == ""){

    $totalRevenue = 0;

}


$pendingAmount = $dbh->query("

SELECT SUM(TotalAmount)

FROM tblbooking

WHERE Status = 0

")->fetchColumn();


if($pendingAmount == ""){

    $pendingAmount = 0;

}


$paidBookings = $dbh->query("

SELECT COUNT(*)

FROM tblbooking

WHERE Status = 1

")->fetchColumn();


$pendingBookings = $dbh->query("

SELECT COUNT(*)

FROM tblbooking

WHERE Status = 0

")->fetchColumn();


/* =========================
   PAYMENT HISTORY
========================= */


$payments = $dbh->query("

SELECT

    b.BookingNumber,

    b.name,

    b.email,

    b.PaymentMethod,

    b.PricePerDay,

    b.TotalAmount,

    b.Status,

    b.PostingDate,

    v.VehiclesTitle,

    br.BrandName

FROM tblbooking b

LEFT JOIN tblvehicles v

ON v.id = b.VehicleId

LEFT JOIN tblbrands br

ON br.id = v.VehiclesBrand

ORDER BY b.id DESC

");


?>


<!DOCTYPE html>

<html lang="en">


<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Emerald Car Rental - Payments</title>


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
   TOP
========================= */


.top{

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 30px;

}


.top h1{

    margin: 0;

}


.top p{

    color: #9ca3af;

    margin-top: 7px;

}


/* =========================
   STAT CARDS
========================= */


.cards{

    display: grid;

    grid-template-columns:
    repeat(4,1fr);

    gap: 20px;

    margin-bottom: 30px;

}


.card{

    background: #111827;

    border: 1px solid #1f2937;

    border-radius: 15px;

    padding: 22px;

}


.card p{

    color: #9ca3af;

    margin: 0 0 10px;

}


.card h2{

    margin: 0;

    font-size: 27px;

}


.revenue{

    color: #22c55e;

}


.pending-money{

    color: #f59e0b;

}


.paid{

    color: #60a5fa;

}


.pending-count{

    color: #fbbf24;

}


/* =========================
   PAYMENT SECTION
========================= */


.section{

    background: #111827;

    border: 1px solid #1f2937;

    border-radius: 15px;

    padding: 25px;

}


.section h2{

    margin-top: 0;

    margin-bottom: 20px;

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
   CLIENT
========================= */


.client-name{

    font-weight: bold;

}


.client-email{

    color: #6b7280;

    font-size: 12px;

    margin-top: 4px;

}


/* =========================
   PAYMENT METHOD
========================= */


.payment-method{

    background: #1f2937;

    padding: 6px 10px;

    border-radius: 7px;

    font-size: 12px;

}


/* =========================
   STATUS
========================= */


.status{

    display: inline-block;

    padding: 6px 11px;

    border-radius: 20px;

    font-size: 12px;

}


.paid-status{

    background: #14532d;

    color: #86efac;

}


.pending-status{

    background: #713f12;

    color: #fde68a;

}


.cancelled-status{

    background: #7f1d1d;

    color: #fca5a5;

}


/* =========================
   AMOUNT
========================= */


.amount{

    font-weight: bold;

    color: #22c55e;

}


/* =========================
   RESPONSIVE
========================= */


@media(max-width:1000px){

    .cards{

        grid-template-columns:
        repeat(2,1fr);

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


    .top{

        display: block;

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


<a href="payments.php" class="active">

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
     MAIN
========================= -->


<div class="main">


<div class="top">


<div>

<h1>

Payments

</h1>


<p>

Track rental payments and outstanding bookings

</p>

</div>


</div>



<!-- =========================
     STATISTICS
========================= -->


<div class="cards">


<div class="card">


<p>

💰 Total Revenue

</p>


<h2 class="revenue">

$<?php echo number_format($totalRevenue,2); ?>

</h2>


</div>



<div class="card">


<p>

⏳ Pending Amount

</p>


<h2 class="pending-money">

$<?php echo number_format($pendingAmount,2); ?>

</h2>


</div>



<div class="card">


<p>

✅ Paid Bookings

</p>


<h2 class="paid">

<?php echo $paidBookings; ?>

</h2>


</div>



<div class="card">


<p>

⏰ Pending Bookings

</p>


<h2 class="pending-count">

<?php echo $pendingBookings; ?>

</h2>


</div>


</div>



<!-- =========================
     PAYMENT HISTORY
========================= -->


<div class="section">


<h2>

Payment History

</h2>


<div class="table-container">


<table>


<thead>


<tr>

<th>Booking</th>

<th>Client</th>

<th>Vehicle</th>

<th>Payment Method</th>

<th>Amount</th>

<th>Date</th>

<th>Status</th>

</tr>


</thead>


<tbody>


<?php


while($payment = $payments->fetch(PDO::FETCH_OBJ)){


?>


<tr>


<td>

<strong>

<?php echo htmlentities($payment->BookingNumber); ?>

</strong>

</td>



<td>


<div class="client-name">

<?php echo htmlentities($payment->name); ?>

</div>


<div class="client-email">

<?php echo htmlentities($payment->email); ?>

</div>


</td>



<td>

<?php


echo htmlentities(

    $payment->BrandName
    . " "
    . $payment->VehiclesTitle

);


?>

</td>



<td>


<span class="payment-method">

<?php


if(!empty($payment->PaymentMethod)){

    echo htmlentities(
        $payment->PaymentMethod
    );

}

else{

    echo "Not specified";

}


?>

</span>


</td>



<td>


<span class="amount">

$<?php echo number_format(
    $payment->TotalAmount,
    2
); ?>

</span>


</td>



<td>


<?php


echo date(

    "d M Y",

    strtotime($payment->PostingDate)

);


?>

</td>



<td>


<?php


if($payment->Status == 1){

    echo "

    <span class='status paid-status'>

    Paid

    </span>

    ";

}

elseif($payment->Status == 2){

    echo "

    <span class='status cancelled-status'>

    Cancelled

    </span>

    ";

}

else{

    echo "

    <span class='status pending-status'>

    Pending

    </span>

    ";

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
