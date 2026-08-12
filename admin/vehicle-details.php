<?php

session_start();

include('../includes/config.php');


if(empty($_SESSION['alogin'])){

    header("location:index.php");
    exit();

}



if(!isset($_GET['id'])){

    header("location:manage-vehicles.php");
    exit();

}


$id=$_GET['id'];


// DELETE VEHICLE

if(isset($_GET['delete'])){


$delete=$dbh->prepare("

DELETE FROM tblvehicles

WHERE id=:id

");


$delete->execute([

":id"=>$id

]);


header("location:manage-vehicles.php");

exit();


}



// GET VEHICLE DETAILS

$query=$dbh->prepare("

SELECT 

v.*,
b.BrandName

FROM tblvehicles v

LEFT JOIN tblbrands b

ON b.id=v.VehiclesBrand

WHERE v.id=:id

");


$query->execute([

":id"=>$id

]);


$vehicle=$query->fetch(PDO::FETCH_OBJ);



if(!$vehicle){

header("location:manage-vehicles.php");

exit();

}




// CURRENT RENT STATUS

$status=$dbh->prepare("

SELECT COUNT(*)

FROM tblbooking

WHERE VehicleId=:id

AND Status=1

AND CURDATE() BETWEEN FromDate AND ToDate

");


$status->execute([

":id"=>$id

]);


$rented=$status->fetchColumn();





// TOTAL BOOKINGS

$totalBookings=$dbh->prepare("

SELECT COUNT(*)

FROM tblbooking

WHERE VehicleId=:id

");


$totalBookings->execute([

":id"=>$id

]);


$totalBookings=$totalBookings->fetchColumn();





// APPROVED BOOKINGS

$approvedBookings=$dbh->prepare("

SELECT COUNT(*)

FROM tblbooking

WHERE VehicleId=:id

AND Status=1

");


$approvedBookings->execute([

":id"=>$id

]);


$approvedBookings=$approvedBookings->fetchColumn();





// TOTAL INCOME

$income=$dbh->prepare("

SELECT SUM(

GREATEST(DATEDIFF(ToDate,FromDate),1)

*

:price

)

FROM tblbooking

WHERE VehicleId=:id

AND Status=1

");


$income->execute([

":price"=>$vehicle->PricePerDay,

":id"=>$id

]);


$totalIncome=$income->fetchColumn();



if($totalIncome==""){

$totalIncome=0;

}





// BOOKING HISTORY

$bookings=$dbh->prepare("

SELECT *

FROM tblbooking

WHERE VehicleId=:id

ORDER BY id DESC

");


$bookings->execute([

":id"=>$id

]);


$bookings=$bookings->fetchAll(PDO::FETCH_OBJ);



?>



<!DOCTYPE html>

<html>

<head>

<title>
Vehicle Details
</title>


<style>


body{

font-family:Arial;
background:#f5f7fa;
padding:30px;

}



.container{

max-width:900px;
margin:auto;

}



.card{

background:white;
padding:30px;
border-radius:15px;
box-shadow:0 5px 15px #ddd;

margin-top:20px;

}



.car-img{

width:100%;
height:350px;
object-fit:cover;
border-radius:15px;

}



.btn{

padding:12px 20px;
border-radius:8px;
text-decoration:none;
color:white;

}



.back{

background:#064e3b;

}



.edit{

background:#f59e0b;

}



.delete{

background:#dc2626;

}



.info{

font-size:18px;
line-height:35px;

}



.available{

color:green;
font-weight:bold;

}



.rented{

color:red;
font-weight:bold;

}

.gallery{
display:flex;
gap:15px;
margin-top:20px;
}

.gallery img{

width:180px;
height:120px;
object-fit:cover;
border-radius:10px;

}


.stats{

display:grid;
grid-template-columns:repeat(3,1fr);
gap:20px;

}


.stat{

background:#064e3b;
color:white;
padding:20px;
border-radius:15px;
text-align:center;

}


table{

width:100%;
border-collapse:collapse;

}


th{

background:#064e3b;
color:white;
padding:12px;

}


td{

padding:12px;
border-bottom:1px solid #ddd;

}

</style>


</head>



<body>


<div class="container">


<a href="manage-vehicles.php" class="btn back">

← Back Vehicles

</a>



<div class="card">


<h1>

🚗 
<?php echo $vehicle->BrandName." ".$vehicle->VehiclesTitle; ?>

</h1>



<img

src="../cars/<?php echo $vehicle->Vimage1; ?>"

class="car-img"

>



<div class="info">


<p>
<b>Brand:</b>

<?php echo $vehicle->BrandName; ?>

</p>



<p>
<b>Price:</b>

$<?php echo $vehicle->PricePerDay; ?> / day

</p>



<p>
<b>Fuel:</b>

<?php echo $vehicle->FuelType; ?>

</p>



<p>
<b>Model Year:</b>

<?php echo $vehicle->ModelYear; ?>

</p>



<p>
<b>Seats:</b>

<?php echo $vehicle->SeatingCapacity; ?>

</p>



<p>
<b>Vehicle Type:</b>

<?php echo $vehicle->VehicleType; ?>

</p>

<p>
<b>Description:</b><br>

<?php echo $vehicle->VehiclesOverview; ?>

</p>



<p>

<b>Status:</b>


<?php

if($rented>0){

echo "

<span class='rented'>

🔴 Rented

</span>

";

}

else{


echo "

<span class='available'>

🟢 Available

</span>

";


}


?>


</p>


</div>

<div class="card">

<h2>
📊 Vehicle Statistics
</h2>


<div class="stats">


<div class="stat">

<h3>
Total Bookings
</h3>

<h1>
<?php echo $totalBookings; ?>
</h1>

</div>


<div class="stat">

<h3>
Approved Rentals
</h3>

<h1>
<?php echo $approvedBookings; ?>
</h1>

</div>


<div class="stat">

<h3>
Income Generated
</h3>

<h1>
$<?php echo $totalIncome; ?>
</h1>

</div>


</div>

</div>

<div class="card">

<h2>
🖼 Vehicle Gallery
</h2>


<div class="gallery">


<?php if($vehicle->Vimage1!=""){ ?>

<img src="../cars/<?php echo $vehicle->Vimage1; ?>">

<?php } ?>


<?php if($vehicle->Vimage2!=""){ ?>

<img src="../cars/<?php echo $vehicle->Vimage2; ?>">

<?php } ?>


<?php if($vehicle->Vimage3!=""){ ?>

<img src="../cars/<?php echo $vehicle->Vimage3; ?>">

<?php } ?>


</div>


</div>

<div class="card">

<h2>
📘 Booking History
</h2>


<table>


<tr>

<th>
Booking Number
</th>

<th>
Customer
</th>

<th>
From
</th>

<th>
To
</th>

<th>
Status
</th>

</tr>


<?php foreach($bookings as $b){ ?>


<tr>

<td>
<?php echo $b->BookingNumber; ?>
</td>


<td>
<?php echo $b->email; ?>
</td>


<td>
<?php echo $b->FromDate; ?>
</td>


<td>
<?php echo $b->ToDate; ?>
</td>


<td>

<?php

if($b->Status==1){

echo "✔ Approved";

}

elseif($b->Status==2){

echo "✖ Cancelled";

}

else{

echo "⏳ Pending";

}

?>

</td>


</tr>


<?php } ?>


</table>


</div>

<br>


<a 

href="edit-vehicle.php?id=<?php echo $vehicle->id; ?>"

class="btn edit">

✏ Edit Vehicle

</a>



<a 

href="?delete=<?php echo $vehicle->id; ?>"

class="btn delete"

onclick="return confirm('Delete vehicle?')">

🗑 Delete

</a>



</div>


</div>


</body>


</html>