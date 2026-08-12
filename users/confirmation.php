<?php

session_start();

include('../includes/config.php');


// Check booking id

if(!isset($_GET['booking'])){

    die("Invalid booking");

}


$id = intval($_GET['booking']);



// Get booking details

$sql = "

SELECT 
b.*,
v.VehiclesTitle,
v.VehicleType

FROM tblbooking b

JOIN tblvehicles v

ON b.VehicleId = v.id

WHERE b.id = :id

";


$query = $dbh->prepare($sql);

$query->bindParam(':id',$id);

$query->execute();


$result = $query->fetch(PDO::FETCH_OBJ);



if(!$result){

    die("Booking not found");

}



// Calculate rental days

$days = (strtotime($result->ToDate) - strtotime($result->FromDate)) / (60*60*24);


if($days < 1){

    $days = 1;

}


?>


<!DOCTYPE html>
<html>

<head>

<title>Booking Confirmation</title>


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">


<style>


body{

background:#111;

color:white;

font-family:Arial;

padding:40px;

}



.box{

background:#000;

max-width:700px;

margin:auto;

padding:35px;

border-radius:15px;

border:1px solid #444;

}



h2{

text-align:center;

color:#00ff66;

margin-bottom:30px;

}



.section{

background:#151515;

padding:20px;

border-radius:10px;

margin-bottom:20px;

}



.row-item{

display:flex;

justify-content:space-between;

border-bottom:1px solid #333;

padding:8px 0;

}



.label{

color:#aaa;

}



.value{

font-weight:bold;

}



.total{

font-size:22px;

color:#00ff66;

}



.status{

text-align:center;

font-size:20px;

padding:10px;

border-radius:8px;

}



.pending{

background:#664d03;

color:#ffc107;

}


.approved{

background:#064d22;

color:#00ff66;

}


.cancelled{

background:#5c0000;

color:red;

}



.btn{

width:100%;

margin-top:10px;

}



</style>


</head>


<body>



<div class="box">


<h2>
✅ Booking Confirmation
</h2>



<div class="section">


<div class="row-item">

<span class="label">Booking Number</span>

<span class="value">
<?php echo $result->BookingNumber; ?>
</span>

</div>



<div class="row-item">

<span class="label">Customer Name</span>

<span class="value">
<?php echo $result->name; ?>
</span>

</div>



<div class="row-item">

<span class="label">Email</span>

<span class="value">
<?php echo $result->email; ?>
</span>

</div>



<div class="row-item">

<span class="label">Phone</span>

<span class="value">
<?php echo $result->phone; ?>
</span>

</div>


</div>





<div class="section">


<h5>🚗 Vehicle Details</h5>


<div class="row-item">

<span class="label">Car Model</span>

<span class="value">
<?php echo $result->VehiclesTitle; ?>
</span>

</div>



<div class="row-item">

<span class="label">Vehicle Type</span>

<span class="value">
<?php 

if($result->VehicleType==""){
    echo "Not specified";
}else{
    echo $result->VehicleType;
}

?>
</span>

</div>



<div class="row-item">

<span class="label">Price Per Day</span>

<span class="value">
$<?php echo $result->PricePerDay; ?>
</span>

</div>



</div>





<div class="section">


<h5>📅 Rental Details</h5>



<div class="row-item">

<span class="label">Pickup Location</span>

<span class="value">
<?php echo $result->PickupLocation; ?>
</span>

</div>



<div class="row-item">

<span class="label">Dropoff Location</span>

<span class="value">
<?php echo $result->DropoffLocation; ?>
</span>

</div>



<div class="row-item">

<span class="label">From Date</span>

<span class="value">
<?php echo $result->FromDate; ?>
</span>

</div>



<div class="row-item">

<span class="label">To Date</span>

<span class="value">
<?php echo $result->ToDate; ?>
</span>

</div>



<div class="row-item">

<span class="label">Rental Days</span>

<span class="value">
<?php echo $days; ?> day(s)
</span>

</div>



<div class="row-item">

<span class="label">Pickup Time</span>

<span class="value">
<?php echo $result->PickupTime; ?>
</span>

</div>



<div class="row-item">

<span class="label">Dropoff Time</span>

<span class="value">
<?php echo $result->DropoffTime; ?>
</span>

</div>



</div>





<div class="section">


<div class="row-item">

<span class="label">Payment Method</span>

<span class="value">
<?php echo $result->PaymentMethod; ?>
</span>

</div>



<div class="row-item">

<span class="label total">
Total Amount
</span>

<span class="value total">
$<?php echo $result->TotalAmount; ?>
</span>

</div>


</div>





<?php

if($result->Status == 0){

    echo "
    <div class='status pending'>
    ⏳ Pending Approval
    </div>
    ";

}

elseif($result->Status == 1){

    echo "
    <div class='status approved'>
    ✅ Booking Approved
    </div>
    ";

}

else{

    echo "
    <div class='status cancelled'>
    ❌ Booking Cancelled
    </div>
    ";

}

?>



<a href="receipt.php?id=<?php echo $result->id; ?>" class="btn btn-success">

📄 Download Receipt

</a>



<a href="car-listing.php" class="btn btn-secondary">

🚗 Browse More Cars

</a>



<a href="../index.php" class="btn btn-dark">

🏠 Home

</a>



</div>



</body>

</html>