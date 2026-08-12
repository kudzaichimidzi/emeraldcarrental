<?php
session_start();
include('../includes/config.php');

// Fetch vehicles
$where = [];
$params = [];

if(!empty($_GET['search'])){
    $where[] = "(v.VehiclesTitle LIKE :search OR b.BrandName LIKE :search OR v.FuelType LIKE :search)";
    $params[':search'] = "%".$_GET['search']."%";
}
if(!empty($_GET['brand'])){
    $where[] = "v.VehiclesBrand = :brand";
    $params[':brand'] = $_GET['brand'];
}
if(!empty($_GET['fuel'])){
    $where[] = "v.FuelType = :fuel";
    $params[':fuel'] = $_GET['fuel'];
}

$sql = "SELECT v.*, b.BrandName 
        FROM tblvehicles v 
        JOIN tblbrands b ON b.id = v.VehiclesBrand";

if(count($where) > 0){
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY v.id DESC";

$query = $dbh->prepare($sql);

foreach($params as $key=>$val){
    $query->bindParam($key,$val);
}

$query->execute();
$cars = $query->fetchAll(PDO::FETCH_OBJ);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Car Listings</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="../css/cars.css">

</head>

<body>

<div class="container py-5">

<h2 class="text-center mb-4">Available Cars</h2>
<p>Choose from our selection of comfortable and reliable vehicles.</p>

<!-- 🔍 Search -->
<div class="search-box">
<form method="GET" class="row g-2">

<div class="col-md-4">
<input type="text" name="search" class="form-control" placeholder="Search car...">
</div>

<div class="col-md-3">
<select name="brand" class="form-control">
<option value="">All Brands</option>
<?php
$brands = $dbh->query("SELECT * FROM tblbrands")->fetchAll(PDO::FETCH_OBJ);
foreach($brands as $b){
    echo "<option value='{$b->id}'>{$b->BrandName}</option>";
}
?>
</select>
</div>

<div class="col-md-3">
<select name="fuel" class="form-control">
<option value="">Fuel Type</option>
<option>Petrol</option>
<option>Diesel</option>
<option>CNG</option>
</select>
</div>

<div class="col-md-2">
<button class="btn btn-custom w-100">
<i class="bi bi-search"></i>
</button>
</div>

</form>
</div>

<!-- 🚗 Cars -->
<div class="row">

<?php if(count($cars) > 0): ?>
<?php foreach($cars as $car): ?>

<div class="col-lg-4 col-md-6 mb-4">
<div class="car-card">

<img src='../cars/<?php echo htmlentities($car->Vimage1); ?>' class="w-100 car-img">

<div class="p-3">

<h5><?php echo htmlentities($car->BrandName . " " . $car->VehiclesTitle); ?></h5>

<p class="price">$<?php echo htmlentities($car->PricePerDay); ?> / day</p>

<div class="d-flex justify-content-between small text-muted mb-2">
<span><i class="bi bi-people"></i> <?php echo $car->SeatingCapacity; ?></span>
<span><i class="bi bi-calendar"></i> <?php echo $car->ModelYear; ?></span>
<span><i class="bi bi-fuel-pump"></i> <?php echo $car->FuelType; ?></span>
</div>

<a href="vehicle-details.php?vhid=<?php echo $car->id; ?>" class="btn btn-custom w-100">
View Details →
</a>

</div>

</div>
</div>

<?php endforeach; ?>
<?php else: ?>

<p class="text-center">No cars found.</p>

<?php endif; ?>

</div>

</div>

</body>
</html>