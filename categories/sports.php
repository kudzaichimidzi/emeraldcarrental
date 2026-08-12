<?php
session_start();
include('../includes/config.php');

$sql = "SELECT VehiclesTitle, Vimage1, PricePerDay, id FROM tblvehicles WHERE VehicleType='Sports'";
$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Sports Car Rentals - Emerald Cars</title></head>
<body>
<div class="container sports-section">
  <h1>Sports Car Rentals</h1>
  <p>"Speed, style, and adrenaline."</p>
  <div class="row">
    <?php foreach($results as $car) { ?>
      <div class="col-md-4">
        <div class="sports-card">
          <img src="../admin/vehicleimages/<?php echo htmlentities($car->Vimage1); ?>" alt="<?php echo htmlentities($car->VehiclesTitle); ?>">
          <h3><?php echo htmlentities($car->VehiclesTitle); ?></h3>
          <p>₹<?php echo htmlentities($car->PricePerDay); ?> / day</p>
          <a href="../vehical-details.php?id=<?php echo htmlentities($car->id); ?>" class="btn-book">View Details</a>
        </div>
      </div>
    <?php } ?>
  </div>
</div>
</body>
</html>
