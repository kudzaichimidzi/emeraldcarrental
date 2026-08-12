<?php
session_start();
include('../includes/config.php');

if(empty($_SESSION['alogin'])){
    header("Location: index.php");
    exit();
}

/* PAGINATION (FROM B) */
$limit = 5;
$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;$start = ($page - 1) * $limit;

/* BOOKINGS */
$query = $dbh->prepare("
SELECT tblbooking.id, tblbooking.BookingNumber, tblbooking.email,
       tblbooking.VehicleId, tblbooking.FromDate, tblbooking.ToDate,
       tblbooking.Status, tblbooking.PostingDate,
       tblbooking.LastUpdationDate, tblbooking.PaymentMethod,
       tblvehicles.VehiclesTitle,
       tblbrands.BrandName,  -- ✅ ADD THIS
       tblvehicles.Vimage1
FROM tblbooking
LEFT JOIN tblvehicles ON tblvehicles.id = tblbooking.VehicleId
LEFT JOIN tblbrands ON tblbrands.id = tblvehicles.VehiclesBrand
ORDER BY tblbooking.id DESC
LIMIT :start, :limit
");

$query->bindParam(':start', $start, PDO::PARAM_INT);
$query->bindParam(':limit', $limit, PDO::PARAM_INT);
$query->execute();

$bookings = $query->fetchAll(PDO::FETCH_ASSOC);

/* TOTAL PAGES */
$totalQuery = $dbh->query("SELECT COUNT(*) FROM tblbooking");
$totalRecords = $totalQuery->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

/* STATS (FROM A + B COMBINED) */
$total = 0;
$approved = 0;
$pending = 0;
$rejected = 0;

$statsQuery = $dbh->query("
SELECT 
COUNT(*) as total,
SUM(Status=1) as approved,
SUM(Status=2) as rejected,
SUM(Status=0) as pending
FROM tblbooking
");

$stats = $statsQuery->fetch(PDO::FETCH_ASSOC);

$total = $stats['total'];
$approved = $stats['approved'];
$pending = $stats['pending'];
$rejected = $stats['rejected'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Booking</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/manage-booking.css">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body>

<div class="container mt-4">

<!-- HEADER (B + A COMBINED) -->
<div class="header">
    <div>
<h3 >🚗 Manage Bookings</h3>
        <small class="text-warning pulse">
            🔔 Pending: <span id="pendingBadge"><?php echo $pending; ?></span>
        </small>
    </div>

    <div class="d-flex gap-2 align-items-center">
        <!-- DASHBOARD BUTTON (FIXED) -->
        <a href="index.php" class="btn-dashboard btn-sm">
            ← Dashboard
        </a>

        <!-- BELL -->
        <div class="bell" onclick="toggleNotif()">
            🔔
            <span id="notifCount"><?php echo $pending; ?></span>
        </div>
    </div>
</div>

<!-- NOTIFICATION BOX (FROM B) -->
<div id="notifBox" style="display:none;background:#111827;padding:10px;border-radius:10px;color:white;">
    <small style="color:white;">Live Pending Bookings</small>
    <div id="notifList"></div>
</div>

<!-- STATS (FROM A ADDED) -->
<div class="row g-3 mt-3">
<div class="col-md-3">
  <div class="card p-3 text-center ">
    <h6>Total</h6>
    <h3><?php echo $total; ?></h3>
  </div>
</div>
    <div class="col-md-3"><div class="card p-3 text-center"><h6>Approved</h6><h3><?php echo $approved; ?></h3></div></div>
    <div class="col-md-3"><div class="card p-3 text-center"><h6>Pending</h6><h3><?php echo $pending; ?></h3></div></div>
    <div class="col-md-3"><div class="card p-3 text-center"><h6>Rejected</h6><h3><?php echo $rejected; ?></h3></div></div>
</div>

<!-- SEARCH (FROM B + A) -->
<input class="search-box mt-3" id="search" placeholder="Search booking / user / car..." onkeyup="searchTable()">

<!-- TABLE -->
<div class="card p-3 mt-3">

<table class="table" id="bookingTable">
<thead>
<tr>
<th>Car</th>
<th>User</th>
<th>From</th>
<th>To</th>
<th>Status</th>
<th>Action</th>
</tr>
</thead>

<tbody>

<?php foreach($bookings as $b){ ?>
<tr class="<?php echo ($b['Status']==0 ? 'new-booking' : ''); ?>">

<td>
<div class="d-flex align-items-center">
<img
src="../cars/<?php echo $b['Vimage1']; ?>" 
onerror="this.onerror=null;this.src='https://cdn.pixabay.com/photo/2012/05/29/00/43/car-49278_1280.jpg';"
class="car-img me-2">

<div>
<strong><?php echo $b['VehiclesTitle']; ?></strong><br>
<small><?php echo $b['BrandName']; ?></small>
</div>
</div>
</td>

<td><?php echo $b['email']; ?></td>
<td><?php echo $b['FromDate']; ?></td>
<td><?php echo $b['ToDate']; ?></td>

<td>
<?php if($b['Status']==1){ ?>
  <span class="badge bg-success">✔ Approved</span>
<?php } elseif($b['Status']==2){ ?>
  <span class="badge bg-danger">✖ Cancelled</span>
<?php } else { ?>
  <span class="badge bg-warning text-dark">⏳ Pending</span>
<?php } ?>

</td>

<td>

<!-- VIEW (FROM B MODAL) -->
 <a href="booking-details.php?bid=<?php echo $b['id']; ?>" 
   class="btn btn-info btn-sm">
   View Details
</a>

<?php if($b['Status']==1){ ?>
<a href="../users/receipt_pdf.php?id=<?php echo $b['id']; ?>" 
   target="_blank"
   class="btn btn-success btn-sm">
   📄 Receipt
</a>
<?php } ?>


<?php if($b['Status']==0){ ?>
<button class="btn btn-success btn-sm" onclick="updateStatus(<?php echo $b['id']; ?>,1)">✔</button>
<button class="btn btn-danger btn-sm" onclick="updateStatus(<?php echo $b['id']; ?>,2)">✖</button>
<?php } else { ?>
<span class="text-muted">Done</span>
<?php } ?>

</td>

</tr>

<?php } ?>

</tbody>
</table>
</div>

<!-- PAGINATION (FROM B) -->
<div class="mt-3">
<?php for($i=1;$i<=$totalPages;$i++){ ?>
<a class="btn btn-sm btn-secondary" href="?page=<?php echo $i; ?>">
<?php echo $i; ?>
</a>
<?php } ?>
</div>

</div>

<!-- MODAL (FROM B) -->
<div class="modal fade" id="bookingModal">
<div class="modal-dialog">
<div class="modal-content p-3">
<h5>Booking Details</h5>
<div id="modalBody"></div>
</div>
</div>
</div>



<script src="../js/manage-booking.js"></script>

</body>
</html>