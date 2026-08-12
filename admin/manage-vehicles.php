<?php
session_start();

include('../includes/config.php');


if(empty($_SESSION['alogin'])){

    header("location:index.php");
    exit();

}


$msg="";
$error="";



// ADD VEHICLE

if(isset($_POST['add_vehicle'])){


$sql="
INSERT INTO tblvehicles
(
VehiclesTitle,
VehiclesBrand,
PricePerDay,
FuelType,
ModelYear,
SeatingCapacity,
Vimage1
)

VALUES

(
:title,
:brand,
:price,
:fuel,
:year,
:seats,
:image
)

";

$imageName = "";

if(isset($_FILES['image']['name']) && $_FILES['image']['name']!=""){

    $imageName = $_FILES['image']['name'];

    $tmp = $_FILES['image']['tmp_name'];

    move_uploaded_file(
        $tmp,
        "../cars/".$imageName
    );

}

$query=$dbh->prepare($sql);


$query->execute([

":title"=>$_POST['title'],
":brand"=>$_POST['brand'],
":price"=>$_POST['price'],
":fuel"=>$_POST['fuel'],
":year"=>$_POST['year'],
":seats"=>$_POST['seats'],
":image"=>$imageName

]);


$msg="Vehicle added successfully";


}




// DELETE VEHICLE

if(isset($_GET['delete'])){


$id=$_GET['delete'];


$sql="
DELETE FROM tblvehicles
WHERE id=:id
";


$query=$dbh->prepare($sql);

$query->execute([

":id"=>$id

]);


$msg="Vehicle deleted";


}





// GET VEHICLES


$vehicles=$dbh->query("

SELECT 

v.*,
b.BrandName


FROM tblvehicles v


LEFT JOIN tblbrands b

ON b.id=v.VehiclesBrand


ORDER BY v.id DESC


")->fetchAll(PDO::FETCH_OBJ);




// GET BRANDS


$brands=$dbh->query("

SELECT *

FROM tblbrands

ORDER BY BrandName ASC

")->fetchAll(PDO::FETCH_OBJ);

/* ================= VEHICLE STATISTICS ================= */


$totalVehicles = $dbh->query("
SELECT COUNT(*) 
FROM tblvehicles
")->fetchColumn();



$rentedVehicles = $dbh->query("

SELECT COUNT(DISTINCT VehicleId)

FROM tblbooking

WHERE Status=1

AND CURDATE() BETWEEN FromDate AND ToDate

")->fetchColumn();



$availableVehicles = $totalVehicles - $rentedVehicles;



$totalVehicleIncome = $dbh->query("

SELECT SUM(
GREATEST(DATEDIFF(ToDate,FromDate),1)
*
tblvehicles.PricePerDay
)

FROM tblbooking

LEFT JOIN tblvehicles

ON tblvehicles.id = tblbooking.VehicleId

WHERE tblbooking.Status=1

")->fetchColumn();


if($totalVehicleIncome==""){
    $totalVehicleIncome=0;
}

?>



<!DOCTYPE html>

<html>

<head>

<title>
Manage Vehicles
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

    text-decoration: none;

    border-radius: 8px;

    transition: .3s;

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
   TOP BAR
========================= */

.topbar{

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 30px;

}


.topbar h1{

    margin: 0;

    font-size: 28px;

}


.topbar p{

    color: #9ca3af;

    margin-top: 7px;

}


.admin{

    background: #111827;

    border: 1px solid #1f2937;

    padding: 10px 15px;

    border-radius: 10px;

}


/* =========================
   SECTION
========================= */

.section{

    background: #111827;

    border: 1px solid #1f2937;

    border-radius: 15px;

    padding: 25px;

    margin-bottom: 30px;

}


.section h2{

    margin-top: 0;

}


/* =========================
   STATISTICS
========================= */

.stats{

    display: grid;

    grid-template-columns:
    repeat(4,1fr);

    gap: 20px;

}


.stat-card{

    background: #111827;

    border: 1px solid #1f2937;

    border-radius: 15px;

    padding: 25px;

}


.stat-card h3{

    color: #9ca3af;

    margin-top: 0;

}


.stat-card h1{

    margin-bottom: 0;

}


.stat-total{

    border-left: 4px solid #2563eb;

}


.stat-rented{

    border-left: 4px solid #dc2626;

}


.stat-available{

    border-left: 4px solid #10b981;

}


.stat-income{

    border-left: 4px solid #f59e0b;

}


/* =========================
   MESSAGE
========================= */

.message{

    background: #14532d;

    color: #86efac;

    border: 1px solid #166534;

    padding: 12px 15px;

    border-radius: 8px;

    margin-bottom: 20px;

}


/* =========================
   FORM
========================= */

.form-grid{

    display: grid;

    grid-template-columns:
    repeat(4,1fr);

    gap: 15px;

}


.form-group{

    display: flex;

    flex-direction: column;

}


.form-group label{

    color: #9ca3af;

    font-size: 13px;

    margin-bottom: 7px;

}


input,
select{

    width: 100%;

    padding: 12px;

    background: #0b1220;

    color: white;

    border: 1px solid #374151;

    border-radius: 8px;

    outline: none;

}


input:focus,
select:focus{

    border-color: #2563eb;

}


select option{

    background: #111827;

    color: white;

}


.add-button{

    padding: 12px;

    background: #2563eb;

    color: white;

    border: none;

    border-radius: 8px;

    cursor: pointer;

    align-self: end;

}


.add-button:hover{

    background: #1d4ed8;

}


/* =========================
   SEARCH
========================= */

.search-box{

    margin-bottom: 20px;

}


#search{

    max-width: 350px;

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

    text-align: left;

    color: #9ca3af;

    font-size: 13px;

    padding: 15px;

    border-bottom: 1px solid #374151;

}


td{

    padding: 17px 15px;

    border-bottom: 1px solid #1f2937;

}


tbody tr{

    transition: .2s;

}


tbody tr:hover{

    background: #172033;

}


/* =========================
   VEHICLE IMAGE
========================= */

.car-img{

    width: 110px;

    height: 70px;

    object-fit: cover;

    border-radius: 9px;

    border: 1px solid #374151;

}


/* =========================
   BUTTONS
========================= */

.btn{

    display: inline-block;

    padding: 7px 12px;

    border-radius: 7px;

    text-decoration: none;

    font-size: 13px;

    margin-right: 5px;

}


.back{

    background: #374151;

    color: white;

}


.back:hover{

    background: #4b5563;

}


.edit{

    background: #d97706;

    color: white;

}


.edit:hover{

    background: #b45309;

}


.delete{

    background: #dc2626;

    color: white;

}


.delete:hover{

    background: #b91c1c;

}


/* =========================
   STATUS
========================= */

.rented-status{

    background: #7f1d1d;

    color: #fca5a5;

    padding: 6px 10px;

    border-radius: 20px;

    font-size: 12px;

}


.available-status{

    background: #14532d;

    color: #86efac;

    padding: 6px 10px;

    border-radius: 20px;

    font-size: 12px;

}


/* =========================
   MOBILE
========================= */

@media(max-width:1100px){

    .stats{

        grid-template-columns:
        repeat(2,1fr);

    }


    .form-grid{

        grid-template-columns:
        repeat(2,1fr);

    }

}


@media(max-width:900px){

    .sidebar{

        width: 190px;

    }


    .main{

        margin-left: 190px;

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


    .topbar{

        flex-direction: column;

        align-items: flex-start;

        gap: 15px;

    }


    .stats{

        grid-template-columns: 1fr;

    }


    .form-grid{

        grid-template-columns: 1fr;

    }

}



</style>


</head>



<body>


<div class="container">



<div class="top">


<h1>
🚗 Manage Vehicles
</h1>


<a href="index.php" class="btn back">

← Back Dashboard

</a>


</div>



<?php

if($msg!=""){

echo "

<p style='color:green'>
$msg
</p>

";

}

?>

<!-- VEHICLE STATISTICS -->

<div class="card">

<h2>
Vehicle Statistics
</h2>


<div style="
display:grid;
grid-template-columns:repeat(4,1fr);
gap:20px;
">


<div style="background:#064e3b;color:white;padding:20px;border-radius:15px;">
<h3>Total Vehicles</h3>
<h1>
<?php echo $totalVehicles; ?>
</h1>
</div>



<div style="background:#dc2626;color:white;padding:20px;border-radius:15px;">
<h3>Rented</h3>
<h1>
<?php echo $rentedVehicles; ?>
</h1>
</div>



<div style="background:#10b981;color:white;padding:20px;border-radius:15px;">
<h3>Available</h3>
<h1>
<?php echo $availableVehicles; ?>
</h1>
</div>



<div style="background:#f59e0b;color:white;padding:20px;border-radius:15px;">
<h3>Income</h3>
<h1>
$<?php echo $totalVehicleIncome; ?>
</h1>
</div>


</div>


</div>


<!-- ADD VEHICLE -->


<div class="card">


<h2>
Add New Vehicle
</h2>



<form method="post" enctype="multipart/form-data">

<div class="form-grid">


<input 
type="text"
name="title"
placeholder="Vehicle Title"
required>



<select name="brand">


<option>
Select Brand
</option>


<?php foreach($brands as $b){ ?>


<option value="<?php echo $b->id; ?>">

<?php echo $b->BrandName; ?>

</option>


<?php } ?>


</select>



<input
type="number"
name="price"
placeholder="Price Per Day"
>



<select name="fuel">


<option>
Fuel Type
</option>

<option>
Petrol
</option>


<option>
Diesel
</option>


<option>
CNG
</option>


</select>



<input
type="number"
name="year"
placeholder="Model Year"
>



<input
type="number"
name="seats"
placeholder="Seats"
>



<label>
Vehicle Image
</label>

<input
type="file"
name="image"
accept=".jpg,.jpeg,.png"
>



<button 
class="add"
name="add_vehicle">

Add Vehicle

</button>


</div>


</form>


</div>





<!-- SEARCH -->


<div class="card">


<input 
id="search"
class="search"
placeholder="Search vehicles...">


</div>






<!-- TABLE -->


<div class="card">


<h2>
Existing Vehicles
</h2>


<table id="table">


<tr>

<th>
Image
</th>

<th>
Title
</th>

<th>
Brand
</th>

<th>
Price
</th>

<th>
Fuel
</th>

<th>
Year
</th>

<th>
Seats
</th>

<th>
Status
</th>

<th>
Action
</th>


</tr>



<?php foreach($vehicles as $v){ ?>


<tr>


<td>


<img 

src="../cars/<?php echo $v->Vimage1; ?>"

class="car-img">


</td>



<td>

<?php echo $v->VehiclesTitle; ?>

</td>



<td>

<?php echo $v->BrandName; ?>

</td>



<td>

$<?php echo $v->PricePerDay; ?>

</td>



<td>

<?php echo $v->FuelType; ?>

</td>



<td>

<?php echo $v->ModelYear; ?>

</td>



<td>

<?php echo $v->SeatingCapacity; ?>

</td>

<td>

<?php

$status=$dbh->query("

SELECT COUNT(*)

FROM tblbooking

WHERE VehicleId=".$v->id."

AND Status=1

AND CURDATE() BETWEEN FromDate AND ToDate

")->fetchColumn();



if($status>0){

echo "<span style='color:red;font-weight:bold'>
🔴 Rented
</span>";

}

else{

echo "<span style='color:green;font-weight:bold'>
🟢 Available
</span>";

}


?>

</td>

<td>

<a 
class="btn edit"
href="edit-vehicle.php?id=<?php echo $v->id; ?>">

Edit

</a>


<a 
class="btn back"
href="vehicle-details.php?id=<?php echo $v->id; ?>">

View

</a>

<a 
class="btn delete"
href="?delete=<?php echo $v->id; ?>"
onclick="return confirm('Delete vehicle?')">

Delete

</a>


</td>



</tr>


<?php } ?>


</table>


</div>



</div>





<script>


document.getElementById("search").addEventListener("keyup",function(){


let value=this.value.toLowerCase();


document.querySelectorAll("#table tr").forEach(function(row){


row.style.display=
row.innerText.toLowerCase().includes(value)
?
""
:
"none";


});


});


</script>



</body>


</html>