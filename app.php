<?php
session_start();
include('includes/config.php');

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

// ✅ DEFAULT PAGE
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Emerald Cars | App</title>
  <link rel="stylesheet" href="./css/app.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<!-- TOP NAVBAR (same design system as index) -->
<body class='app-body'>

<div class="app-navbar">

  <div class="logo-container">
    <img src="admin/vehicleimages/logo.jpg" style="height:45px;">
    <div class="company-name">
      <span class="emerald">Emerald</span>
      <span class="cars">Cars</span>
    </div>
  </div>

  <div class="app-links">
<a href="index.php">Home</a>
<a href="app.php?page=about">About</a>
<a href="app.php?page=services">Services</a>
<a href="app.php?page=shop">Shop</a>
<a href="app.php?page=blog">Blog</a>
<a href="app.php?page=cars">Cars</a>
  </div>

</div>


<div class="app-content">
      <?php

if($page == "home"){
    echo "<h2>Welcome to Emerald Cars 🚗</h2>
          <p>Your dashboard is ready.</p>";
}

else if($page == "about"){
?>

<div class='page about-page'>

    <!-- HERO -->
    <section class='hero-section'>
        <h1>About Emerald Cars</h1>
        <p>Luxury • Comfort • Reliability</p>
    </section>

    <!-- CONTENT ROW -->
    <section class='split-section'>
        
        <div class='text-block'>
            <h2>Who We Are</h2>
            <p>
                Emerald Cars is a premium car rental platform offering luxury,
                comfort, and affordable vehicles for all travel needs.
                We focus on safety, trust, and customer satisfaction.
            </p>
        </div>

        <div class='image-block'>
            <img src='admin/vehicleimages/cargrp2.jpg'>
        </div>

    </section>

    <section class='text-section'>
        <h2>Our Mission</h2>

        <p>
            Our mission is to make luxury and comfort accessible to everyone.
            We aim to provide reliable transport solutions for individuals,
            families, and businesses across all travel needs.
        </p>

        <p>
            We continuously upgrade our fleet and services to ensure every customer
            enjoys a smooth, safe, and premium driving experience with Emerald Cars.
        </p>
    </section>

    <!-- TEAM -->
    <section class='team-section'>
        <h2>Meet Our Team</h2>
        <p>Dedicated professionals ensuring your best experience</p>

        <div class='team-grid'>

            <div class='team-card'>
                <img src='admin/vehicleimages/aboutus2.jpg'>
                <h3>Kudzai Chimidzi</h3>
                <p>CEO & Founder</p>
            </div>

            <div class='team-card'>
                <img src='admin/vehicleimages/aboutus3.jpg'>
                <h3>Junior Chimidzi</h3>
                <p>Operations Manager</p>
            </div>

            <div class='team-card'>
                <img src='admin/vehicleimages/aboutus1.jpg'>
                <h3>Tendai Matewe</h3>
                <p>Director/Customer Support</p>
            </div>

            <div class='team-card'>
                <img src='admin/vehicleimages/aboutus4.jpg'>
                <h3>Governor Chimidzi</h3>
                <p>Sales / Fleet Engineer</p>
            </div>

        </div>
    </section>

    <!-- FEATURES -->
    <section class='grid-section'>
        <div class='card'>🚗 Well Maintained Cars</div>
        <div class='card'>⏱ 24/7 Support</div>
        <div class='card'>💰 Affordable Pricing</div>
        <div class='card'>🛡 Trusted Service</div>
    </section>

    <!-- STATS -->
    <section class='stats-section'>
        <div class='stat-card'>
            <h3>500+</h3>
            <p>Cars</p>
        </div>

        <div class='stat-card'>
            <h3>10K+</h3>
            <p>Customers</p>
        </div>

        <div class='stat-card'>
            <h3>5+</h3>
            <p>Years Experience</p>
        </div>

        <div class='stat-card'>
            <h3>24/7</h3>
            <p>Service</p>
        </div>
    </section>

</div>

<?php
}

else if($page == "services"){
?>

<div class='service-page'>
    <div class='page-header'>
        <h2>Our Services</h2>
        <p>Explore premium travel solutions</p>
    </div>

    <div class='services-grid'>

        <div class='service-card'>
            <img src='admin/vehicleimages/Fortuner.jpg'>
            <h3>Corporate Rentals</h3>
            <p>Professional travel for business executives and companies.</p>

            <!-- ✅ FIXED LINK -->
            <a href="app.php?page=service-detail&type=corporate" class='plus-btn'>+</a>
        </div>

        <div class='service-card'>
            <img src='admin/vehicleimages/rolls royce.jpg'>
            <h3>Event Rentals</h3>
            <p>Luxury cars for weddings, parties and special events.</p>

            <!-- ✅ FIXED LINK -->
            <a href="app.php?page=service-detail&type=event" class='plus-btn'>+</a>
        </div>

        <div class='service-card'>
            <img src='admin/vehicleimages/Airport Limo.jpg'>
            <h3>Airport Service</h3>
            <p>Fast and reliable airport pickup & drop services.</p>

            <!-- ✅ FIXED LINK -->
            <a href="app.php?page=service-detail&type=airport" class='plus-btn'>+</a>
        </div>

    </div>
</div>

<?php
}

else if($page == "service-detail"){

$type = isset($_GET['type']) ? $_GET['type'] : '';

if(empty($type)){
    echo "<h2>No service type specified</h2>";
}
else{

    if ($type == "corporate") {
        $title = "Corporate Rentals";
        $desc = "Premium travel solutions for business professionals";
        $sql = "SELECT * FROM tblvehicles WHERE status = 1 AND (VehicleType = 'Sedan' OR VehicleType = 'SUV')";
    } 
    else if ($type == "event") {
        $title = "Event Rentals";
        $desc = "Luxury cars for weddings, parties and special moments";
        $sql = "SELECT * FROM tblvehicles WHERE status = 1 AND (VehicleType = 'Coupe' OR VehicleType = 'Convertible' OR VehicleType = 'Sedan')";
    } 
    else if ($type == "airport") {
        $title = "Airport Service";
        $desc = "Fast and reliable airport pickup and drop-off";
        $sql = "SELECT * FROM tblvehicles WHERE status = 1 AND (VehicleType = 'SUV' OR VehicleType = 'Sedan')";
    } 
    else {
        echo "<h2>Service not found</h2>";
        return;
    }

    $query = $dbh->prepare($sql);
    $query->execute();
    $cars = $query->fetchAll(PDO::FETCH_OBJ);
?>

<div class='page-header'>
    <h2><?= $title ?></h2>
    <p><?= $desc ?></p>
</div>

<div class='car-grid'>

<?php foreach ($cars as $car): ?>

    <div class='car-card'>
        <img src='cars/<?= $car->Vimage1 ?>'>
        <div class='car-info'>
            <h3><?= $car->VehiclesTitle ?></h3>
            <p><b>Brand:</b> <?= $car->VehiclesBrand ?></p>
            <p><b>Type:</b> <?= $car->VehicleType ?></p>
            <p class='price'>₹<?= $car->PricePerDay ?> / day</p>
            <a href='users/booking.php' class='car-btn'>Book Now</a>
        </div>
    </div>

<?php endforeach; ?>

</div>

<?php
}
}

else if ($page == "blog") {

    echo "
    <div class='page blog-page'>

    <div class='page-header'>
        <h2>Car Rental Blog</h2>
        <p>Tips, travel guides and driving insights</p>
    </div>

    <div class='blog-layout'>

        <div class='blog-sidebar'>
            <h3>Categories</h3>

            <a href='app.php?page=blog'>All</a><br>
            <a href='app.php?page=blog&cat=rental'>Rental Tips</a><br>
            <a href='app.php?page=blog&cat=holiday'>Holiday Advice</a><br>
            <a href='app.php?page=blog&cat=checklist'>Checklist</a><br>
            <a href='app.php?page=blog&cat=travel'>Travel Planning</a><br>
        </div>

        <div class='blog-grid'>
    ";

    $cat = isset($_GET['cat']) ? $_GET['cat'] : 'all';

function showBlog($id, $title, $type, $img, $cat){

    if($cat == 'all' || $cat == $type){

        echo "
        <div class='blog-card $type'>

            <img src='$img'>

            <div class='blog-content'>

                <span class='blog-tag'>".ucfirst($type)."</span>

                <h3>$title</h3>

                <p>Click below to read more...</p>

                <a href='app.php?page=blog-detail&id=$id' class='read-btn'>
                    Read More
                </a>

            </div>

        </div>
        ";
    }
}

    showBlog(1, "How to Choose the Perfect Rental Car", "rental", "admin/vehicleimages/blog1.jpg", $cat);
    showBlog(2, "Car Rental Tips for Holidays", "holiday", "admin/vehicleimages/blog2.jpg", $cat);
    showBlog(3, "Ultimate Rental Checklist", "checklist", "admin/vehicleimages/blog3.jpg", $cat);
    showBlog(4, "Essential Driving Rules", "checklist", "admin/vehicleimages/blog4.jpg", $cat);
    showBlog(5, "Travel Planning with Rental Cars", "travel", "admin/vehicleimages/blog5.jpg", $cat);

    echo "
        </div>
    </div>
    </div>
    ";
}

else if ($page == "blog-detail") {

    $id = isset($_GET['id']) ? $_GET['id'] : 0;

    echo "<div class='page blog-detail-page'>";

    // BLOG 1
    if ($id == 1) {
        echo "
        <h1>How to Choose the Perfect Rental Car</h1>
        <p class='blog-meta'>By Admin • Rental Tips</p>

        <img src='admin/vehicleimages/blog1.jpg' class='blog-main-img'>

        <p>
            Choosing the right rental car depends on your needs. 
            If you are traveling with family, SUVs are best. 
            For city driving, compact cars are more efficient.
        </p>

        <p>
            Always compare pricing, fuel type, and seating capacity 
            before booking your vehicle.
        </p>
        ";
    }

    // BLOG 2
    else if ($id == 2) {
        echo "
        <h1>Car Rental Tips for Holidays</h1>
        <p class='blog-meta'>By Admin • Holiday Advice</p>

        <img src='admin/vehicleimages/blog2.jpg' class='blog-main-img'>

        <p>
            Book your car early during holidays to avoid high prices.
            Always check documents and insurance before renting.
        </p>
        ";
    }

    // BLOG 3
    else if ($id == 3) {
        echo "
        <h1>The Ultimate Car Rental Checklist</h1>
        <p class='blog-meta'>By Admin • Checklist</p>

        <img src='admin/vehicleimages/blog3.jpg' class='blog-main-img'>

        <p>
            Check fuel level, tire condition, and documents before driving.
        </p>
        ";
    }

    // BLOG 4
    else if ($id == 4) {
        echo "
        <h1>Essential Driving Rules</h1>
        <p class='blog-meta'>By Admin • Driving Rules</p>

        <img src='admin/vehicleimages/blog4.jpg' class='blog-main-img'>

        <p>
            Follow speed limits, wear seat belts, and respect traffic laws.
        </p>
        ";
    }

    // BLOG 5
    else if ($id == 5) {
        echo "
        <h1>Travel Planning with Rental Cars</h1>
        <p class='blog-meta'>By Admin • Travel Planning</p>

        <img src='admin/vehicleimages/blog5.jpg' class='blog-main-img'>

        <p>
            Plan routes, fuel stops, and destinations before your journey.
        </p>
        ";
    }

    else {
        echo "<h2>Post not found</h2>";
    }

    echo "</div>";
}

else if($page == "cars"){

    //$sql = "SELECT * FROM tblvehicles WHERE status = 1";
    $sql = "
SELECT v.*, b.BrandName
FROM tblvehicles v
JOIN tblbrands b
ON v.VehiclesBrand = b.id
WHERE v.status = 1
";
    $query = $dbh->prepare($sql);
    $query->execute();
    $cars = $query->fetchAll(PDO::FETCH_OBJ);

    echo "
    <div class='page'>
        <div class='page-header'>
            <h2>Available Cars 🚗</h2>
            <p>Choose from our premium fleet</p>
        </div>

        <div class='car-grid'>
    ";

    if($cars){
        foreach($cars as $car){

            echo "
            <div class='car-card'>

                
                <img src='cars/{$car->Vimage1}' alt='car image' />
                <div class='car-info'>
                <h3>{$car->VehiclesTitle}</h3>
                <p><b>Brand:</b> {$car->BrandName}</p>
                <p><b>Type:</b> {$car->VehicleType}</p>
                <p><b>Fuel:</b> {$car->FuelType}</p>
                <p><b>Seats:</b> {$car->SeatingCapacity}</p>

                <p class='price'>₹{$car->PricePerDay} / day</p>

<a href='users/booking.php?car=<?php echo $car->id; ?>' class='car-btn'>
Book Now
</a>
                </div>  
            </div>
            ";
        }
    } else {
        echo "<p>No cars available</p>";
    }

    echo "
        </div>
    </div>
    ";
}



else if ($page == "shop") {
    $category = $_GET['category'] ?? 'all';

    echo "
    <div class='page shop-page'>

      <!-- Page Header -->
      <div class='page-header'>
        <h2>Shop</h2>
        <p>Browse car accessories & tools</p>
      </div>

      <div class='shop-layout'>

        <!-- Sidebar -->
        <div class='shop-sidebar'>

          <!-- Search -->
          <h3>Search</h3>
          <div class='search-box'>
            <input type='text' id='shopSearch' placeholder='Search Shop...'>
            <button class='search-btn'><i class='fa fa-search'></i></button>
          </div>

          <!-- Categories -->
          <h3>Categories</h3>
          <ul class='category-list'>
          <li><a href='app.php?page=orders'><i class='fa fa-box'></i> My Orders</a></li>
            <li><a href='app.php?page=shop'><i class='fa fa-list'></i> All (15)</a></li>
            <li><a href='app.php?page=shop&category=tools'><i class='fa fa-wrench'></i> Tools & Equipment (5)</a></li>
            <li><a href='app.php?page=shop&category=maintenance'><i class='fa fa-cogs'></i> Car Maintenance (3)</a></li>
            <li><a href='app.php?page=shop&category=accessories'><i class='fa fa-box'></i> Automotive Accessories (8)</a></li>
            <li><a href='app.php?page=shop&category=exterior'><i class='fa fa-car-side'></i> Exterior Accessories (4)</a></li>
            <li><a href='app.php?page=shop&category=interior'><i class='fa fa-chair'></i> Interior Accessories (4)</a></li>
            </ul>


          <!-- Price Filter -->
          <h3>Price Filter</h3>
          <div class='price-filter'>
            <input type='number' id='minPrice' placeholder='Min $0'>
            <input type='number' id='maxPrice' placeholder='Max $250'>
            <button id='applyPrice'>Apply</button>
          </div>

          <!-- Popular Products -->
          <h3>Popular Products</h3>
          <div class='popular-products'>
            <div class='popular-item'>
              <img src='admin/vehicleimages/battery.jpg' alt='Car Battery'>
              <span>Car Battery – $185</span>
            </div>
            <div class='popular-item'>
              <img src='admin/vehicleimages/seatcovers.jpg' alt='Seat Covers'>
              <span>Seat Covers – $199</span>
            </div>
            <div class='popular-item'>
              <img src='admin/vehicleimages/cloths.jpg' alt='Microfiber Cloths'>
              <span>Microfiber Cloths – $20</span>
            </div>
          </div>

        </div> <!-- end shop-sidebar -->

        <!-- RIGHT SIDE (wrapper) -->
            <div class='shop-main'>

        <!-- Product Grid -->
            <div class='shop-header'>

            <div class='results'>
                Showing 9 of 15 Products
            </div>

            <div class='sort'>
                <label for='sort'>Sort by:</label>
                <select id='sort' name='sort'>
                <option value='default'>Default</option>
                <option value='low'>Price: Low to High</option>
                <option value='high'>Price: High to Low</option>
                </select>
            </div>

            </div>
    
          
            <div class='shop-grid'>
          ";

$stmt = $dbh->prepare("SELECT * FROM products WHERE category = :category OR :category = 'all'");
$stmt->execute(['category' => $category]);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "
    <div class='product-card'>
      <div class='product-image'>
        <img src='{$row['image']}' alt='{$row['name']}'>
        <div class='product-icons'>
            <a href='app.php?page=add_to_cart&id={$row['id']}' class='icon'>
              <i class='fa fa-shopping-cart'></i>
            </a>
            <a href='app.php?page=add_to_wishlist&id={$row['id']}' class='icon'>
    <i class='fa fa-heart'></i>
    </a>

<a href='app.php?page=product&id={$row['id']}' class='icon'>
  <i class='fa fa-eye'></i>
</a>

        </div>
      </div>
      <h3>{$row['name']}</h3>
      <div class='product-row'>
        <p class='rating'>⭐ {$row['rating']} ({$row['reviews']} reviews)</p>
        <span class='stock'>✔ In Stock</span>
      </div>
      <div class='product-row'>
        <p class='price'>\${$row['price']} <span class='old-price'>\${$row['old_price']}</span></p>
        <span class='delivery'>🚚 Fast Delivery</span>
      </div>
    </div>";
}


echo "
        </div> <!-- end shop-grid -->
      </div> <!-- end shop-main -->
    </div> <!-- end shop-layout -->
    </div> <!-- end shop-page -->
";
}




else if ($page == "add_to_cart") {
    $id = intval($_GET['id']);

    $stmt = $dbh->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        header("Location: app.php?page=shop");
        exit();
    }

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity']++;
        $_SESSION['flash'] = "<div class='message-success'>Quantity updated in cart!</div>";
    } else {
        $_SESSION['cart'][$id] = [
            'id'       => $product['id'],
            'name'     => $product['name'],
            'price'    => $product['price'],
            'image'    => $product['image'],
            'quantity' => 1
        ];
        $_SESSION['flash'] = "<div class='message-success'>Product added to cart!</div>";
    }

    header("Location: app.php?page=cart");
    exit();
}




else if ($page == "cart") {
    echo "<div class='cart-page'><h2>My Cart 🛒</h2>";

    if (empty($_SESSION['cart'])) {
        echo "<p>Your cart is empty.</p></div>";
    } else {
        echo "<table class='cart-table'>
                <tr>
                  <th>Image</th>
                  <th>Product</th>
                  <th>Price</th>
                  <th>Quantity</th>
                  <th>Subtotal</th>
                </tr>";

        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $subtotal = $item['price'] * $item['quantity'];
            $total += $subtotal;

            echo "<tr>
                    <td><img src='{$item['image']}' alt='{$item['name']}'></td>
                    <td>{$item['name']}</td>
                    <td>\${$item['price']}</td>
                    <td>{$item['quantity']}</td>
                    <td>\${$subtotal}</td>
                  </tr>";
        }

        echo "</table>";

        echo "<div class='cart-summary'>
                <h3>Order Summary</h3>
                <p class='total'>Total: \${$total}</p>
                <div class='payment-options'>
                  <label><input type='radio' name='payment' value='cash'> Cash on Delivery</label>
                  <label><input type='radio' name='payment' value='card'> Pay with Card</label>
                </div>
                <a href='app.php?page=checkout' class='btn-cart'>Proceed to Checkout</a>
              </div>";
        echo "</div>";
    }
}


else if ($page == "checkout") {
    $total = 0;

    echo "<div class='page checkout'>
            <h2>Checkout 💳</h2>";

    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        echo "<p>No items to checkout.</p>";
    } else {
        echo "<h3>Order Summary</h3>";
        echo "<table class='cart-table'>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>";

        foreach ($_SESSION['cart'] as $id => $item) {
            $subtotal = $item['price'] * $item['quantity'];
            $total += $subtotal;

            echo "<tr>
                    <td>{$item['name']}</td>
                    <td>\${$item['price']}</td>
                    <td>{$item['quantity']}</td>
                    <td>\${$subtotal}</td>
                  </tr>";
        }

        echo "<tr>
                <td colspan='3'><b>Total</b></td>
                <td><b>\${$total}</b></td>
              </tr>
              </table>";

        // ✅ Checkout form with payment options
        echo "<form method='POST' action='app.php?page=place_order'>
                <input type='text' name='name' placeholder='Your Name' required><br><br>
                <input type='text' name='address' placeholder='Address' required><br><br>

                <label><input type='radio' name='payment' value='cash' required> Cash on Delivery</label><br>
                <label><input type='radio' name='payment' value='card' required> Pay with Card</label><br>

                <div id='card-fields' style='display:none; margin-top:15px;'>
                    <input type='text' name='card_number' placeholder='Card Number'><br><br>
                    <input type='text' name='expiry' placeholder='MM/YY'><br><br>
                    <input type='text' name='cvv' placeholder='CVV'><br><br>
                </div>

                <button type='submit' class='btn-cart'>Place Order</button>
              </form>";
    }

    echo "</div>";
}





else if ($page == "place_order") {
    echo "<div class='page'>
    <h2>Order Confirmation 🎉</h2>";

    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        echo "<p>No items to place an order.</p>";
    } else {
        $customerName    = $_POST['name'];
        $customerAddress = $_POST['address'];

        // calculate total
        $total = 0;
        foreach ($_SESSION['cart'] as $id => $item) {
            $total += $item['price'] * $item['qty'];
        }

        // ✅ Insert into orders table
        $stmt = $dbh->prepare("INSERT INTO orders (user_id, customer_name, customer_address, total) 
                               VALUES (:user_id, :name, :address, :total)");
        $stmt->execute([
            'user_id' => $_SESSION['user_id'] ?? null, // if logged in
            'name'    => $customerName,
            'address' => $customerAddress,
            'total'   => $total
        ]);

        $orderId = $dbh->lastInsertId();

        // ✅ Insert each cart item into order_items
        $stmtItem = $dbh->prepare("INSERT INTO order_items (order_id, product_id, qty, price) 
                                   VALUES (:order_id, :product_id, :qty, :price)");

        foreach ($_SESSION['cart'] as $id => $item) {
            $stmtItem->execute([
                'order_id'   => $orderId,
                'product_id' => $item['id'],
                'qty'        => $item['qty'],
                'price'      => $item['price']
            ]);
        }

        // clear cart
        unset($_SESSION['cart']);

        echo "
        <p>Thank you, <b>{$customerName}</b>!</p>
        <p>Your order totaling <b>\${$total}</b> has been placed successfully.</p>
        <p>It will be shipped to: <b>{$customerAddress}</b></p>
        <br>
        <a href='app.php?page=orders'>View My Orders</a> | 
        <a href='app.php?page=shop'>Continue Shopping</a>
        ";
    }

    echo "</div>";
}



else if ($page == "orders") {
    echo "<div class='page'>
    <h2>My Orders 📦</h2>";

    $userId = $_SESSION['user_id'] ?? null;

    if (!$userId) {
        echo "<p>Please log in to view your orders.</p>";
    } else {
        // fetch orders for this user
        $stmt = $dbh->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->execute(['user_id' => $userId]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$orders) {
            echo "<p>You have no past orders.</p>";
        } else {
            foreach ($orders as $order) {
                // ✅ compute badge class before echo
                $statusClass = strtolower($order['status']); 

                echo "<div class='order-card'>
                    <h3>Order #{$order['id']} – 
                        <span class='status-badge status-{$statusClass}'>{$order['status']}</span>
                    </h3>
                    <p><b>Date:</b> {$order['created_at']}</p>
                    <p><b>Total:</b> \${$order['total']}</p>
                    <p><b>Shipping to:</b> {$order['customer_address']}</p>";

                if ($order['status'] === 'Pending') {
                echo "<form method='POST' action='app.php?page=cancel_order' style='margin-top:10px;'>
                        <input type='hidden' name='order_id' value='{$order['id']}'>
                        <button type='submit'>Cancel Order</button>
                    </form>";
            } else if ($order['status'] === 'Cancelled') {
                echo "<p style='color:red; font-weight:bold;'>This order has been cancelled.</p>";
            }

                 if ($order['status'] === 'Shipped') {
                echo "<p><a href='app.php?page=track_order&id={$order['id']}'>Track Order</a></p>";
                }


                // fetch items for this order
                $stmtItems = $dbh->prepare("SELECT oi.*, p.name 
                                            FROM order_items oi 
                                            JOIN products p ON oi.product_id = p.id 
                                            WHERE oi.order_id = :order_id");
                $stmtItems->execute(['order_id' => $order['id']]);
                $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

                echo "<table border='1' cellpadding='8'>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>";

                foreach ($items as $item) {
                    $subtotal = $item['price'] * $item['qty'];
                    echo "<tr>
                        <td>{$item['name']}</td>
                        <td>{$item['qty']}</td>
                        <td>\${$item['price']}</td>
                        <td>\${$subtotal}</td>
                    </tr>";
                }

                echo "</table></div><br>";
            }
        }
    }

    echo "</div>";
}


else if ($page == "wishlist") {
    echo "<div class='page'><h2>My Wishlist ❤️</h2>";

    // Show flash message if set
    if (isset($_SESSION['flash'])) {
        echo $_SESSION['flash'];
        unset($_SESSION['flash']); // clear after showing once
    }


    if (!isset($_SESSION['wishlist']) || empty($_SESSION['wishlist'])) {
        echo "<p>Your wishlist is empty.</p>";
    } else {
        echo "<div class='shop-grid'>";
        foreach ($_SESSION['wishlist'] as $item) {
            echo "
            <div class='product-card'>
              <div class='product-image'>
                <img src='{$item['image']}' alt='{$item['name']}'>
              </div>
              <h3>{$item['name']}</h3>
              <p class='price'>\${$item['price']}</p>
              <div class='product-actions'>
<a href='app.php?page=add_to_cart&id={$item['id']}' class='btn-cart'>Add to Cart</a>
                <a href='app.php?page=remove_wishlist&id={$item['id']}' class='btn btn-danger'>Remove</a>
              </div>
            </div>";
        }
        echo "</div>";
    }

    echo "</div>";
}

else if ($page == "add_to_wishlist") {
    $id = intval($_GET['id']);

    $stmt = $dbh->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        header("Location: app.php?page=shop");
        exit();
    }

    if (!isset($_SESSION['wishlist'])) {
        $_SESSION['wishlist'] = [];
    }

  if (isset($_SESSION['wishlist'][$id])) {
    $_SESSION['flash'] = "<div class='message-warning'>Item already in wishlist!</div>";
} else {
    $_SESSION['wishlist'][$id] = [
        'id'    => $product['id'],
        'name'  => $product['name'],
        'price' => $product['price'],
        'image' => $product['image']
    ];
    $_SESSION['flash'] = "<div class='message-success'>Product added to wishlist!</div>";
}

header("Location: app.php?page=wishlist");
exit();

}



else if ($page == "remove_wishlist") {
    $id = intval($_GET['id']);

    if (isset($_SESSION['wishlist'][$id])) {
        unset($_SESSION['wishlist'][$id]);
    }

    header("Location: app.php?page=wishlist");
    exit();
}



else if ($page == "product") {
    $id = intval($_GET['id']);

    $stmt = $dbh->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo "<div class='page'><p>Product not found.</p></div>";
    } else {
        echo "
        <div class='page product-details' style='background:#000; padding:40px; border-radius:12px;'>
          <div style='display:flex; gap:40px; align-items:flex-start;'>

            <!-- LEFT SIDE: Product Image -->
            <div style='flex:1; max-width:350px;'>
              <img src='{$product['image']}' alt='{$product['name']}' 
                   style='width:100%; height:auto; border-radius:8px; border:2px solid #444;'>
            </div>

            <!-- RIGHT SIDE: Product Info -->
            <div style='flex:2; color:#fff;'>
              <p style='font-size:18px; font-weight:bold; color:#ffd700;'>⭐ {$product['rating']} ({$product['reviews']})</p>
              <h2 style='margin:10px 0; color:#0ff;'>{$product['name']}</h2>
              <p style='font-size:22px; color:#0f0;'>
                \${$product['price']} <span style='text-decoration:line-through; color:#aaa;'>\${$product['old_price']}</span>
              </p>
              <p style='color:red; font-weight:bold;'>Limited stock available!</p>

              <!-- Professional description -->
              <p style='margin-top:20px; line-height:1.6;'>
                Upgrade your toolbox with our premium Wrench Set, engineered for precision and durability. 
                Featuring a complete range of sizes, this set adapts to any task with ease. 
                Crafted from high‑grade steel, it delivers superior torque and long‑lasting performance. 
                Ergonomic grips ensure comfort and reduce fatigue, making it the perfect choice for both professionals and DIY enthusiasts.
              </p>
                
              <div style='margin-top:25px;'>
<a href='app.php?page=add_to_cart&id={$product['id']}' class='btn-cart'>Add to Cart</a>
<a href='app.php?page=add_to_wishlist&id={$product['id']}' class='btn-wishlist'>Add to Wishlist</a>

            
</div>
            </div>

          </div>
        </div>";
    }
}
















































else if ($page == "cancel_order") {
    $orderId = $_POST['order_id'];
    $userId  = $_SESSION['user_id'] ?? null;

    if (!$userId) {
        echo "<p>You must be logged in to cancel orders.</p>";
    } else {
        // ✅ Check if order belongs to this user and is still pending
        $stmt = $dbh->prepare("SELECT * FROM orders WHERE id = :id AND user_id = :user_id AND status = 'Pending'");
        $stmt->execute(['id' => $orderId, 'user_id' => $userId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            // ✅ Update status to Cancelled
            $stmtUpdate = $dbh->prepare("UPDATE orders SET status = 'Cancelled' WHERE id = :id");
            $stmtUpdate->execute(['id' => $orderId]);

            echo "<div class='page'>
                    <h2>Order Cancelled ❌</h2>
                    <p>Your order #{$orderId} has been cancelled successfully.</p>
                    <a href='app.php?page=orders'>Back to My Orders</a>
                  </div>";
        } else {
            echo "<p>Order cannot be cancelled. It may already be processed or doesn’t belong to you.</p>";
        }
    }
}


else if ($page == "track_order") {
    $orderId = $_GET['id'];
    $userId  = $_SESSION['user_id'] ?? null;

    echo "<div class='page'>
    <h2>Track Order #{$orderId} 🚚</h2>";

    if (!$userId) {
        echo "<p>Please log in to track your orders.</p>";
    } else {
        // check if order belongs to user
        $stmt = $dbh->prepare("SELECT * FROM orders WHERE id = :id AND user_id = :user_id");
        $stmt->execute(['id' => $orderId, 'user_id' => $userId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            echo "<p>Order not found or doesn’t belong to you.</p>";
        } else {
            // fetch tracking updates
            $stmtTrack = $dbh->prepare("SELECT * FROM order_tracking WHERE order_id = :order_id ORDER BY update_time ASC");
            $stmtTrack->execute(['order_id' => $orderId]);
            $updates = $stmtTrack->fetchAll(PDO::FETCH_ASSOC);

            if (!$updates) {
                echo "<p>No tracking updates yet. Please check back later.</p>";
            } else {
                echo "<ul>";
                foreach ($updates as $update) {
                    echo "<li><b>{$update['update_time']}:</b> {$update['status_update']}</li>";
                }
                echo "</ul>";
            }
        }
    }

    echo "</div>";
}



else{
    echo "<h2>Page not found</h2>";
}

?>
</div>
<script src="js/app.js"></script>
</body>
</html>
