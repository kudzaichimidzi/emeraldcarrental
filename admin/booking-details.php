<?php
session_start();
include('../includes/config.php');

if(strlen($_SESSION['alogin'])==0){
    header('location:index.php');
    exit();
}

/* GET BOOKING ID */
$bid = 0;
if(!empty($_GET['bid']) && ctype_digit($_GET['bid'])){
    $bid = (int) $_GET['bid'];
} elseif(!empty($_GET['id']) && ctype_digit($_GET['id'])){
    // fallback if some links use `id` instead of `bid`
    $bid = (int) $_GET['id'];
}

if($bid <= 0){
    echo '<!DOCTYPE html><html><head><title>Invalid booking ID</title>';
    echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">';
    echo '<style>body{background:#0f172a;color:white;font-family:Segoe UI;} .card{background:rgba(255,255,255,0.06);border:none;border-radius:12px;} .card h3,.card p{color:white;font-weight:700;}</style>';
    echo '</head><body><div class="container mt-4"><div class="card p-4"><h3>Invalid booking ID</h3><p>Please open this page from the booking list.</p><a href="manage-booking.php" class="btn btn-primary">Back to bookings</a></div></div></body></html>';
    exit;
}

/* HANDLE ACTION */
if(isset($_GET['action'])){
    $action = $_GET['action'];

    if($action == "confirm"){
        $sql = "UPDATE tblbooking SET Status=1, LastUpdationDate=NOW() WHERE id=:id";
    } elseif($action == "cancel"){
        $sql = "UPDATE tblbooking SET Status=2, LastUpdationDate=NOW() WHERE id=:id";
    }

    if(isset($sql)){
        $q = $dbh->prepare($sql);
        $q->bindParam(':id',$bid,PDO::PARAM_INT);
        $q->execute();

        header("Location: manage-booking.php");
        exit();
    }
}

/* FETCH BOOKING */
$sql = "SELECT 
    tblusers.FullName,
    tblusers.EmailId AS UserEmail,
    tblbrands.BrandName,
    tblvehicles.VehiclesTitle,
    tblvehicles.PricePerDay,
    tblvehicles.FuelType,
    tblvehicles.ModelYear,
    tblvehicles.SeatingCapacity,
    tblvehicles.Vimage1,
    tblvehicles.Vimage2,
    tblvehicles.Vimage3,

    tblbooking.VehicleId,
    tblbooking.FromDate,
    tblbooking.ToDate,
    tblbooking.Status,
    tblbooking.id,
    tblbooking.BookingNumber,
    tblbooking.PostingDate,
    tblbooking.LastUpdationDate,
    tblbooking.name AS BookingName,
    tblbooking.phone AS BookingPhone,
    tblbooking.Email AS BookingEmail,
    tblbooking.PickupLocation,
    tblbooking.DropoffLocation,
    tblbooking.PickupTime,
    tblbooking.DropoffTime,
    tblbooking.PaymentMethod
FROM tblbooking
LEFT JOIN tblvehicles ON tblvehicles.id = tblbooking.VehicleId
LEFT JOIN tblusers ON tblusers.EmailId = tblbooking.Email
LEFT JOIN tblbrands ON tblvehicles.VehiclesBrand = tblbrands.id
WHERE tblbooking.id = :bid";



$query = $dbh->prepare($sql);
$query->bindParam(':bid',$bid,PDO::PARAM_INT);
$query->execute();

$result = $query->fetch(PDO::FETCH_OBJ);

if(!$result){
    die("Booking not found");
}


$start = new DateTime($result->FromDate);
$end = new DateTime($result->ToDate);
$days = $start->diff($end)->days;

// prevent 0 days issue
if($days == 0){
    $days = 1;
}

$total = $days * $result->PricePerDay;

$start = strtotime($result->FromDate);
$end = strtotime($result->ToDate);

$days = ($end - $start) / 86400;
if($days <= 0) $days = 1;

/* faster booking = longer stay = slower animation */
$speed = max(2, 6 - $days); 

?>
<!DOCTYPE html>
<html>
<head>
<title>Booking Details</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/booking-details.css">
</head>
<body>

<div class="container mt-4">

<!-- HEADER -->


<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>🚗 Booking Details</h3>
    <a href="manage-booking.php" class="btn btn-outline-light btn-sm">← Back</a>
</div>

<div class="row g-3">

<!-- LEFT SIDE (USER + BOOKING INFO) -->
<div class="col-md-6">

    <!-- USER CARD -->
    <div class="card p-3 mb-3">
        <h5><i class="fa fa-user"></i> User Information</h5>
        <hr>

        <p><i class="fa fa-user text-info"></i>
        <p><b>Name:</b> <?php echo $result->BookingName ?: $result->FullName; ?></p>

        
<i class="fa fa-envelope text-warning"></i>

<p><b>Email:</b>
<a href="mailto:<?php echo $result->BookingEmail ?: $result->UserEmail; ?>">
<i class="fa fa-paper-plane"></i>
<?php echo $result->BookingEmail ?: $result->UserEmail; ?>
</a>
</p>


<a href="mailto:<?php echo $result->BookingEmail ?: $result->UserEmail; ?>" class="btn btn-mail btn-sm mt-2">
✉ Send Email
</a>


<a href="tel:<?php echo $result->phone; ?>" class="btn btn-success btn-sm mt-2">
📞 Call User
</a>
    </div>

   

    <div class="ticket text-center">
    <h4>🎫 BOOKING #<?php echo $result->BookingNumber; ?></h4>

    <div class="timeline">
        <div class="step <?php echo ($result->Status==0?'active':''); ?>">Pending</div>
        <div class="step <?php echo ($result->Status==1?'active':''); ?>">Confirmed</div>
        <div class="step <?php echo ($result->Status==2?'active':''); ?>">Cancelled</div>
    </div>
</div>

    <!-- BOOKING INFO -->
    <div class="card p-3">
        <h5><i class="fa fa-calendar"></i> Booking Info</h5>
        <hr>

        <p><b>Booking No:</b> #<?php echo $result->BookingNumber; ?></p>

        <p><b>From:</b> 📅 <?php echo $result->FromDate; ?></p>
        <p><b>To:</b> 📅 <?php echo $result->ToDate; ?></p>

        <!-- ✅ ADD HERE -->
        <p><b>📞 Phone:</b> <?php echo $result->BookingPhone; ?></p>        <p><b>📍 Pickup:</b> <?php echo $result->PickupLocation; ?></p>
        <p><b>📍 Dropoff:</b> <?php echo $result->DropoffLocation; ?></p>
        <p><b>⏰ Pickup Time:</b> <?php echo $result->PickupTime; ?></p>
        <p><b>⏰ Dropoff Time:</b> <?php echo $result->DropoffTime; ?></p>
        <p><b>💳 Payment:</b> <?php echo $result->PaymentMethod; ?></p>



        <p>
        <b>Status:</b>
        <?php if($result->Status==0){ ?>
            <span class="badge bg-warning text-dark">Pending</span>
        <?php } elseif($result->Status==1){ ?>
            <span class="badge bg-success">Confirmed</span>
        <?php } else { ?>
            <span class="badge bg-danger">Cancelled</span>
        <?php } ?>
        </p>
    </div>

</div>

<!-- RIGHT SIDE (CAR INFO) -->
<div class="col-md-6">

    <div class="card p-3">

        <h5><i class="fa fa-car"></i> Vehicle Information</h5>
        <hr>

        <!-- CAR IMAGE (optional static or DB image later) -->
    <div style="
    position:absolute;
    bottom:10px;
    left:10px;
    right:10px;
    text-align:center;
    background:rgba(0,0,0,0.6);
    padding:8px;
    border-radius:10px;
    font-weight:bold;
    color:#fff;
    backdrop-filter:blur(5px);
">
    🚗 <?php echo $result->BrandName . " " . $result->VehiclesTitle; ?>
</div>

    <!-- <img src="../admin/vehicleimages/<?php echo $result->Vimage1; ?>" class="car-img">-->
<div id="carCarousel" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="../cars/<?php echo $result->Vimage1; ?>" class="d-block w-100">
    </div>
    <?php if(!empty($result->Vimage2)){ ?>
    <div class="carousel-item">
      <img src="../cars/<?php echo $result->Vimage2; ?>" class="d-block w-100">
    </div>
    <?php } ?>
    <?php if(!empty($result->Vimage3)){ ?>
    <div class="carousel-item">
      <img src="../cars/<?php echo $result->Vimage3; ?>" class="d-block w-100">
    </div>
    <?php } ?>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>
</div>

</div>

<div class="price-box">
💰 $<?php echo $result->PricePerDay; ?> / Day<br>

🔥 Total: $<?php echo $total; ?> 
<br>
<small>(<?php echo $days; ?> day(s))</small>

<div class="icon-row">
<span>⛽ <?php echo $result->FuelType; ?></span>
<span>📅 <?php echo $result->ModelYear; ?></span>
<span>👥 <?php echo $result->SeatingCapacity; ?></span>
</div>
</div>
</div>




<!-- ACTION BUTTONS -->
<div class="mt-3">

<?php if($result->Status==0){ ?>

<a href="booking-details.php?action=confirm&bid=<?php echo $result->id; ?>"
   class="btn btn-success">
   ✔ Confirm Booking
</a>

<a href="booking-details.php?action=cancel&bid=<?php echo $result->id; ?>"
   class="btn btn-danger">
   ✖ Cancel Booking
</a>

<?php } ?>

<?php if($result->Status==1){ ?>

<a href="../users/receipt_pdf.php?id=<?php echo $result->id; ?>" 
   target="_blank"
   class="btn btn-primary">
   📄 View Receipt
</a>

<a href="../users/send_receipt.php?id=<?php echo $result->id; ?>" 
   class="btn btn-warning">
   📧 Email Receipt
</a>


<?php } ?>

</div>

</div>
<script src="../js/booking-details.js"></script>

</body>
</html>
