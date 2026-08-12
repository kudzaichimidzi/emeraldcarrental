<?php
session_start();
include('../includes/config.php');

if(empty($_SESSION['alogin'])){
   header("Location: index.php");
    exit();
}


/* ===== MONTHLY EARNINGS DATA ===== */

$monthlyData = array_fill(1, 12, 0);

$query = $dbh->query("
  SELECT 
    MONTH(b.PostingDate) as month,
SUM(GREATEST(DATEDIFF(b.ToDate, b.FromDate), 1) * v.PricePerDay) as total
  FROM tblbooking b
  LEFT JOIN tblvehicles v ON v.id = b.VehicleId
  WHERE b.Status=1
  GROUP BY MONTH(b.PostingDate)
");

while($row = $query->fetch(PDO::FETCH_ASSOC)){
$monthlyData[(int)$row['month']] = (int)($row['total'] ?? 0);}


/* ===== DASHBOARD DATA ===== */

// TOTAL BOOKINGS
$totalBookings = $dbh->query("SELECT COUNT(*) FROM tblbooking")->fetchColumn();

// RENTED CARS (approved bookings)
$rentedCars = $dbh->query("SELECT COUNT(*) FROM tblbooking WHERE Status=1")->fetchColumn();

// AVAILABLE CARS (simple logic: total vehicles - rented)
// If you DON'T have vehicles table yet, we fake total vehicles = 100
$totalVehicles = $dbh->query("SELECT COUNT(*) FROM tblvehicles")->fetchColumn();

$availableCarsQuery = $dbh->query("
SELECT COUNT(*) FROM tblvehicles v
WHERE v.id NOT IN (
    SELECT b.VehicleId 
    FROM tblbooking b
    WHERE b.Status = 1
    AND CURDATE() BETWEEN b.FromDate AND b.ToDate
)
");

$availableCars = $availableCarsQuery->fetchColumn();

// REVENUE (100 per day logic)
// REVENUE (replace this whole section)

$revenueQuery = $dbh->query("
SELECT SUM(GREATEST(DATEDIFF(b.ToDate, b.FromDate), 1) * v.PricePerDay) AS total
FROM tblbooking b
LEFT JOIN tblvehicles v ON v.id = b.VehicleId
WHERE b.Status = 1
");

$revenue = $revenueQuery->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 <link rel="stylesheet" href="css/dashboard.css">

<style>


</style>
</head>

<body>

<div class="d-flex">

<!-- SIDEBAR -->
<div id="sidebar" class="sidebar bg-dark text-white p-3" style="width:220px;">
  <h4>Menu</h4>

  <ul class="nav flex-column">

    <li class="nav-item">
      <a href="index.php" class="nav-link text-white active">
        <i class="bi bi-speedometer2 me-2"></i> <span>Dashboard</span>
      </a>
    </li>

    <li class="nav-item">
      <a href="manage-booking.php" class="nav-link text-white">
        <i class="bi bi-journal-text me-2"></i> <span>Bookings</span>
      </a>
    </li>

    <li class="nav-item">
      <a href="#" class="nav-link text-white">
        <i class="bi bi-car-front-fill me-2"></i> <span>Vehicles</span>
      </a>
    </li>

    <li class="nav-item">
      <a href="#" class="nav-link text-white">
        <i class="bi bi-calendar-event me-2"></i> <span>Calendar</span>
      </a>
    </li>

    <li class="nav-item">
      <a href="#" class="nav-link text-white">
        <i class="bi bi-people-fill me-2"></i> <span>Clients</span>
      </a>
    </li>

    <li class="nav-item">
      <a href="#" class="nav-link text-white">
        <i class="bi bi-person-badge-fill me-2"></i> <span>Drivers</span>
      </a>
    </li>

    <li class="nav-item">
      <a href="#" class="nav-link text-white">
        <i class="bi bi-cash-stack me-2"></i> <span>Financials</span>
      </a>
    </li>

    <li class="nav-item">
      <a href="#" class="nav-link text-white">
        <i class="bi bi-geo-alt-fill me-2"></i> <span>Tracking</span>
      </a>
    </li>

    <li class="nav-item">
      <a href="#" class="nav-link text-white">
        <i class="bi bi-chat-dots-fill me-2"></i> <span>Messages</span>
      </a>
    </li>

    <li class="nav-item">
      <a href="#" class="nav-link text-white">
        <i class="bi bi-gear-fill me-2"></i> <span>Settings</span>
      </a>
    </li>

    <li class="nav-item">
      <a href="logout.php" class="nav-link text-danger">
        <i class="bi bi-box-arrow-right me-2"></i> <span>Logout</span>
      </a>
    </li>

  </ul>
</div>

<!-- MAIN CONTENT -->
<div class="flex-grow-1 p-4">

<!-- TOP BAR -->
<div class="topbar d-flex align-items-center mb-4">
    <i class="bi bi-list fs-3 me-3" onclick="toggleSidebar()" style="cursor:pointer;"></i>
    <div class="ms-auto d-flex align-items-center">

  <!-- DARK MODE BUTTON -->
  <button onclick="toggleDarkMode()" class="btn btn-dark me-2">
    <i class="bi bi-moon"></i>
  </button>

  <!-- SEARCH -->
<input type="text" class="form-control me-3" placeholder="Search..." style="width:200px;">

<!-- NOTIFICATION -->
<i class="bi bi-bell fs-5 me-3 position-relative">
<span id="notifCount" class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill">
  0
</span>
</i>

<!-- ADMIN NAME -->
<span class="fw-bold">
  Admin: <?php echo $_SESSION['alogin']; ?>
</span>

</div> <!-- END RIGHT SIDE -->
</div> <!-- END TOPBAR -->

<!-- DATE -->
<div class="mb-3 text-end">
  <strong>
    <?php
      echo date("M 01, Y") . " - " . date("M t, Y");
    ?>
  </strong>
</div>

<!-- TITLE -->
<h2>Dashboard</h2>
<p>Welcome to Emerald Car Rental System</p>

<!-- CARDS -->
<div class="row">

  <div class="col-md-3">
  <div class="card shadow text-center p-3">
    <h6>Total Revenue</h6>
   <h4 id="revenue">$<?php echo $revenue; ?></h4>

  </div>
</div>

<div class="col-md-3">
  <div class="card shadow text-center p-3">
    <h6>Bookings</h6>
   <h4 id="bookings"><?php echo $totalBookings; ?></h4>
  </div>
</div>

<div class="col-md-3">
  <div class="card shadow text-center p-3">
    <h6>Rented Cars</h6>
    <h4 id="rented"><?php echo $rentedCars; ?></h4>
  </div>
</div>

<div class="col-md-3">
  <div class="card shadow text-center p-3">
    <h6>Available Cars</h6>
    <h4 id="available"><?php echo $availableCars; ?></h4>
  </div>

</div>
</div>

<!-- CHARTS -->
<div class="row mt-4">

  <div class="col-md-6">
  <div class="card shadow p-3">
    <h5>Weekly Bookings</h5>
    <div style="height:250px;">
      <canvas id="chart1"></canvas>
    </div>
  </div>
</div>

<div class="col-md-6">
  <div class="card shadow p-3">
    <h5>Booking Distribution</h5>
    <div style="height:250px;">
      <canvas id="chart2"></canvas>
    </div>
  </div>
</div>
</div> <!-- ✅ VERY IMPORTANT: CLOSE ROW -->

<div class="row mt-4">
  <div class="col-md-12">
    <div class="card shadow p-3">
      <h5>Earnings Summary</h5>
      <div style="height:300px;">
        <canvas id="earningsSummary"></canvas>
      </div>
    </div>
  </div>
</div>


<?php
$logs = $dbh->query("SELECT * FROM admin_logs ORDER BY id DESC LIMIT 5");
?>

<div class="card shadow mt-4 p-3">
  <h5>Recent Activity</h5>
  <ul>
    <?php while($log = $logs->fetch(PDO::FETCH_ASSOC)){ ?>
      <li><?php echo $log['action']; ?> - <?php echo $log['created_at']; ?></li>
    <?php } ?>
  </ul>
</div>

<!-- BOOKINGS + CAR TYPES -->
<div class="row mt-4">

  <!-- BOOKINGS TABLE -->
   <a href="export.php" class="btn btn-success mb-2">
  <i class="bi bi-download"></i> Export Excel
</a>

  <div class="col-12">
    <div class="card shadow-lg border-0">
      <div class="card-body">
        <h4 class="card-title">Recent Bookings</h4>
       <!-- SEARCH + FILTER -->
<div class="d-flex justify-content-between mb-3 flex-wrap gap-2">

  <!-- SEARCH -->
  <div class="input-group" style="max-width:250px;">
    <span class="input-group-text">
      <i class="bi bi-search"></i>
    </span>
    <input type="text" id="searchInput" class="form-control" placeholder="Search client name, car etc">
  </div>






  <!-- STATUS FILTER -->
  <select id="statusFilter" class="form-select" style="max-width:180px;">
    <option value="">All Status</option>
    <option value="approved">Approved</option>
    <option value="pending">Pending</option>
  </select>

  <!-- DATE FILTER -->
  <input type="date" id="dateFilter" class="form-control" style="max-width:180px;">

</div>

        <table class="table table-striped">
          <thead>
            <tr>
              <th>ID</th>
              <th>Date</th>
              <th>Client</th>
              <th>Car</th>
              <th>Days</th>
              <th>Return</th>
              <th>Payment</th>
              <th>Status</th>
            </tr>
          </thead>

          <tbody>
          <?php
          $query = $dbh->query("
SELECT tblbooking.*, tblvehicles.VehiclesTitle,tblvehicles.PricePerDay
FROM tblbooking 
LEFT JOIN tblvehicles 
ON tblvehicles.id = tblbooking.VehicleId
ORDER BY tblbooking.id DESC 
LIMIT 5
");
          while($row = $query->fetch(PDO::FETCH_ASSOC)){

            $start = new DateTime($row['FromDate']);
            $end = new DateTime($row['ToDate']);
            $days = $start->diff($end)->days;
            if($days == 0) $days = 1;
            
            $price = isset($row['PricePerDay']) ? $row['PricePerDay'] : 100;
            $payment = $days * $price;
          ?>
            <tr 
  data-status="<?php echo ($row['Status']==1) ? 'approved' : 'pending'; ?>"
  data-date="<?php echo date('Y-m-d', strtotime($row['PostingDate'])); ?>"
>
              <td><?php echo $row['BookingNumber']; ?></td>
              <td><?php echo $row['PostingDate']; ?></td>
              <td><?php echo $row['email']; ?></td>
              <td><?php echo $row['VehiclesTitle']; ?></td>
               <td><?php echo $days; ?> Days</td>
              <td><?php echo $row['ToDate']; ?></td>
              <td>$<?php echo $payment; ?></td>
              <td>
                <?php
                if($row['Status']==1){
                  echo "<span class='badge bg-success'>Approved</span>";
                } else {
                  echo "<span class='badge bg-warning'>Pending</span>";
                }
                ?>
              </td>
            </tr>
          <?php } ?>
          </tbody>

        </table>

      </div>
    </div>
  </div>

  <div class="col-12">
  <div class="card shadow-lg border-0 p-3">
    <h4 class="mb-3">Car Types Preference</h4>

    <?php
    $types = [
      ["Sedan", 30, "https://cdn-icons-png.flaticon.com/512/744/744465.png"],
      ["SUV", 25, "https://cdn-icons-png.flaticon.com/512/744/744467.png"],
      ["Hatchback", 20, "https://cdn-icons-png.flaticon.com/512/744/744466.png"],
      ["Convertible", 10, "https://cdn-icons-png.flaticon.com/512/744/744464.png"],
      ["Truck", 15, "https://cdn-icons-png.flaticon.com/512/1995/1995574.png"]
    ];

    foreach($types as $type){
    ?>

    <div class="d-flex align-items-center mb-3 p-2 border rounded">

      <!-- IMAGE -->
      <img src="<?php echo $type[2]; ?>" width="40" class="me-3">

      <!-- TEXT + BAR -->
      <div style="width:100%;">
        <div class="d-flex justify-content-between">
          <strong><?php echo $type[0]; ?></strong>
          <span><?php echo $type[1]; ?>%</span>
        </div>

        <div class="progress mt-1">
          <div class="progress-bar 
<?php 
if($type[0]=='Sedan') echo 'bg-primary';
elseif($type[0]=='SUV') echo 'bg-success';
elseif($type[0]=='Hatchback') echo 'bg-warning';
elseif($type[0]=='Convertible') echo 'bg-info';
else echo 'bg-danger';
?> 
" style="width:<?php echo $type[1]; ?>%">
          </div>
        </div>
      </div>

    </div>

    <?php } ?>

  </div>
</div>

<div id="toast" class="toast-box" style="display:none;"></div>

<script>
  const monthlyData = <?php echo json_encode(array_values($monthlyData)); ?>;
</script>
<script src="js/dashboard.js"></script>

</body>
</html>