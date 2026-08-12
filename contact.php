<?php
session_start();
include('includes/config.php');

// ✅ Protect contact page if you want it private
// Protect contact page: only logged-in users can see it
if(!isset($_SESSION['user_id'])){
    header("Location: login.php?redirect=contact.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact Us - Emerald Cars</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
  background: url('admin/vehicleimages/carseats1.jpg') center/cover fixed;
  background-attachment: fixed; /* keeps the image frozen */

      font-family: 'Segoe UI', sans-serif;
    }
    .contact-header {
      background: linear-gradient(90deg, #050e05, red);
      color: #fff;

      text-align: center;
      border-radius: 0 0 40px 40px;
      margin-bottom: 40px;
    }
    .contact-card {
      background: black;
      border-radius: 12px;
      box-shadow: 0 4px 20px white;
      padding: 30px;
      margin-bottom: 30px;
      color:white;
       border : 1px solid white;
    }


.contact-card .form-control::placeholder {
  color: white;                             /* lighter placeholder */
  font-style: italic;
}

.contact-card .form-control:focus {
  background: black;   /* slightly brighter on focus */
  border-color: #006400;                   /* darker emerald border */
  box-shadow: 0 0 10px rgba(0, 168, 107, 0.6);
  outline: none;
}

/* Spacing for each field */
.contact-card .mb-3 {
  margin-bottom: 20px;
  background:black;
}

/* Contact form inputs: Name, Email, Message */
.contact-card input[type="text"],
.contact-card input[type="email"],
.contact-card textarea {
  background: #000;                  /* solid black background */
  border: 1px solid white;         /* emerald border */
  color: #fff;                       /* white text */
  border-radius: 8px;
  padding: 12px 15px;
  transition: all 0.3s ease;
  resize: none;                      /* keep textarea neat */
}

.contact-card input[type="text"]::placeholder,
.contact-card input[type="email"]::placeholder,
.contact-card textarea::placeholder {
  color: white;                       /* lighter placeholder */
  font-style: italic;
}

.contact-card input[type="text"]:focus,
.contact-card input[type="email"]:focus,
.contact-card textarea:focus {
  border-color: #00a86b;
  box-shadow: 0 0 10px rgba(0,168,107,0.6);
  outline: none;
  background: #111;                  /* slightly lighter black on focus */
}

/* spacing between fields */
.contact-card .mb-3 {
  margin-bottom: 20px;
}

    .contact-card h2 {
      color: white;
      margin-bottom: 20px;
    }
    .btn-emerald {
      background: #094b33;
      color: #fff;
      border-radius: 50px;
      padding: 10px 25px;
      font-weight: 600;
      transition: 0.3s;
    }
    .btn-emerald:hover {
      background: #006400;
      transform: scale(1.05);
    }

  </style>
</head>
<body>

<div class="contact-header d-flex align-items-center justify-content-center">
  <!-- Bigger Logo on the left -->
  <img src="admin/vehicleimages/logo.jpg" alt="Emerald Cars Logo" 
       style="height:120px; margin-right:25px; border-radius:12px;">
  
  
       <h1>Contact Emerald Cars</h1>
  
</div>

<div class="container">
  <div class="row">
    <!-- Contact Info -->
    <div class="col-md-5">
      <div class="contact-card">
  <h2>Our Office</h2>

  <p><i class="bi bi-geo-alt-fill text-primary fs-3 me-2"></i> 45 Premium Avenue</p>
  <p><i class="bi bi-telephone-fill text-danger fs-3 me-2"></i> +111-222-333</p>
  <p><i class="bi bi-envelope-fill text-success fs-3 me-2"></i> info@emeraldcars.com</p>
  <p>We’re here to help you 24/7 — reach out anytime.</p>
</div>
</div>

    <!-- Contact Form -->
    <div class="col-md-7">
      <div class="contact-card">
        <h2>Send Us a Message</h2>
        <form action="send_message.php" method="post" autocomplete="off">
          <div class="mb-3">
            <input type="text" name="name" class="form-control" placeholder="Your Name" required>
          </div>
          <div class="mb-3">
            <input type="email" name="email" class="form-control" placeholder="Your Email" required>
          </div>
          <div class="mb-3">
            <textarea name="message" class="form-control" rows="4" placeholder="Your Message" required></textarea>
          </div>
          <button type="submit" class="btn-emerald">Send Message</button>
        </form>
      </div>
    </div>
  </div>

  <div class="text-center mt-4">
    <a href="index.php" class="btn-emerald">← Back to Home</a>
  </div>
</div>

<script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>
</html>
