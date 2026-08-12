<?php
session_start();
include('../includes/config.php');

if (empty($_SESSION['alogin'])) {
    header('location:index.php');
    exit();
}

$msg = $error = "";

// Handle add vehicle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_vehicle'])) {
    try {
        $sql = "INSERT INTO tblvehicles 
                (VehiclesTitle, VehiclesBrand, PricePerDay, FuelType, ModelYear, SeatingCapacity, Vimage1) 
                VALUES (:title, :brand, :price, :fuel, :year, :seats, :image)";
        $query = $dbh->prepare($sql);
        $query->execute([
            ':title' => $_POST['title'],
            ':brand' => $_POST['brand'],
            ':price' => $_POST['price'],
            ':fuel'  => $_POST['fuel'],
            ':year'  => $_POST['year'],
            ':seats' => $_POST['seats'],
            ':image' => $_POST['image'] // for simplicity, just store filename
        ]);
        $msg = "✅ Vehicle added successfully.";
    } catch (Exception $e) {
        $error = "⚠️ Error adding vehicle.";
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $dbh->prepare("DELETE FROM tblvehicles WHERE id=:id")->execute([':id'=>$id]);
    $msg = "🗑️ Vehicle deleted.";
}

// Handle edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_vehicle'])) {
    $id = (int)$_POST['id'];
    $sql = "UPDATE tblvehicles SET VehiclesTitle=:title, VehiclesBrand=:brand, PricePerDay=:price,
            FuelType=:fuel, ModelYear=:year, SeatingCapacity=:seats WHERE id=:id";
    $query = $dbh->prepare($sql);
    $query->execute([
        ':title' => $_POST['title'],
        ':brand' => $_POST['brand'],
        ':price' => $_POST['price'],
        ':fuel'  => $_POST['fuel'],
        ':year'  => $_POST['year'],
        ':seats' => $_POST['seats'],
        ':id'    => $id
    ]);
    $msg = "✏️ Vehicle updated.";
}

// Fetch vehicles
$vehicles = $dbh->query("SELECT v.*, b.BrandName 
                         FROM tblvehicles v 
                         JOIN tblbrands b ON b.id=v.VehiclesBrand 
                         ORDER BY v.id DESC")->fetchAll(PDO::FETCH_OBJ);

// Fetch brands for dropdown
$brands = $dbh->query("SELECT * FROM tblbrands ORDER BY BrandName ASC")->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Vehicles | Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    .car-img { max-width: 120px; border-radius: 6px; }
  </style>
</head>
<body class="bg-light">

<?php include('../includes/header.php'); ?>
<div class="container py-5">
  <h2 class="mb-4">Manage Vehicles</h2>

  <?php if($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php elseif($msg): ?>
    <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <!-- Add Vehicle Form -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header">Add New Vehicle</div>
    <div class="card-body">
      <form method="post" class="row g-3">
        <div class="col-md-4"><input type="text" name="title" class="form-control" placeholder="Vehicle Title" required></div>
        <div class="col-md-3">
          <select name="brand" class="form-select" required>
            <option value="">Select Brand</option>
            <?php foreach($brands as $b): ?>
              <option value="<?= $b->id ?>"><?= htmlspecialchars($b->BrandName) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-2"><input type="number" name="price" class="form-control" placeholder="Price/Day" required></div>
        <div class="col-md-3">
          <select name="fuel" class="form-select" required>
            <option value="">Fuel Type</option>
            <option>Petrol</option><option>Diesel</option><option>CNG</option>
          </select>
        </div>
        <div class="col-md-2"><input type="number" name="year" class="form-control" placeholder="Model Year" required></div>
        <div class="col-md-2"><input type="number" name="seats" class="form-control" placeholder="Seats" required></div>
        <div class="col-md-3"><input type="text" name="image" class="form-control" placeholder="Image filename"></div>
        <div class="col-md-2">
          <button type="submit" name="add_vehicle" class="btn btn-primary w-100">Add Vehicle</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Search Bar -->
  <div class="mb-3">
    <input type="text" id="searchInput" class="form-control" placeholder="Search vehicles...">
  </div>

  <!-- Vehicles Table -->
  <div class="card shadow-sm">
    <div class="card-header">Existing Vehicles</div>
    <div class="card-body">
      <table class="table table-striped align-middle" id="vehicleTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Title</th>
            <th>Brand</th>
            <th>Price/Day</th>
            <th>Fuel</th>
            <th>Year</th>
            <th>Seats</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($vehicles as $v): ?>
            <tr>
              <td><?= $v->id ?></td>

              <td>
              <img src="../cars/<?= $v->Vimage1 ?>" class="car-img">
              </td>

             <!-- <td><img src="img/vehicleimages/<?= htmlspecialchars($v->Vimage1) ?>" class="car-img"></td> -->
              <td><?= htmlspecialchars($v->VehiclesTitle) ?></td>
              <td><?= htmlspecialchars($v->BrandName) ?></td>
              <td>$<?= $v->PricePerDay ?></td>
              <td><?= htmlspecialchars($v->FuelType) ?></td>
              <td><?= $v->ModelYear ?></td>
              <td><?= $v->SeatingCapacity ?></td>
              <td>
                <!-- Edit form inline -->
                <form method="post" class="d-inline">
                  <input type="hidden" name="id" value="<?= $v->id ?>">
                  <input type="hidden" name="title" value="<?= htmlspecialchars($v->VehiclesTitle) ?>">
                  <input type="hidden" name="brand" value="<?= $v->VehiclesBrand ?>">
                  <input type="hidden" name="price" value="<?= $v->PricePerDay ?>">
                  <input type="hidden" name="fuel" value="<?= htmlspecialchars($v->FuelType) ?>">
                  <input type="hidden" name="year" value="<?= $v->ModelYear ?>">
                  <input type="hidden" name="seats" value="<?= $v->SeatingCapacity ?>">
                  <button type="submit" name="edit_vehicle" class="btn btn-sm btn-warning">Edit</button>
                </form>
                <a href="?delete=<?= $v->id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this vehicle?')">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Simple search filter
document.getElementById('searchInput').addEventListener('keyup', function() {
  let filter = this.value.toLowerCase();
  document.querySelectorAll('#vehicleTable tbody tr').forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
  });
});
</script>

</body>
</html>
