<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard | EmeraldCars</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Sidebar -->
<div class="d-flex">
  <nav class="bg-dark text-white p-3" style="width:220px; min-height:100vh;">
    <h4 class="mb-4">Dashboard</h4>
    <ul class="nav flex-column">
      <li class="nav-item"><a href="#" class="nav-link text-white"><i class="bi bi-house me-2"></i> Home</a></li>
      <li class="nav-item"><a href="#" class="nav-link text-white"><i class="bi bi-car-front me-2"></i> Bookings</a></li>
      <li class="nav-item"><a href="#" class="nav-link text-white"><i class="bi bi-credit-card me-2"></i> Payments</a></li>
      <li class="nav-item"><a href="#" class="nav-link text-white"><i class="bi bi-person me-2"></i> Profile</a></li>
      <li class="nav-item"><a href="#" class="nav-link text-white"><i class="bi bi-chat-dots me-2"></i> Testimonials</a></li>
      <li class="nav-item"><a href="#" class="nav-link text-white"><i class="bi bi-envelope me-2"></i> Enquiries</a></li>
      <li class="nav-item"><a href="#" class="nav-link text-danger"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
    </ul>
  </nav>

  <!-- Main Content -->
  <div class="flex-grow-1 p-4">

    <!-- Welcome Panel -->
    <div class="card shadow mb-4">
      <div class="card-body">
        <h4>Welcome back, Kudzai</h4>
        <p>Status: Active Account</p>
      </div>
    </div>

    <!-- Dashboard Cards -->
    <div class="row mb-4">
      <div class="col-md-3"><div class="card p-3 text-center shadow"><h6>Total Bookings</h6><h4>12</h4></div></div>
      <div class="col-md-3"><div class="card p-3 text-center shadow"><h6>Active Rentals</h6><h4>2</h4></div></div>
      <div class="col-md-3"><div class="card p-3 text-center shadow"><h6>Pending Testimonials</h6><h4>1</h4></div></div>
      <div class="col-md-3"><div class="card p-3 text-center shadow"><h6>Loyalty Points</h6><h4>250</h4></div></div>
    </div>

    <!-- Active Booking -->
    <div class="card shadow mb-4">
      <div class="card-header">Active Booking</div>
      <div class="card-body">
        <img src="car.jpg" alt="Car" class="img-fluid mb-3" style="max-width:200px;">
        <p><strong>Toyota Corolla 2024</strong></p>
        <p>Pickup: City Center | Drop-off: Airport</p>
        <p>Apr 28, 2026 - Apr 30, 2026</p>
        <p>Status: Active</p>
        <button class="btn btn-primary me-2">Extend Booking</button>
        <button class="btn btn-danger">Return Car</button>
      </div>
    </div>

    <!-- Upcoming Reservations -->
    <div class="card shadow mb-4">
      <div class="card-header">Upcoming Reservations</div>
      <ul class="list-group list-group-flush">
        <li class="list-group-item">Nissan Altima - May 5, 2026 <button class="btn btn-sm btn-warning float-end">Cancel</button></li>
      </ul>
    </div>

    <!-- Booking History -->
    <div class="card shadow mb-4">
      <div class="card-header">Booking History</div>
      <ul class="list-group list-group-flush">
        <li class="list-group-item">Honda Civic - Apr 2026 - $200 <button class="btn btn-sm btn-outline-primary float-end">Download Invoice</button></li>
      </ul>
    </div>

    <!-- Payments & Billing -->
    <div class="card shadow mb-4">
      <div class="card-header">Payments & Billing</div>
      <div class="card-body">
        <p>Saved Card: **** **** **** 1234</p>
        <p>Last Transaction: $150 - Apr 20, 2026</p>
        <button class="btn btn-outline-secondary">View All Transactions</button>
      </div>
    </div>

    <!-- Available Cars -->
    <div class="card shadow mb-4">
      <div class="card-header">Available Cars</div>
      <div class="card-body">
        <input type="text" class="form-control mb-3" placeholder="Search cars...">
        <div class="row">
          <div class="col-md-4"><div class="card p-2"><img src="car1.jpg" class="img-fluid"><p>Tesla Model 3</p><button class="btn btn-sm btn-primary">Book Now</button></div></div>
          <div class="col-md-4"><div class="card p-2"><img src="car2.jpg" class="img-fluid"><p>BMW X5</p><button class="btn btn-sm btn-primary">Book Now</button></div></div>
        </div>
      </div>
    </div>

    <!-- Notifications -->
    <div class="card shadow mb-4">
      <div class="card-header">Notifications</div>
      <ul class="list-group list-group-flush">
        <li class="list-group-item">Booking confirmed for Nissan Altima</li>
        <li class="list-group-item">Your testimonial was approved</li>
      </ul>
    </div>

    <!-- Reviews & Ratings -->
    <div class="card shadow mb-4">
      <div class="card-header">Reviews & Ratings</div>
      <div class="card-body">
        <p>Rate your last ride: ⭐⭐⭐⭐☆</p>
      </div>
    </div>

    <!-- Offers & Loyalty -->
    <div class="card shadow mb-4">
      <div class="card-header">Offers & Loyalty</div>
      <div class="card-body">
        <p>Coupon: SAVE20</p>
        <p>Loyalty Points: 250</p>
      </div>
    </div>

    <!-- Analytics -->
    <div class="card shadow mb-4">
      <div class="card-header">Analytics</div>
      <div class="card-body">
        <p>Total Money Spent: $1,200</p>
        <p>Total Trips: 15</p>
        <p>Favorite Car Type: SUV</p>
        <p>Distance Traveled: 2,500 km</p>
      </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
