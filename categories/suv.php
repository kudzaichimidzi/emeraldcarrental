<?php
session_start();
include('../includes/config.php'); // adjust path if needed

$sql = "SELECT VehiclesTitle, Vimage1, PricePerDay, id FROM tblvehicles WHERE VehicleType='SUV'";
$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SUV Rentals - Emerald Cars</title>
  <link rel="stylesheet" href="../css/homepage.css">
</head>
<body>
<div class="container suv-section">
  <h1>SUV Rentals</h1>
  <p>"Power, space, and comfort for every journey."</p>

  <div class="row">
    <?php foreach($results as $car) { ?>
      <div class="col-md-4">
        <div class="suv-card">
          <img src="../admin/vehicleimages/<?php echo htmlentities($car->Vimage1); ?>" 
               alt="<?php echo htmlentities($car->VehiclesTitle); ?>" class="suv-img">
          <h3><?php echo htmlentities($car->VehiclesTitle); ?></h3>
          <p>₹<?php echo htmlentities($car->PricePerDay); ?> / day</p>
          <a href="../vehical-details.php?id=<?php echo htmlentities($car->id); ?>" class="btn-book">View Details</a>
        </div>
      </div>
    <?php } ?>
  </div>

  <a href="../index.php" class="btn-book mt-4">← Back to Home</a>
</div>
</body>
</html>
