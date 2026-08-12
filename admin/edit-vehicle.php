<?php

session_start();

include('../includes/config.php');


if(empty($_SESSION['alogin'])){

    header("location:index.php");
    exit();

}


$msg="";


// GET VEHICLE ID

if(!isset($_GET['id'])){

    header("location:manage-vehicles.php");
    exit();

}


$id=$_GET['id'];




// FETCH VEHICLE

$query=$dbh->prepare("

SELECT *

FROM tblvehicles

WHERE id=:id

");


$query->execute([

":id"=>$id

]);


$vehicle=$query->fetch(PDO::FETCH_OBJ);



if(!$vehicle){

    header("location:manage-vehicles.php");
    exit();

}





// GET BRANDS

$brands=$dbh->query("

SELECT *

FROM tblbrands

ORDER BY BrandName ASC

")->fetchAll(PDO::FETCH_OBJ);


$imageName=$vehicle->Vimage1;


if(isset($_FILES['image']['name']) && $_FILES['image']['name']!=""){


$imageName=$_FILES['image']['name'];


$tmp=$_FILES['image']['tmp_name'];


move_uploaded_file(

$tmp,

"../cars/".$imageName

);


}



// UPDATE VEHICLE


if(isset($_POST['update_vehicle'])){


$sql="

UPDATE tblvehicles SET


VehiclesTitle=:title,

VehiclesBrand=:brand,

PricePerDay=:price,

FuelType=:fuel,

ModelYear=:year,

SeatingCapacity=:seats,

Vimage1=:image


WHERE id=:id


";



$query=$dbh->prepare($sql);



$query->execute([


":title"=>$_POST['title'],

":brand"=>$_POST['brand'],

":price"=>$_POST['price'],

":fuel"=>$_POST['fuel'],

":year"=>$_POST['year'],

":seats"=>$_POST['seats'],

":image"=>$imageName,

":id"=>$id


]);



$msg="Vehicle updated successfully";



// reload new data

$query=$dbh->prepare("

SELECT *

FROM tblvehicles

WHERE id=:id

");


$query->execute([":id"=>$id]);


$vehicle=$query->fetch(PDO::FETCH_OBJ);



}



?>



<!DOCTYPE html>

<html>


<head>

<title>
Edit Vehicle
</title>


<style>


body{

font-family:Arial;
background:#f5f7fa;
padding:30px;

}



.container{

max-width:800px;
margin:auto;

}



.card{

background:white;
padding:30px;
border-radius:15px;
box-shadow:0 5px 15px #ddd;

}



input,
select{

width:100%;
padding:12px;
margin-bottom:15px;
border:1px solid #ddd;
border-radius:8px;

}



button,
a{

padding:12px 20px;
border:none;
border-radius:8px;
text-decoration:none;
cursor:pointer;

}



.update{

background:#10b981;
color:white;

}



.back{

background:#064e3b;
color:white;

}



.car-img{

width:200px;
height:130px;
object-fit:cover;
border-radius:10px;

}


</style>


</head>



<body>


<div class="container">


<a href="manage-vehicles.php" class="back">

← Back Vehicles

</a>



<div class="card">


<h1>
✏️ Edit Vehicle
</h1>



<?php

if($msg!=""){

echo "

<p style='color:green'>
$msg
</p>

";

}

?>



<img 

src="../cars/<?php echo $vehicle->Vimage1; ?>"

class="car-img">





<form method="post" enctype="multipart/form-data">



<label>
Vehicle Name
</label>


<input 

type="text"

name="title"

value="<?php echo $vehicle->VehiclesTitle; ?>"

>




<label>
Brand
</label>



<select name="brand">


<?php foreach($brands as $b){ ?>


<option

value="<?php echo $b->id; ?>"

<?php

if($b->id==$vehicle->VehiclesBrand){

echo "selected";

}

?>

>


<?php echo $b->BrandName; ?>


</option>


<?php } ?>


</select>






<label>
Price Per Day
</label>


<input

type="number"

name="price"

value="<?php echo $vehicle->PricePerDay; ?>"

>





<label>
Fuel Type
</label>



<select name="fuel">


<option
<?php if($vehicle->FuelType=="Petrol"){echo "selected";}?>
>
Petrol
</option>


<option
<?php if($vehicle->FuelType=="Diesel"){echo "selected";}?>
>
Diesel
</option>


<option
<?php if($vehicle->FuelType=="CNG"){echo "selected";}?>
>
CNG
</option>


</select>






<label>
Model Year
</label>


<input

type="number"

name="year"

value="<?php echo $vehicle->ModelYear; ?>"

>






<label>
Seats
</label>


<input

type="number"

name="seats"

value="<?php echo $vehicle->SeatingCapacity; ?>"

>



<label>
Change Image
</label>

<input

type="file"

name="image"

accept=".jpg,.jpeg,.png"

>




<button 

class="update"

name="update_vehicle">

Update Vehicle

</button>



</form>



</div>


</div>


</body>


</html>