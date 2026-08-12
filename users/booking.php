<?php

include('../includes/config.php');

$selectedCar = "";

if(isset($_GET['car'])){

    $selectedCar = $_GET['car'];

}


$availableCars = [];


if(!empty($selectedCar)){

    $sql = "
    SELECT v.*, b.BrandName
    FROM tblvehicles v
    JOIN tblbrands b
    ON v.VehiclesBrand = b.id
    WHERE v.id = :id
    ";

    $query = $dbh->prepare($sql);

    $query->bindParam(':id',$selectedCar);

    $query->execute();

    $availableCars = $query->fetchAll(PDO::FETCH_OBJ);

}

if(isset($_POST['check']) && empty($selectedCar)){
        $from = $_POST['fromdate'];
    $to = $_POST['todate'];

$sql = "SELECT v.*, b.BrandName
        FROM tblvehicles v
        LEFT JOIN tblbrands b 
        ON v.VehiclesBrand = b.id
        WHERE v.id NOT IN (
            SELECT VehicleId 
            FROM tblbooking
            WHERE Status IN (0,1)
            AND (FromDate <= :to AND ToDate >= :from)
        )
        AND v.status = 1";
 
    $query = $dbh->prepare($sql);
    $query->bindParam(':from', $from);
    $query->bindParam(':to', $to);
    $query->execute();

    $availableCars = $query->fetchAll(PDO::FETCH_OBJ);
}

$carName = "";
if(isset($_GET['car'])){
    $carName = $_GET['car'];
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Car Booking</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
 <link rel="stylesheet" href="../css/booking.css">

<style>

</style>
</head>

<body>

<div class="floating-icons">
<i class="bi bi-shield-lock"></i>
<i class="bi bi-key-fill"></i>
<i class="bi bi-person-circle"></i>
<i class="bi bi-car-front"></i>
<i class="bi bi-check-circle"></i>
</div> 

<div class="box">

<h2><i class="fa-solid fa-car"></i> EMERALD BOOKING FORM</h2>


<form method="POST" id="bookingForm" autocomplete="off">

  <!-- FULL NAME + DOB -->
  <div class="row mb-3">
    <div class="col-md-6">
      <label>Full Name</label>
      <div class="input-group">
        <div class="input-icon"><i class="fa fa-user"></i></div>
        <input type="text" name="fullname" placeholder="Enter Full Name" required    value="<?= isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : '' ?>">
      </div>
    </div>
    <div class="col-md-6">
      <label>DOB</label>
      <div class="input-group">
        <div class="input-icon"><i class="fa fa-cake-candles"></i></div>
        <input type="date" name="dob" required  value="<?= isset($_POST['dob']) ? htmlspecialchars($_POST['dob']) : '' ?>">
      </div>
    </div>
  </div>

  <!-- PHONE + EMAIL -->
  <div class="row mb-3">
    <div class="col-md-6">
      <label>Phone</label>
      <div class="input-group">
        <div class="input-icon"><i class="fa fa-phone"></i></div>
        <input type="text" name="phone" placeholder="Phone Number" required  value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>">
      </div>
    </div>
    <div class="col-md-6">
      <label>Email</label>
      <div class="input-group">
        <div class="input-icon"><i class="fa fa-envelope"></i></div>
        <input type="email" name="email" placeholder="Email" required  value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
      </div>
    </div>
  </div>

  <!-- PICKUP + DROPOFF -->
  <div class="row mb-3">
    <div class="col-md-6">
      <label>Pickup Location</label>
      <div class="input-group">
        <div class="input-icon"><i class="fa fa-location-dot"></i></div>
        <input type="text" name="pickup" placeholder="Enter Pickup Location" required  value="<?= isset($_POST['pickup']) ? htmlspecialchars($_POST['pickup']) : '' ?>">
      </div>
    </div>
    <div class="col-md-6">
      <label>Dropoff Location</label>
      <div class="input-group">
        <div class="input-icon"><i class="fa fa-location-dot"></i></div>
        <input type="text" name="dropoff" placeholder="Enter Dropoff Location" required  value="<?= isset($_POST['dropoff']) ? htmlspecialchars($_POST['dropoff']) : '' ?>">
      </div>
    </div>
  </div>

  <!-- FROM DATE + TO DATE -->
  <div class="row mb-3">
    <div class="col-md-6">
      <label>From Date</label>
      <div class="input-group">
        <div class="input-icon"><i class="fa fa-calendar"></i></div>
        <input type="date" name="fromdate" required  value="<?= isset($_POST['fromdate']) ? htmlspecialchars($_POST['fromdate']) : '' ?>">
      </div>
    </div>
    <div class="col-md-6">
      <label>To Date</label>
      <div class="input-group">
        <div class="input-icon"><i class="fa fa-calendar-check"></i></div>
        <input type="date" name="todate" required  value="<?= isset($_POST['todate']) ? htmlspecialchars($_POST['todate']) : '' ?>">
      </div>
    </div>
  </div>

  <!-- PICKUP TIME + DROPOFF TIME -->
  <div class="row mb-3">
    <div class="col-md-6">
      <label>Pickup Time</label>
      <div class="input-group">
        <div class="input-icon"><i class="fa fa-clock"></i></div>
        <input type="time" name="pickuptime" required  value="<?= isset($_POST['pickuptime']) ? htmlspecialchars($_POST['pickuptime']) : '' ?>">
            </div>
    </div>
    <div class="col-md-6">
      <label>Dropoff Time</label>
      <div class="input-group">
        <div class="input-icon"><i class="fa fa-clock"></i></div>
        <input type="time" name="dropofftime" required  value="<?= isset($_POST['dropofftime']) ? htmlspecialchars($_POST['dropofftime']) : '' ?>">
      </div>
    </div>
  </div>

  <!-- CAR + PAYMENT -->
  <div class="row mb-3">
    <div class="col-md-6">
      <label>Car Selected</label>
      <div class="input-group">
        <div class="input-icon"><i class="fa fa-car"></i></div>
            
<?php if(!empty($availableCars)): ?>

<select name="car" id="car" disabled>

<?php foreach($availableCars as $car): ?>

<option 
value="<?= $car->id ?>"
data-price="<?= $car->PricePerDay ?>"
selected
>

<?= $car->BrandName ?> |
<?= $car->VehiclesTitle ?> |
<?= $car->VehicleType ?> |
<?= $car->SeatingCapacity ?> Seats |
$<?= $car->PricePerDay ?>/day

</option>

<?php endforeach; ?>

</select>

<input type="hidden" name="car" value="<?= $selectedCar ?>">

<?php else: ?>

<p style="color:#ccc;">
ℹ️ Select dates and click "Check Available Cars" to see options here.
</p>

<?php endif; ?>

      </div>
    </div>
    <div class="col-md-6">
      <label>Payment Method</label>
      <div class="input-group">
        <div class="input-icon"><i class="fa fa-credit-card"></i></div>
        <select name="paymentmethod"  required>
          <option value="">Select Payment Method</option>
          <option value="Cash" <?= (isset($_POST['paymentmethod']) && $_POST['paymentmethod'] == 'Cash') ? 'selected' : '' ?>>Cash</option>
          <option value="Card" <?= (isset($_POST['paymentmethod']) && $_POST['paymentmethod'] == 'Card') ? 'selected' : '' ?>>Card</option>

        </select>
      </div>
    </div>
  </div>

  <!-- LICENSE + COUNTRY -->
  <div class="row mb-3">
    <div class="col-md-6">
      <label>License Number</label>
      <div class="input-group">
        <div class="input-icon"><i class="fa fa-id-card"></i></div>
        <input type="text" name="licenseNumber"  required  value="<?= isset($_POST['licenseNumber']) ? htmlspecialchars($_POST['licenseNumber']) : '' ?>">
      </div>
    </div>
    <div class="col-md-6">
      <label>Country of Issuance</label>
      <div class="input-group">
        <div class="input-icon"><i class="fa fa-globe"></i></div>
        <input type="text" name="countryOfIssuance"  required  value="<?= isset($_POST['countryOfIssuance']) ? htmlspecialchars($_POST['countryOfIssuance']) : '' ?>">
      </div>
    </div>
  </div>

  <!-- TOTAL -->
  <div class="mb-3 text-center">
    <h5>Total Price: $<span id="totalPrice">0</span></h5>
  </div>

  <input type="hidden" name="totalprice" id="totalInput">

<button class="btn" type="submit" name="check" value="1">
    🔍 Check Available Cars
</button>


<button class="btn" type="submit" name="action" value="book" formaction="insert_booking.php">
      <i class="fa fa-paper-plane"></i> Proceed Booking
</button>

</form>
</div>


</body>
<script src="../js/booking.js"></script>
</html>