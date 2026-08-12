 <?php
session_start();
include('includes/config.php');

$isLoggedIn = isset($_SESSION['user_id']);

// SUV count
$sql = "SELECT COUNT(*) as total FROM tblvehicles WHERE VehicleType='SUV'";
$query = $dbh->prepare($sql);
$query->execute();
$countSUV = $query->fetch(PDO::FETCH_OBJ);

// Sedan count
$sql = "SELECT COUNT(*) as total FROM tblvehicles WHERE VehicleType='Sedan'";
$query = $dbh->prepare($sql);
$query->execute();
$countSedan = $query->fetch(PDO::FETCH_OBJ);

// Convertible count
$sql = "SELECT COUNT(*) as total FROM tblvehicles WHERE VehicleType='Convertible'";
$query = $dbh->prepare($sql);
$query->execute();
$countConvertible = $query->fetch(PDO::FETCH_OBJ);

// Coupe count
$sql = "SELECT COUNT(*) as total FROM tblvehicles WHERE VehicleType='Coupe'";
$query = $dbh->prepare($sql);
$query->execute();
$countCoupe = $query->fetch(PDO::FETCH_OBJ);

// Sports count
$sql = "SELECT COUNT(*) as total FROM tblvehicles WHERE VehicleType='Sports'";
$query = $dbh->prepare($sql);
$query->execute();
$countSports = $query->fetch(PDO::FETCH_OBJ);

// Luxury count
$sql = "SELECT COUNT(*) as total FROM tblvehicles WHERE VehicleType='Luxury'";
$query = $dbh->prepare($sql);
$query->execute();
$countLuxury = $query->fetch(PDO::FETCH_OBJ);



if(isset($_SESSION['logout_msg'])) {
    echo '<div class="alert alert-success text-center">'
         . htmlspecialchars($_SESSION['logout_msg']) .
         '</div>';
    unset($_SESSION['logout_msg']); // clear it so it only shows once
}


          
// Fetch featured cars only
$sql = "SELECT v.*, b.BrandName 
        FROM tblvehicles v 
        JOIN tblbrands b ON b.BrandName = v.VehiclesBrand 
        WHERE v.status = 1 AND v.featured = 1 
        ORDER BY v.id DESC LIMIT 12"; 

$query = $dbh->prepare($sql);
$query->execute();
$cars = $query->fetchAll(PDO::FETCH_OBJ);

// Fetch approved testimonials
// Fetch approved testimonials with safe fallback
$sql = "SELECT t.Testimonial, t.PostingDate, 
               COALESCE(u.FullName, 'Customer') AS DisplayName
        FROM tbltestimonial t
        LEFT JOIN tblusers u ON u.EmailId = t.UserEmail
        WHERE t.status=1
        ORDER BY t.PostingDate DESC LIMIT 6"; 

$query = $dbh->prepare($sql);
$query->execute();
$homepageTestimonials = $query->fetchAll(PDO::FETCH_OBJ);

// Stats counters
$carsCount   = $dbh->query("SELECT COUNT(*) FROM tblvehicles")->fetchColumn();
$usersCount  = $dbh->query("SELECT COUNT(*) FROM tblusers")->fetchColumn();
$reviewsCount = $dbh->query("SELECT COUNT(*) FROM tbltestimonial WHERE status=1")->fetchColumn();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Emerald Cars</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="/emeraldcarrental/css/homepage.css">
<style>

</style>

</head>
<body>

<div class="hero-slider">

  <!-- Topbar -->
  <div class="topbar">
    <div class="top-info">
      <span><i class="fas fa-map-marker-alt"></i> 45 Premium Avenue</span>
      <span><i class="fas fa-phone"></i> +111-222-333</span>
      <span><i class="fas fa-envelope"></i> info@example.com</span>
    </div>
    <div class="top-social">
      <a href="#"><i class="fab fa-facebook-f"></i></a>
      <a href="#"><i class="fab fa-twitter"></i></a>
      <a href="#"><i class="fab fa-instagram"></i></a>
      <a href="#"><i class="fab fa-linkedin-in"></i></a>
    </div>
  </div>

  <!-- Navbar -->
  <div class="navbar">
    <div class="logo-container">
      <img src="admin/vehicleimages/logo.jpg" alt="Emerald Cars Logo">
      <div>
        <span class="company-name">
          <span class="emerald">Emerald</span> <span class="cars">Cars</span>
        </span>
        <span class="tagline">Car Rental Service</span>
      </div>
    </div>

 <ul class="nav-links">
  <li><a href="index.php">Home</a></li>
  <li class="dropdown">
  <i class="fas fa-user"></i>
  <a href="app.php?page=about">About </a>
  <ul class="dropdown-menu">
    <!-- Only include app.php pages NOT already in index nav -->
    <li><a href="app.php?page=services">Services</a></li>
    <li><a href="app.php?page=blog">Blog</a></li>
    <li><a href="app.php?page=orders">My Orders</a></li>
    <!-- add more app.php sections here as needed -->
  </ul>
</li>

<li><a href="app.php?page=cars">Cars</a></li>

<?php if(empty($_SESSION['user_id'])): ?>
  <li><a href="login.php?redirect=contact.php">Contact</a></li>
<?php else: ?>
  <li><a href="contact.php">Contact</a></li>
<?php endif; ?>

<li class="user-menu">

<?php if(!$isLoggedIn): ?>

  <a href="login.php" class="login-btn">
    <i class="fas fa-user"></i> Login
  </a>

<?php else: ?>

  <div class="user-dropdown">
    <span class="user-name">
      <i class="fas fa-user"></i>
      <?= !empty($_SESSION['fullname']) ? $_SESSION['fullname'] : 'User'; ?> ▼
    </span>

    <div class="dropdown-content">
      <a href="users/profile.php">Profile</a>
      <a href="users/bookings.php">My Bookings</a>
      <a href="users/testimonials.php">My Testimonials</a>
      <a href="./logout.php">Logout</a>
    </div>
  </div>

<?php endif; ?>

</li>

      <span class="search-icon"><i class="fas fa-search"></i></span>
      <a href="#booking-section" class="rent-btn">RENT CAR</a></li>
    </ul>
    <div class="search-box">
    <input type="text" placeholder="Search cars...">
  </div>
 </div> <!-- ✅ close navbar here -->


  <!-- Slides (outside navbar) -->
  <div class="slide active" style="background-image:url('admin/vehicleimages/mycar.jpg')">
    <div class="overlay"></div>
    <div class="hero-text">
      <h1>Welcome To EmeraldCars</h1>
        <h2>Better care with best prices</h2>
        <p>We provide Best cars with the best prices. We are expert in car rental. Enjoy your holidays with us. We make your drive memorable. We care for your safety.</p>
<?php if(empty($_SESSION['user_id'])): ?>
  <a href="login.php?redirect=app.php?page=about" class="btn-learn">Learn More</a>
<?php else: ?>
  <a href="app.php?page=about" class="btn-learn">Learn More</a>
<?php endif; ?>

    </div>
  </div>

  <div class="slide" style="background-image:url('admin/vehicleimages/listing_img3.jpg')">
    <div class="overlay"></div>
    <div class="hero-text">
      <h1>Welcome To EmeraldCars</h1>
        <h3>Best cars for the best journey</h3>
        <p>We provide Best cars with the best prices. We are expert in car rental. Enjoy your holidays with us. We make your drive memorable. We care for your safety.</p>
<?php if(empty($_SESSION['user_id'])): ?>
  <a href="login.php?redirect=app.php?page=cars" class="btn-learn">Learn More</a>
<?php else: ?>
  <a href="app.php?page=cars" class="btn-learn">Learn More</a>
<?php endif; ?>

    </div>
  </div>

  <div class="slide" style="background-image:url('admin/vehicleimages/danger.jpg')">
    <div class="overlay"></div>
    <div class="hero-text">
      <h1>Welcome To EmeraldCars</h1>
        <h3>Drive in Style</h3>
        <p>Choose from our premium fleet and enjoy unmatched comfort and safety on every journey.</p>
<?php if(empty($_SESSION['user_id'])): ?>
  <a href="login.php?redirect=app.php?page=about" class="btn-learn">Learn More</a>
<?php else: ?>
  <button class="btn-learn" data-bs-toggle="modal" data-bs-target="#learnModal">Learn More</button>
<?php endif; ?>

    </div>
  </div>

</div> <!-- ✅ close hero-slider -->


<div class="booking-section" id="booking-section"> 
   <h3>Drive Your Way</h3>
  <h1>Rent A Car</h1>

  <div class="preview-wrapper">
  <div class="preview-form">

  <h2 class="preview-title">What You Need To Rent a Car</h2>
<p class="preview-sub">Fill these details in the booking page 🚗</p>

  <div class="input-box">
    <span>👤</span>
    <input type="text" placeholder="Full Name" disabled>
  </div>

  <div class="input-box">
    <span>📞</span>
    <input type="text" placeholder="Phone Number" disabled>
  </div>

  <div class="input-box">
    <span>📍</span>
    <input type="text" placeholder="Pickup Location" disabled>
  </div>

  <div class="date-row">
    <div class="input-box">
      <span>📅</span>
      <input type="text" placeholder="From Date" disabled>
    </div>

    <div class="input-box">
      <span>📅</span>
      <input type="text" placeholder="To Date" disabled>
    </div>
  </div>

<?php if(empty($_SESSION['user_id'])): ?>
  <!-- Not logged in: send them to login first -->
  <a href="login.php?redirect=users/booking.php" class="preview-btn">Proceed to Booking</a>
  <p class="login-note">Login required to continue booking</p>
<?php else: ?>
  <!-- Logged in: go straight to booking -->
  <a href="users/booking.php" class="preview-btn">Proceed to Booking</a>
<?php endif; ?>
 
 <span class="tagline">Own your masterpiece 🚗✨</span>
</div>
</div>
</div>


   <div class="about-section">
  <div class="about-slide" style="background-image:url('admin/vehicleimages/7.jpg')">
    <div class="about-overlay"></div>
    <div class="about-content">
      <h2>About Us</h2>
      <h3>Drive With Confidence, Rent With Emerald Cars</h3>
      <p>
        Emerald Cars is your reliable choice for premium and affordable car rentals. Whether it’s for airport transfers, business travel, or special occasions, we provide a wide range of well‑maintained vehicles to meet your needs. Experience hassle‑free booking, 24/7 support, and the best price guarantee — all in one place.
      </p>
       </div>
  </div>
  </div>

    <div class="services-section">
      <h2>At Emerald Cars, We Offer the best</h2>
  <h2>Our Services</h2>

  <div class="services-grid">

  <div class="service-box">
    <i class="fas fa-car"></i>
    <h3>Variety Of Cars</h3>
    <p>Choose from a wide range of vehicles to suit every journey, from compact city cars to spacious SUVs.</p>
    <?php if(empty($_SESSION['user_id'])): ?>
      <a href="login.php?redirect=app.php?page=services" class="read-btn">READ MORE</a>
    <?php else: ?>
      <a href="app.php?page=services" class="read-btn">READ MORE</a>
    <?php endif; ?>
  </div>

  <div class="service-box">
    <i class="fas fa-tags"></i>
    <h3>Best Price Guarantee</h3>
    <p>We offer the most competitive prices in the market. Enjoy great value without compromising on quality.</p>
    <?php if(empty($_SESSION['user_id'])): ?>
      <a href="login.php?redirect=app.php?page=cars" class="read-btn">READ MORE</a>
    <?php else: ?>
      <a href="app.php?page=cars" class="read-btn">READ MORE</a>
    <?php endif; ?>
  </div>

  <div class="service-box">
    <i class="fas fa-clock"></i>
    <h3>Available 24 X 7</h3>
    <p>Our car rental service is available 24/7 to meet your travel needs anytime, anywhere.</p>
    <?php if(empty($_SESSION['user_id'])): ?>
      <a href="login.php?redirect=app.php?page=services" class="read-btn">READ MORE</a>
    <?php else: ?>
      <a href="app.php?page=services" class="read-btn">READ MORE</a>
    <?php endif; ?>
  </div>

  <div class="service-box">
    <i class="fas fa-phone"></i>
    <h3>Phone Reservation</h3>
    <p>Prefer to book by phone? Our friendly staff is ready to assist you with quick and convenient car reservations.</p>
    <?php if(empty($_SESSION['user_id'])): ?>
  <li><a href="login.php?redirect=contact.php">Contact</a></li>
  <?php else: ?>
    <li><a href="contact.php">Contact</a></li>
  <?php endif; ?>

  </div>

  <div class="service-box">
    <i class="fas fa-calendar-check"></i>
    <h3>Reservation Anytime</h3>
    <p>Reserve your car anytime, from anywhere. Our seamless booking system is open 24/7 to suit your schedule.</p>
    <?php if(empty($_SESSION['user_id'])): ?>
      <a href="login.php?redirect=app.php?page=cars" class="read-btn">READ MORE</a>
    <?php else: ?>
      <a href="app.php?page=cars" class="read-btn">READ MORE</a>
    <?php endif; ?>
  </div>

  <div class="service-box">
    <i class="fas fa-star"></i>
    <h3>Best Quality Cars Add Ons</h3>
    <p>Drive with confidence in our top-quality, well-maintained vehicles. We ensure every car meets the highest standards.</p>
    <?php if(empty($_SESSION['user_id'])): ?>
      <a href="login.php?redirect=app.php?page=shop" class="read-btn">READ MORE</a>
    <?php else: ?>
      <a href="app.php?page=shop" class="read-btn">READ MORE</a>
    <?php endif; ?>
  </div>

</div>


    <div class="stats-section">
  <div class="stats-overlay"></div>
  <div class="stats-grid">
    <div class="stat-box">
      <i class="fas fa-briefcase"></i>
      <h3><span class="counter" data-target="500">0</span>+</h3>
      <p>Experience</p>
    </div>
    <div class="stat-box">
      <i class="fas fa-car"></i>
      <h3><span class="counter" data-target="<?php echo $carsCount; ?>">0</span>+</h3>
      <p>Cars</p>
    </div>
    <div class="stat-box">
      <i class="fas fa-users"></i>
      <h3><span class="counter" data-target="<?php echo $usersCount; ?>">0</span>+</h3>
      <p>Customers</p>
    </div>
    <div class="stat-box">
      <i class="fas fa-comments"></i>
      <h3><span class="counter" data-target="<?php echo $reviewsCount; ?>">0</span>+</h3>
      <p>Reviews</p>
    </div>
  </div>
</div>


  <div class="parallax-wrapper">
  <div class="category-wrapper">

   <!--WHITE BACKGROUND CONTAINER -->
  <div class="category-background"></div>

  <!-- ACTUAL CONTENT (FLOATING) -->
  <div class="car-categories-section">
<h2 class="section-title">Explore Our Rental Cars</h2>
<p class="section-quote">"Drive the journey, don’t just reach the destination."</p>
  <div class="categories-grid">

    <div class="category-card">
      <div class="category-img">
      <img src="admin/vehicleimages/hyundai Creta car-suv.jpg" alt="SUV">
      </div>
       <div class="category-content">
      <h3>SUV</h3>
      <!-- SUV card -->
      <p><?php echo htmlentities($countSUV->total); ?> Cars Available</p>      
      <a href="app.php?page=cars&type=SUV" class="view-more">
        <i class="fas fa-arrow-right"></i> View SUVs
</a>

    </div>
    </div>

    <div class="category-card">
      <div class="category-img">
      <img src="admin/vehicleimages/Hyundai Verna-sedan.jpg" alt="Sedan">
      </div>
      <div class="category-content">
      <h3>Sedan</h3>
<!-- Sedan card -->
<p><?php echo htmlentities($countSedan->total); ?> Cars Available</p>     
 <a href="app.php?page=cars&type=Sedan" class="view-more">
        <i class="fas fa-arrow-right"></i> View Sedans
      </a>
</div>
    </div>

    <div class="category-card">
      <div class="category-img">
      <img src="admin/vehicleimages/BMW Z4.jpg" alt="Convertible">
      </div>

      <div class="category-content">
      <h3>Convertible</h3>
<!-- Convertible card -->
<p><?php echo htmlentities($countConvertible->total); ?> Cars Available</p>      
<a href="app.php?page=cars&type=Convertible" class="view-more">
        <i class="fas fa-arrow-right"></i> View Convertibles
      </a>
    </div>
    </div>

    <div class="category-card">
      <div class="category-img">
      <img src="admin/vehicleimages/rolls royce2.jpg" alt="Hatchback">
      </div>
       <div class="category-content">
      <h3>Coupe</h3>
<!-- Coupe card -->
<p><?php echo htmlentities($countCoupe->total); ?> Cars Available</p>     
 <a href="app.php?page=cars&type=Coupe" class="view-more">
        <i class="fas fa-arrow-right"></i> View Coupe
      </a>
    </div>
    </div>

    <div class="category-card">
      <div class="category-img">
      <img src="admin/vehicleimages/Hyundai Venue.jpg" alt="Sports Car">
      </div>
       <div class="category-content">
      <h3>Sports Car</h3>
<!-- Sports card -->
<p><?php echo htmlentities($countSports->total); ?> Cars Available</p>    
  <a href="app.php?page=cars&type=Sports" class="view-more">
        <i class="fas fa-arrow-right"></i> View Sports Cars
      </a>
    </div>
    </div>


    <div class="category-card">
      <div class="category-img">
      <img src="admin/vehicleimages/rangerover.jpg" alt="Luxury Cars">
      </div>

      <div class="category-content">
      <h3>Luxury</h3>
<!-- Luxury card -->
<p><?php echo htmlentities($countLuxury->total); ?> Cars Available</p>     
 <a href="app.php?page=cars&type=Luxury" class="view-more">
      <i class="fas fa-arrow-right"></i> View Luxury Cars
      </a>
      </div>
    </div>

  </div>
</div>
</div>
</div>

   <section class="parallax-section">
    <div class="portfolio-section">

  <!-- TITLES -->
  <h3 class="portfolio-small">Explore Our Work</h3>
  <h2 class="portfolio-title">Our Portfolio</h2>

  <!-- FILTER BUTTONS -->
 <div class="portfolio-filters">
  <button class="filter-btn active" data-filter="all">All</button>
  <button class="filter-btn" data-filter="Corporate Rentals">Corporate Rentals</button>
  <button class="filter-btn" data-filter="Event Rentals">Event Rentals</button>
  <button class="filter-btn" data-filter="Airport Service">Airport Service</button>
</div>


  <!-- GRID -->
  <div class="portfolio-grid">

    <!-- CARD 1 -->
<div class="portfolio-item" data-category="Corporate Rentals">
      <img src="admin/vehicleimages/2025 Hyundai Tucson.jpg">
      <div class="portfolio-overlay">
        <h4>Corporate Rentals</h4>
        <a href="app.php?page=services&type=corporate">Executive Lease</a>
        <div class="plus-btn">+</div>
      </div>
    </div>

    <!-- CARD 2 -->
<div class="portfolio-item" data-category="Event Rentals">      <img src="admin/vehicleimages/white BMW M3 F80 with Black Roof.jpg">  
      <div class="portfolio-overlay">
        <h4>Event Rentals</h4>
        <a href="app.php?page=services&type=event.php">Gala Ride</a>
        <div class="plus-btn">+</div>
      </div>
    </div>

    <!-- CARD 3 -->
<div class="portfolio-item" data-category="Airport Service">
      <img src="admin/vehicleimages/Airport Limo.jpg">
      <div class="portfolio-overlay">
        <h4>Airport Service</h4>
        <a href="app.php?page=services&type=airport">Airport Pickup</a>
        <div class="plus-btn">+</div>
      </div>
    </div>

    
    <!-- CARD 4 -->
<div class="portfolio-item" data-category="Corporate Rentals">   <img src="admin/vehicleimages/Fortuner.jpg">
  <div class="portfolio-overlay">
    <h4>Corporate Rentals</h4>
    <a href="app.php?page=services&type=corporate">Staff Shuttle</a>
    <div class="plus-btn">+</div>
  </div>
</div> <!-- 🔥 ADD THIS -->
    


    <!-- CARD 5 -->
<div class="portfolio-item" data-category="Event Rentals">
      <img src="admin/vehicleimages/gala rides.jpg">
      <div class="portfolio-overlay">
        <h4>Event Rentals</h4>
        <a href="app.php?page=services&type=event">Wedding Car</a>
        <div class="plus-btn">+</div>
      </div>
    </div>

    <!-- CARD 6 -->
<div class="portfolio-item" data-category="Airport Service">
      <img src="admin/vehicleimages/Hyundai Exter.jpg">
      <div class="portfolio-overlay">
        <h4>Airport Service</h4>
        <a href="app.php?page=services&type=airport">Business Ride</a>
        <div class="plus-btn">+</div>
      </div>
    </div>


  </div>
</div>

  </section>
</div>


<section class="featured-section">

  <h2 class="section-title">Our Popular Collection</h2>
  <p class="section-quote">"Drive the journey, don’t just reach the destination."</p>

  <div class="slider-container">

    <!-- LEFT ARROW -->
    <button class="arrow prev" onclick="prevCarSlide()">❮</button>   

    <div class="slider">
      <div class="car-track">

        <?php 
        // Split cars into groups of 3 for your slider design
        $chunks = array_chunk($cars, 3);
        $first = true;
        foreach($chunks as $group): ?>
          <div class="car-slide <?php echo $first ? 'active' : ''; ?>">
            <?php foreach($group as $car): ?>
              <div class="car-card">
  <div class="car-gallery">
    <img src="cars/<?php echo htmlentities($car->Vimage1); ?>" class="active" alt="Front view">
    <img src="cars/<?php echo htmlentities($car->Vimage2); ?>" alt="Side view">
    <img src="cars/<?php echo htmlentities($car->Vimage3); ?>" alt="Interior view">

    <!-- Gallery controls -->
    <button class="gallery-prev">❮</button>
    <button class="gallery-next">❯</button>
  </div>

  <div class="car-info">
    <h3><?php echo htmlentities($car->BrandName . " " . $car->VehiclesTitle); ?></h3>
    <p class="price">₹<?php echo htmlentities($car->PricePerDay); ?>/day</p>
    <div class="car-details">
      <span>🚗 <?php echo htmlentities($car->VehicleType); ?></span>
      <span>👤 <?php echo htmlentities($car->SeatingCapacity); ?> seats</span>
      <span>⛽ <?php echo htmlentities($car->FuelType); ?></span>
      <span>📅 <?php echo htmlentities($car->ModelYear); ?></span>
    </div>
<a href="login.php?redirect=<?php echo urlencode('users/booking.php?car='.$car->id); ?>">  Rent Car
</a>
  </div>
</div>


            <?php endforeach; ?>
          </div>
        <?php 
        $first = false;
        endforeach; 
        ?>

      </div>
    </div>

    <!-- RIGHT ARROW -->
    <button class="arrow next" onclick="nextCarSlide()">❯</button>  

  </div>
</section>


<section class="team-section">

  <h3 class="team-small">Meet Our Team</h3>
  <h2 class="team-title">Our Team</h2>

  <div class="team-container">

    <!-- MEMBER 1 -->
    <div class="team-card">
      <img src="admin/vehicleimages/aboutus2.jpg">
      <div class="team-overlay">
        <div class="social-icons">
          <?php if(empty($_SESSION['user_id'])): ?>
            <a href="login.php?redirect=contact.php"><i class="fab fa-facebook-f"></i></a>
            <a href="login.php?redirect=contact.php"><i class="fab fa-twitter"></i></a>
            <a href="login.php?redirect=contact.php"><i class="fab fa-instagram"></i></a>
          <?php else: ?>
            <a href="contact.php"><i class="fab fa-facebook-f"></i></a>
            <a href="contact.php"><i class="fab fa-twitter"></i></a>
            <a href="contact.php"><i class="fab fa-instagram"></i></a>
          <?php endif; ?>
        </div>
      </div>
      <h4>Kudzai Chimidzi</h4>
      <p>Founder & CEO</p>
    </div>

    <!-- MEMBER 2 -->
    <div class="team-card">
      <img src="admin/vehicleimages/aboutus3.jpg">
      <div class="team-overlay">
        <div class="social-icons">
          <?php if(empty($_SESSION['user_id'])): ?>
            <a href="login.php?redirect=contact.php"><i class="fab fa-facebook-f"></i></a>
            <a href="login.php?redirect=contact.php"><i class="fab fa-twitter"></i></a>
            <a href="login.php?redirect=contact.php"><i class="fab fa-instagram"></i></a>
          <?php else: ?>
            <a href="contact.php"><i class="fab fa-facebook-f"></i></a>
            <a href="contact.php"><i class="fab fa-twitter"></i></a>
            <a href="contact.php"><i class="fab fa-instagram"></i></a>
          <?php endif; ?>
        </div>
      </div>
      <h4>Junior Chimidzi</h4>
      <p>Manager</p>
    </div>

    <!-- MEMBER 3 -->
    <div class="team-card">
      <img src="admin/vehicleimages/aboutus1.jpg">
      <div class="team-overlay">
        <div class="social-icons">
          <?php if(empty($_SESSION['user_id'])): ?>
            <a href="login.php?redirect=contact.php"><i class="fab fa-facebook-f"></i></a>
            <a href="login.php?redirect=contact.php"><i class="fab fa-twitter"></i></a>
            <a href="login.php?redirect=contact.php"><i class="fab fa-instagram"></i></a>
          <?php else: ?>
            <a href="contact.php"><i class="fab fa-facebook-f"></i></a>
            <a href="contact.php"><i class="fab fa-twitter"></i></a>
            <a href="contact.php"><i class="fab fa-instagram"></i></a>
          <?php endif; ?>
        </div>
      </div>
      <h4>Tendai Matewe</h4>
      <p>Director</p>
    </div>

    <!-- MEMBER 4 -->
    <div class="team-card">
      <img src="admin/vehicleimages/aboutus4.jpg">
      <div class="team-overlay">
        <div class="social-icons">
          <?php if(empty($_SESSION['user_id'])): ?>
            <a href="login.php?redirect=contact.php"><i class="fab fa-facebook-f"></i></a>
            <a href="login.php?redirect=contact.php"><i class="fab fa-twitter"></i></a>
            <a href="login.php?redirect=contact.php"><i class="fab fa-instagram"></i></a>
          <?php else: ?>
            <a href="contact.php"><i class="fab fa-facebook-f"></i></a>
            <a href="contact.php"><i class="fab fa-twitter"></i></a>
            <a href="contact.php"><i class="fab fa-instagram"></i></a>
          <?php endif; ?>
        </div>
      </div>
      <h4>Governor Charlse</h4>
      <p>Sales Manager</p>
    </div>

  </div>

</section>

<section class="testimonial-section">
  <h4 class="portfolio-small">Testimonials</h4>
  <h2 class="portfolio-title">What Our Clients Say</h2>

  <div class="testimonial-slider">
    <div class="testimonial-track">

      <?php if($homepageTestimonials): ?>
        <?php foreach($homepageTestimonials as $t): ?>
          <div class="testimonial-card">
            <div class="user-img">
              <img src="uploads/default-user.jpg" alt="User">
            </div>
            <div class="stars">★★★★★</div>
            <p>"<?php echo htmlentities($t->Testimonial); ?>"</p>
            <h4><?php echo htmlentities($t->DisplayName); ?></h4>

            <small class="text-muted">
              <?php echo htmlentities($t->PostingDate); ?>
            </small>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No testimonials yet. Be the first to share your experience!</p>
      <?php endif; ?>

    </div>
  </div>
</section>


<section class="logo-section">

  <div class="logo-slider">
    <div class="logo-track">

      <img src="admin/vehicleimages/bmw logo.jpg">
      <img src="admin/vehicleimages/bugatti logo.jpg">
      <img src="admin/vehicleimages/chevrolet logo.jpg">
      <img src="admin/vehicleimages/lambo.jpg">
      <img src="admin/vehicleimages/maruti logo.jpg">
      <img src="admin/vehicleimages/mazda.jpg">
      <img src="admin/vehicleimages/mercedes benz logo.jpg">
      <img src="admin/vehicleimages/nissan logo.jpg">
      <img src="admin/vehicleimages/volkswagon logo.jpg">
      <img src="admin/vehicleimages/audi logo.jpg">

      <!-- duplicate for smooth loop -->
      <img src="admin/vehicleimages/bmw logo.jpg">
      <img src="admin/vehicleimages/bugatti logo.jpg">
      <img src="admin/vehicleimages/chevrolet logo.jpg">
      <img src="admin/vehicleimages/lambo.jpg">
      <img src="admin/vehicleimages/maruti logo.jpg">
      <img src="admin/vehicleimages/mazda.jpg">
      <img src="admin/vehicleimages/mercedes benz logo.jpg">
      <img src="admin/vehicleimages/nissan logo.jpg">
      <img src="admin/vehicleimages/volkswagon logo.jpg">
      <img src="admin/vehicleimages/audi logo.jpg">
    </div>
  </div>

</section>


<!-- NEXT SECTION -->
<section class="next-section">
  <div class="next-container">
    <!-- Column 1 -->
    <div class="next-col">
       <div class="logo-container">
      <img src="admin/vehicleimages/logo.jpg" alt="Emerald Cars Logo">
      <div>
        <span class="company-name">
          <span class="emerald">Emerald</span> <span class="cars">Cars</span>
        </span>
        <span class="tagline">Car Rental Service</span>
      </div>
    </div>
      <p> 
         Drive your journey forward with confidence.
        Discover affordable, reliable, and premium-quality
        rental cars designed for comfort and performance.
        We deliver a seamless, stress-free ride every time.</p>
      <!-- Social Media Icons -->
<div class="hero-social">
  <?php if(empty($_SESSION['login'])): ?>
    <a href="login.php?redirect=contact-us.php"><i class="fab fa-facebook-f"></i></a>
    <a href="login.php?redirect=contact-us.php"><i class="fab fa-instagram"></i></a>
    <a href="login.php?redirect=contact-us.php"><i class="fab fa-x-twitter"></i></a>
    <a href="login.php?redirect=contact-us.php"><i class="fab fa-linkedin-in"></i></a>
  <?php else: ?>
    <a href="contact-us.php"><i class="fab fa-facebook-f"></i></a>
    <a href="contact-us.php"><i class="fab fa-instagram"></i></a>
    <a href="contact-us.php"><i class="fab fa-x-twitter"></i></a>
    <a href="contact-us.php"><i class="fab fa-linkedin-in"></i></a>
  <?php endif; ?>
</div>


    </div>

    <!-- Column 2 -->
    <!-- Column 2 -->
<div class="next-col">
  <h4>Quick Links</h4>
  <ul>
    <li><a href="app.php?page=about">About Us</a></li>
    <li><a href="app.php?page=services">Services</a></li>
    <li><a href="app.php?page=cars">Browse Cars</a></li>
    <li>
      <?php if(empty($_SESSION['login'])): ?>
        <a href="login.php?redirect=users/booking.php">Rent Cars</a>
      <?php else: ?>
        <a href="users/booking.php">Rent Cars</a>
      <?php endif; ?>
    </li>
    <li><a href="app.php?page=blogs">Blogs</a></li>
     <li><a href="users/contact-us.php">Contact Us</a></li>
  </ul>
</div>


    <!-- Column 3 -->
    <div class="next-col">
      <h4>Contact Info</h4>
      <p>📍 EmeraldCars Office, Premium Avenue</p>
      <p>📞 +111-222-333</p>
      <p>📞 +123-456-7890</p>
      <p>✉️ info@emeraldcars.com</p>
    </div>

    <!-- Column 4 -->
    <div class="next-col">
      <h4>Newsletter</h4>
      <p>Get the latest deals and offers straight to your inbox.</p>
<form method="post" action="contact-us.php">
  <input type="email" name="subscriberEmail" placeholder="Your Email Address" required>
  <button type="submit" name="subscribe">Subscribe</button>
</form>

    </div>
  </div>
</section>

<!-- FINAL SECTION -->
<footer class="final-footer">
  <div class="footer-left">
    <p>© EmeraldCars 2026 All Rights Reserved</p>
  </div>
  <div class="footer-right">
    <a href="users/profile.php#privacy-section">Privacy Policy</a>
    <a href="users/profile.php#privacy-section">Terms & Conditions</a>
  </div>
</footer>




<!-- CHATBOT -->
<div class="chat-toggle" id="chatToggle" onclick="toggleChat()">
  <div class="ai-pulse"></div>
  <i class="fas fa-comment-dots"></i>
  <span class="chat-text">Ask me</span>
</div>

<!-- CHAT WINDOW -->
<div class="chatbot" id="chatbot">

  <!-- HEADER (DRAG HANDLE) -->
  <div class="chat-header" id="chatHeader">
    <span>EmeraldCars Bot</span>
    <button onclick="toggleChat()">✖</button>
  </div>

  <!-- BODY -->
  <div class="chat-body" id="chatBody"></div>

  <!-- TYPING -->
  <div class="chat-typing" id="typing">
    <span></span><span></span><span></span>
  </div>

  <!-- INPUT -->
  <div class="chat-input">
    <input type="text" id="userInput" placeholder="Type a message..." />
    <button onclick="sendMessage()">Send</button>
  </div>

</div>


<!-- FLOATING BOOK NOW BUTTON -->
<?php if(empty($_SESSION['login'])): ?>
  <a href="login.php?redirect=users/booking.php" class="floating-btn">
    🚗 Book Now
  </a>
<?php else: ?>
  <a href="users/booking.php" class="floating-btn">
    🚗 Book Now
  </a>
<?php endif; ?>


</body>
<script>
function showSection(sectionId) {
  // Hide all sections
  document.querySelectorAll('.section').forEach(sec => sec.style.display = 'none');
  // Show the selected one
  const target = document.getElementById(sectionId);
  if (target) {
    target.style.display = 'block';
    target.scrollIntoView({ behavior: 'smooth' });
  }
}
</script>

<script src="js/homepage.js"></script>
</html>
