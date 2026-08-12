<!-- SIDEBAR -->
<div id="sidebar" class="sidebar bg-dark text-white p-3" style="width:220px;">
  <h4>Menu</h4>
  <ul class="nav flex-column">
    <li class="nav-item"><a href="dashboard.php" class="nav-link text-white"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
    <li class="nav-item"><a href="manage-booking.php" class="nav-link text-white"><i class="bi bi-journal-text me-2"></i> Bookings</a></li>
    <li class="nav-item"><a href="manage-vehicles.php" class="nav-link text-white"><i class="bi bi-car-front-fill me-2"></i> Vehicles</a></li>
    <li class="nav-item"><a href="manage-testimonials.php" class="nav-link text-white"><i class="bi bi-chat-dots-fill me-2"></i> Testimonials</a></li>
    <li class="nav-item"><a href="logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
  </ul>
</div>

<!-- TOP BAR -->
<div class="topbar d-flex align-items-center mb-4">
  <i class="bi bi-list fs-3 me-3" onclick="toggleSidebar()" style="cursor:pointer;"></i>
  <div class="ms-auto d-flex align-items-center">
    <button onclick="toggleDarkMode()" class="btn btn-dark me-2"><i class="bi bi-moon"></i></button>
    <input type="text" class="form-control me-3" placeholder="Search..." style="width:200px;">
    <i class="bi bi-bell fs-5 me-3 position-relative">
      <span id="notifCount" class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill">0</span>
    </i>
    <span class="fw-bold">Admin: <?= $_SESSION['alogin']; ?></span>
  </div>
</div>
