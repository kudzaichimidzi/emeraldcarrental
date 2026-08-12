<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}


if(!isset($_SESSION['user_id'])){
    header("Location:../login.php");
    exit();
}
include('../includes/config.php');

if(isset($_POST['mark_read'])){
    $stmt = $dbh->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    exit(); // stop page reload
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

function createNotification($dbh, $userId, $message){

    // get user settings
    $sql = "SELECT email_notifications, booking_notifications, promo_notifications 
            FROM tblusers WHERE id = :uid";
    $q = $dbh->prepare($sql);
    $q->bindParam(':uid', $userId);
    $q->execute();
    $settings = $q->fetch(PDO::FETCH_OBJ);

    // 🚨 control logic
    if(!$settings->email_notifications && !$settings->booking_notifications && !$settings->promo_notifications){
        return; // ❌ DO NOT SAVE notification
    }

    // ✅ save notification
    $sql = "INSERT INTO notifications (user_id, message) VALUES (:uid, :msg)";
    $q = $dbh->prepare($sql);
    $q->bindParam(':uid', $userId);
    $q->bindParam(':msg', $message);
    $q->execute();
}

if (isset($_POST['signout_all'])) {

    $stmt = $dbh->prepare("DELETE FROM login_activity WHERE user_id=?");
    $stmt->execute([$_SESSION['user_id']]);

    session_destroy();

    header("Location: ../login.php");
    exit();
}

if(isset($_POST['logout_device'])){

    $sessionId = $_POST['session_id'];

    $stmt = $dbh->prepare("DELETE FROM login_activity WHERE id=? AND user_id=?");
    $stmt->execute([$sessionId, $_SESSION['user_id']]);

    header("Location: profile.php#sessions-section");
    exit();
}


$userId = $_SESSION['user_id'];
$sql = "SELECT * FROM tblusers WHERE id=:id";
$query = $dbh->prepare($sql);
$query->bindParam(':id',$userId);
$query->execute();
$user = $query->fetch(PDO::FETCH_OBJ);


if(isset($_POST['toggle_2fa'])){
    $newStatus = $user->twofa_enabled ? 0 : 1;

    $stmt = $dbh->prepare("UPDATE tblusers SET twofa_enabled=:status WHERE id=:id");
    $stmt->bindParam(':status', $newStatus);
    $stmt->bindParam(':id', $userId);
    $stmt->execute();

    // 🔥 ADD THIS PART
    if($newStatus == 1){
        createNotification($dbh, $userId, "🔐 Two-Factor Authentication enabled");
    } else {
        createNotification($dbh, $userId, "⚠️ Two-Factor Authentication disabled");
    }

    header("Location: profile.php#security");
    exit();
}


// Update profile info
if(isset($_POST['update']))
  {
    $fullname = !empty($_POST['fullname']) ? $_POST['fullname'] : $user->FullName;
    $contact  = !empty($_POST['contact']) ? $_POST['contact'] : $user->ContactNo;
    $address  = !empty($_POST['address']) ? $_POST['address'] : $user->Address;
    $city     = !empty($_POST['city']) ? $_POST['city'] : $user->City;
    $country  = !empty($_POST['country']) ? $_POST['country'] : $user->Country;
    $bio = isset($_POST['bio']) ? $_POST['bio'] : $user->bio;
    $profileImage = $user->profile_image;

    if(!empty($_FILES['profile_image']['name'])){

        $filename = $_FILES['profile_image']['name'];
        $tmpname  = $_FILES['profile_image']['tmp_name'];

        $newname = time() . "_" . $filename;
        $folder = "../uploads/" . $newname;

        $allowed = ['jpg','jpeg','png'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if(in_array($ext, $allowed)){

            if(move_uploaded_file($tmpname, $folder)){
                $profileImage = $newname;
            } else {
                echo "<script>alert('Upload failed');</script>";
            }

        } else {
            echo "<script>alert('Only JPG, JPEG, PNG allowed');</script>";
        }
    }

    $sql = "UPDATE tblusers 
    SET FullName=:fullname, ContactNo=:contact, Address=:address, City=:city, Country=:country, profile_image=:img, bio=:bio, UpdationDate=NOW()
    WHERE id=:id";

    $update = $dbh->prepare($sql);
    $update->bindParam(':fullname',$fullname);
    $update->bindParam(':contact',$contact);
    $update->bindParam(':address',$address);
    $update->bindParam(':city',$city);
    $update->bindParam(':country',$country);
    $update->bindParam(':id',$userId);
    $update->bindParam(':img',$profileImage);
    $update->bindParam(':bio',$bio);
    $update->execute();
    createNotification($dbh, $userId, "✏️ Profile updated successfully");


// REFETCH UPDATED USER DATA
$sql = "SELECT * FROM tblusers WHERE id=:id";
$query = $dbh->prepare($sql);
$query->bindParam(':id',$userId);
$query->execute();
$user = $query->fetch(PDO::FETCH_OBJ);

echo "<script>
window.location='profile.php#home-section';
</script>";

}

// Change password
if(isset($_POST['change_password'])){
    $currentPassword = $_POST['current_password'];
    $newPassword     = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    $sql = "SELECT Password FROM tblusers WHERE id=:id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id',$userId);
    $query->execute();
    $row = $query->fetch(PDO::FETCH_OBJ);

    if(password_verify($currentPassword, $row->Password)){
        if($newPassword === $confirmPassword){
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $updatePwd = $dbh->prepare("UPDATE tblusers SET Password=:pwd, UpdationDate=NOW() WHERE id=:id");
            $updatePwd->bindParam(':pwd',$hashed);
            $updatePwd->bindParam(':id',$userId);

            $updatePwd->execute();
            createNotification($dbh, $userId, "🔑 Your password was changed");
            echo "<script>alert('Password updated successfully'); window.location='profile.php';</script>";
        } else {
            echo "<script>alert('New passwords do not match');</script>";
        }
    } else {
        echo "<script>alert('Current password is incorrect');</script>";
    }
}

if(isset($_POST['save_notifications'])){

    $emailNotif   = isset($_POST['email_notifications']) ? 1 : 0;
    $bookingNotif = isset($_POST['booking_notifications']) ? 1 : 0;
    $promoNotif   = isset($_POST['promo_notifications']) ? 1 : 0;

    $stmt = $dbh->prepare("UPDATE tblusers 
        SET email_notifications=:e, booking_notifications=:b, promo_notifications=:p 
        WHERE id=:id");

    $stmt->bindParam(':e', $emailNotif);
    $stmt->bindParam(':b', $bookingNotif);
    $stmt->bindParam(':p', $promoNotif);
    $stmt->bindParam(':id', $userId);
    createNotification($dbh, $userId, "🔔 Notification preferences updated");
    $stmt->execute();

    // ✅ BEST FIX → reload page
    header("Location: profile.php#notifications-section");
    exit();
}


if(isset($_POST['clear_all'])){
    $stmt = $dbh->prepare("DELETE FROM notifications WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    exit();
}



if(isset($_POST['delete_account'])){

    $password = $_POST['confirm_password'];
    $confirmText = $_POST['confirm_text'];

    // check DELETE text
    if($confirmText !== "DELETE"){
        echo "<script>alert('Please type DELETE to confirm');</script>";
    } else {

        // get password
        $sql = "SELECT Password FROM tblusers WHERE id=:id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id',$userId);
        $query->execute();
        $row = $query->fetch(PDO::FETCH_OBJ);

        if(password_verify($password, $row->Password)){

// 🔥 SOFT DELETE
$stmt = $dbh->prepare("UPDATE tblusers SET is_active=0 WHERE id=?");
$stmt->execute([$userId]);

// ✅ SEND EMAIL FIRST
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'kudzaichimidzi@gmail.com';
    $mail->Password = 'rccranagrdnfaaoj';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('kudzaichimidzi@gmail.com', 'Emerald Car Rental');
    $mail->addAddress($user->EmailId);

    $mail->isHTML(true);
    $mail->Subject = 'Account Deactivated';
    $mail->Body = 'Your account has been successfully deactivated.';

    $mail->send();

} catch (Exception $e) {
    echo "Mailer Error: " . $mail->ErrorInfo;
}

createNotification($dbh, $userId, "⚠️ Your account was deactivated");
// ✅ THEN destroy session + redirect
session_destroy();

echo "<script>
alert('Account deactivated successfully');
window.location='login.php';
</script>";

  }
        
    }
}

// Total bookings
$sql = "SELECT COUNT(*) FROM tblbooking WHERE user_id = :uid";
$q = $dbh->prepare($sql);
$q->bindParam(':uid', $userId);
$q->execute();
$totalBookings = $q->fetchColumn();

// Active
$sql = "SELECT COUNT(*) FROM tblbooking WHERE user_id = :uid AND Status IN (0,1)";
$q = $dbh->prepare($sql);
$q->bindParam(':uid', $userId);
$q->execute();
$activeBookings = $q->fetchColumn();

// Completed
$sql = "SELECT COUNT(*) FROM tblbooking WHERE user_id = :uid AND Status = 2";
$q = $dbh->prepare($sql);
$q->bindParam(':uid', $userId);
$q->execute();
$completedBookings = $q->fetchColumn();

//Recent bookings
$sql = "SELECT * FROM tblbooking WHERE user_id = :uid ORDER BY PostingDate DESC LIMIT 3";
$q = $dbh->prepare($sql);
$q->bindParam(':uid', $userId);
$q->execute();
$recentBookings = $q->fetchAll(PDO::FETCH_OBJ);

// Account + Activity + Security data

// Account Created / Updated already in $user
$createdDate = $user->JoiningDate ?? "N/A";
$updatedDate = $user->UpdationDate ?? "N/A";
$userIdDisplay = $user->id;


// Last booking
$sql = "SELECT MAX(PostingDate) FROM tblbooking WHERE user_id = :uid";
$q = $dbh->prepare($sql);
$q->bindParam(':uid', $userId);
$q->execute();
$lastBooking = $q->fetchColumn();

// Session count
$sql = "SELECT COUNT(*) FROM login_activity WHERE user_id = :uid";
$q = $dbh->prepare($sql);
$q->bindParam(':uid', $userId);
$q->execute();
$totalSessions = $q->fetchColumn();

// Last login
$sql = "SELECT MAX(login_time) FROM login_activity WHERE user_id = :uid";
$q = $dbh->prepare($sql);
$q->bindParam(':uid', $userId);
$q->execute();
$lastLogin = $q->fetchColumn();


// Fetch user sessions
$sql = "SELECT * FROM login_activity WHERE user_id = :uid ORDER BY login_time DESC";
$q = $dbh->prepare($sql);
$q->bindParam(':uid', $userId);
$q->execute();
$sessions = $q->fetchAll(PDO::FETCH_OBJ);


$profileScore = 0;

if(!empty($user->FullName)) $profileScore += 20;
if(!empty($user->EmailId)) $profileScore += 20;
if(!empty($user->ContactNo)) $profileScore += 20;
if(!empty($user->Address)) $profileScore += 20;
if(!empty($user->profile_image)) $profileScore += 20;

// Fetch notifications
$sql = "SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC LIMIT 5";
$q = $dbh->prepare($sql);
$q->bindParam(':uid', $userId);
$q->execute();
$notifications = $q->fetchAll(PDO::FETCH_OBJ);

// Unread count
$sql = "SELECT COUNT(*) FROM notifications WHERE user_id = :uid AND is_read = 0";
$q = $dbh->prepare($sql);
$q->bindParam(':uid', $userId);
$q->execute();
$unreadCount = $q->fetchColumn();


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Settings Layout</title>
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="../css/profile.css">  

</head>
<body>
  <!-- Fixed top bar -->
<div class="topbar">

  <!-- LEFT -->
  <div class="topbar-left">
    <p>My Profile : <?= $user->FullName ?> </p>
  </div>

  <!-- CENTER -->
  <div class="topbar-search">
<input type="text" id="searchBox" placeholder="Search settings..." />
    <div id="searchSuggestions" class="search-suggestions"></div>
<i class="fas fa-search"></i>
  </div>

  <!-- RIGHT -->
  <div class="topbar-right">

    <!-- 🔔 NOTIFICATION -->
    <div class="topbar-icon" onclick="toggleNotif()">
      <i class="fas fa-bell"></i>

      <span class="badge">
        <?= $unreadCount ?>
      </span>

<div id="notifPanel" class="notif-panel">
<button onclick="clearAll()" class="clear-btn">Clear all</button>
<div class="notif-header">
  <span>Notifications</span>
  <button onclick="markAllRead()" class="clear-btn">Mark all as read</button>
</div>

<?php if(!empty($notifications)): ?>
  
  <?php foreach($notifications as $n): ?>
    
<p class="<?= $n->is_read ? 'notif-read' : 'notif-new' ?>">
   <?= $n->message ?>
    </p>

  <?php endforeach; ?>

<?php else: ?>

  <p style="opacity:0.6;">No notifications yet</p>

<?php endif; ?>

</div>

</div>

    <!-- 👤 PROFILE -->
    <div class="topbar-profile" onclick="toggleProfileMenu()">
      <img src="<?= !empty($user->profile_image) ? '../uploads/'.$user->profile_image : '../images/default-user.jpg'; ?>">
      <span><?= $user->FullName ?></span>

      <div id="profileMenu" class="dropdown-menu">
       <a href="/emeraldcarrental/index.php">Main Home</a>

        <a href="#" onclick="showSection('basic-info-section')">Profile</a>
        <a href="#" onclick="showSection('security-section')">Settings</a>
        <a href="/emeraldcarrental/logout.php">Logout</a>
      </div>
    </div>

  </div>

</div>

  <!-- Sidebar -->
  <div class="sidebar-wrapper">
    <div class="sidebar">
      <ul>
        <li class="nav-item active" onclick="showSection('home-section')"><i class="fas fa-home"></i> Home</li>
        <li class="nav-item" onclick="showSection('basic-info-section')"><i class="fas fa-id-card"></i> Basic Info</li>

        <li class="nav-item" onclick="showSection('security-section')">
          <i class="fas fa-shield-alt"></i> Security
        </li>
                <li class="nav-item" onclick="showSection('accounts-section')">
          <i class="fas fa-users"></i> Accounts
        </li>

        <li class="nav-item" onclick="showSection('notifications-section')">
          <i class="fas fa-bell"></i> Notifications
        </li>

          <li class="nav-item" onclick="showSection('privacy-section')">
          <i class="fas fa-lock"></i> Privacy & Data
        </li>

        <li class="nav-item" onclick="showSection('delete-section')">
          <i class="fas fa-trash"></i> Delete Account
        </li>


        </ul>
    </div>
  </div>

 <div class="content">

<div id="home-section" class="section">

  <div class="home-row" style="position:relative;">
      <!-- Profile photo (dynamic or gradient placeholder) -->
      <div class="profile-pic-wrapper">
          <img 
          id="profileImage" 
          src="<?php echo !empty($user->profile_image) ? '../uploads/'.$user->profile_image : '../images/default-user.jpg'; ?>" 
          class="profile-img">

          <div id="profilePlaceholder" class="profile-placeholder" 
            style="<?php echo !empty($user->profile_image) ? 'display:none;' : ''; ?>">
            <i class="fas fa-user"></i>
        </div>
      </div>

 
    <!-- Your existing card stays untouched -->
  <div class="home-card" style="display:flex; justify-content:space-between; align-items:center;">
        
    <div class="avatar">
        <?php 
          if (!empty($user->FullName)) {
            echo strtoupper(substr($user->FullName, 0, 1));
          } else {
            echo "U"; // fallback letter
          }
        ?>

      </div>

      <div class="profile-info">
        
        <h2><?php echo $user->FullName ?? "Unknown User"; ?></h2>
                    <p style="opacity:0.7;">
          Welcome back, <?= $user->FullName ?> 👋
        </p>
        <p><?php echo $user->EmailId ?? "No email available"; ?></p>
      </div>
    </div>
<a href="/emeraldcarrental/index.php" class="top-home-btn">
  <i class="fas fa-home"></i> Home
</a>
  </div>

  <!-- ✅ feature cards now inside Home -->
  <div class="feature-row">
    <div class="feature-card" onclick="showSection('personal-section')">
      <i class="fas fa-user"></i>
      <p>Personal Info</p>
    </div>
    <div class="feature-card" onclick="showSection('security-section')">
      <i class="fas fa-shield-alt"></i>
      <p>Security</p>
    </div>
    <div class="feature-card" onclick="showSection('privacy-section')">
      <i class="fas fa-lock"></i>
      <p>Privacy & Data</p>
    </div>
  </div>

<div class="stats-row">

  <div class="stat-card">
    <i class="fas fa-car"></i>
    <h3><?= $totalBookings ?></h3>
    <p>Total Bookings</p>
  </div>

  <div class="stat-card">
    <i class="fas fa-clock"></i>
    <h3><?= $activeBookings ?></h3>
    <p>Active</p>
  </div>

  <div class="stat-card">
    <i class="fas fa-check-circle"></i>
    <h3><?= $completedBookings ?></h3>
    <p>Completed</p>
  </div>

  <div class="stat-card">
    <i class="fas fa-calendar"></i>
    <h3><?= $lastBooking ?? 'None' ?></h3>
    <p>Last Booking</p>
  </div>

</div>


<div class="chart-row">
  <div class="chart-card">
    <div id="bookingChart"></div>
  </div>
  <div class="chart-card">
    <div id="barChart"></div>
  </div>
  <div class="chart-card">
    <div id="lineChart"></div>
  </div>
</div>


<div class="activity-card">
  <h3><i class="fas fa-history"></i> Recent Activity</h3>

  <div id="bookingList"> <!-- 🔥 ADD THIS -->

    <?php if(!empty($recentBookings)): ?>
      <?php foreach($recentBookings as $b): ?>
        <div class="booking-item">

  <span class="booking-date">
    🚗 <?= $b->PostingDate ?>
  </span>

  <?php
  $statusText = "Pending";
  $statusClass = "pending";

  if($b->Status == 1){
    $statusText = "Approved";
    $statusClass = "approved";
  } elseif($b->Status == 2){
    $statusText = "Cancelled";
    $statusClass = "cancelled";
  }
  ?>

  <span class="status-badge <?= $statusClass ?>">
    <?= $statusText ?>
  </span>

</div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No recent activity</p>
    <?php endif; ?>

  </div> <!-- 🔥 END -->
</div>

<div class="quick-actions">

  <button onclick="window.location='booking.php'">
    🚗 Book Car
  </button>

  <button onclick="showSection('personal-section')">
    ✏️ Update Profile
  </button>

  <button onclick="showSection('security-section')">
    🔐 Security
  </button>

  <button class="btn-home" onclick="window.location='../index.php'">
  🏠 Main Home
</button>

</div>

<div class="about-card">
  <div style="display:flex; justify-content:space-between; align-items:center;">
    

    <p>Tell us about yourself</p>
<i class="fas fa-pen" 
   style="cursor:pointer;" 
   onclick="event.stopPropagation(); openPopup('bioPopup')"></i>
  
  </div>

<p onclick="openPopup('bioPopup')" style="cursor:pointer;">
  <?= !empty($user->bio) 
    ? $user->bio 
    : '<span class="bio-placeholder">Write your bio to tell people about yourself</span>'; ?>
</p>

</div>


<div class="popup" id="bioPopup">
  <div class="popup-content">

    <h3>Edit Bio</h3>

    <form method="post">
      <textarea name="bio" maxlength="150" rows="4" placeholder="Write about yourself..."><?= $user->bio ?></textarea>

      <div class="popup-buttons">
        <button class="btn-save" name="update">Save</button>
        <button type="button" class="btn-cancel" onclick="closePopup('bioPopup')">Cancel</button>
      </div>
    </form>

  </div>
 
</div>





</div>


<!-- Basic Info Section -->
<div id="basic-info-section" class="section" style="display:none;">

  <h2 style="margin-bottom:20px;">📋 Basic Info Dashboard</h2>
     <h3>About Me</h3>
  
  <div class="info-grid">

  <div class="completion-box">
  <h3><i class="fas fa-tasks"></i> Profile Completion</h3>

  
  <div class="progress-bar">
    <div class="progress" style="width: <?= $profileScore ?>%;"></div>
  </div>

  <p><?= $profileScore ?>% completed</p>
</div>

    <!-- 👤 ACCOUNT CARD -->
    <div class="info-card">
      <h3><i class="fas fa-user"></i> Account Info</h3>
      <p><strong>User ID:</strong> <?= $user->id ?></p>
      <p>
  <span class="status-dot"></span> Online
</p>
      <p><strong>Name:</strong> <?= $user->FullName ?></p>
      <p><strong>Email:</strong> <?= $user->EmailId ?></p>
      <p><strong>Phone:</strong> <?= $user->ContactNo ?></p>
      <p><strong>Address:</strong> <?= $user->Address ?></p>
      <p><strong>City:</strong> <?= $user->City ?></p>
      <p><strong>Country:</strong> <?= $user->Country ?></p>
    </div>

    <!-- 📊 ACTIVITY CARD -->
    <div class="info-card">
      <h3><i class="fas fa-chart-bar"></i> Activity</h3>
      <p><strong>Total Bookings:</strong><span id="bookingsCount"><?= $totalBookings ?></span></p>
      <p><strong>Active:</strong> <span id="bookingsCount"><?= $activeBookings ?></span></p>
      <p><strong>Completed:</strong> <span id="bookingsCount"><?= $completedBookings ?></span></p>
      <p><strong>Last Booking:</strong> <span id="bookingsCount"><?= $lastBooking ?></span></p>
    </div>

    <!-- 🔐 SECURITY CARD -->
    <div class="info-card">
      <h3><i class="fas fa-shield-alt"></i> Security</h3>
      <p><strong>2FA:</strong> <?= $user->twofa_enabled ? "Enabled" : "Disabled" ?></p>
      <p><strong>Sessions:</strong> <?= $totalSessions ?></p>
      <p><strong>Last Login:</strong> <?= $lastLogin ?></p>
    </div>

  </div>

</div>


<!-- Personal Info section -->
<div id="personal-section" class="section" style="display:none;">

  <h4>Personal Info</h4>

  <div class="home-row">

    <!-- PROFILE IMAGE -->
<div class="profile-pic-wrapper">
  <div class="avatar-wrapper">

    <?php if(!empty($user->profile_image)): ?>
<img id="profileImage" src="../uploads/<?php echo $user->profile_image; ?>" class="profile-img">      <div class="profile-placeholder">
        <i class="fas fa-user"></i>
      </div>
    <?php endif; ?>

    <!-- FORM ONLY WRAPS INPUT -->
    <form method="post" enctype="multipart/form-data">
<input type="file" name="profile_image" id="imgInput"
       style="display:none;" 
       onchange="previewImage(event); this.form.submit();">
      <input type="hidden" name="update" value="1">
    </form>

    <!-- LABEL OUTSIDE FORM -->
    <label for="imgInput" class="upload-label">
      <i class="fas fa-pencil-alt"></i>
    </label>

  </div>
</div>

  </div>

    <div class="list-group">

  <!-- Name -->
  <div class="list-item" onclick="openPopup('namePopup')">
    <div>
      <i class="fas fa-user"></i>
      <strong>Name</strong><br>
      <small><?php echo $user->FullName; ?></small>
    </div>
    <i class="fas fa-chevron-right"></i>
  </div>

  <!-- Phone -->
  <div class="list-item" onclick="openPopup('phonePopup')">
    <div>
      <i class="fas fa-phone"></i>
      <strong>Phone number</strong><br>
      <small><?php echo $user->ContactNo; ?></small>
    </div>
    <i class="fas fa-chevron-right"></i>
  </div>

  <!-- Email -->
  <div class="list-item" onclick="openPopup('emailPopup')">
    <div>
      <i class="fas fa-envelope"></i>
      <strong>Email</strong><br>
      <small><?php echo $user->EmailId; ?></small>
    </div>
    <i class="fas fa-chevron-right"></i>
  </div>

  <!-- Address -->
  <div class="list-item" onclick="openPopup('addressPopup')">
    <div>
      <i class="fas fa-map-marker-alt"></i>
      <strong>Address</strong><br>
      <small><?php echo $user->Address; ?></small>
    </div>
    <i class="fas fa-chevron-right"></i>
  </div>

</div>


<div class="popup" id="namePopup">
  <div class="popup-content">
    <h3>Edit Name</h3>

    <form method="post">
      <input type="text" name="fullname" value="<?php echo $user->FullName; ?>">

      <div class="popup-buttons">
        <button class="btn-save" name="update">Save</button>
        <button type="button" class="btn-cancel" onclick="closePopup('namePopup')">Cancel</button>
      </div>
    </form>

  </div>
</div>

<div class="popup" id="phonePopup">
  <div class="popup-content">
    <h3>Edit Phone Number</h3>

    <form method="post">
      <input type="text" name="contact" 
             value="<?php echo $user->ContactNo; ?>" 
             placeholder="Enter phone number">

      <div class="popup-buttons">
        <button class="btn-save" name="update">Save</button>
        <button type="button" class="btn-cancel" onclick="closePopup('phonePopup')">Cancel</button>
      </div>
    </form>

  </div>
</div>

<div class="popup" id="emailPopup">
  <div class="popup-content">
    <h3>Email Address</h3>

    <input type="text" 
           value="<?php echo $user->EmailId; ?>" 
           disabled>

    <div class="popup-buttons">
      <button type="button" class="btn-cancel" onclick="closePopup('emailPopup')">Close</button>
    </div>

  </div>
</div>
<div class="popup" id="addressPopup">
  <div class="popup-content">
    <h3>Edit Address</h3>

    <form method="post">
      
      <input type="text" name="address" 
             value="<?php echo $user->Address; ?>" 
             placeholder="Street address">

      <input type="text" name="city" 
             value="<?php echo $user->City; ?>" 
             placeholder="City">

      <input type="text" name="country" 
             value="<?php echo $user->Country; ?>" 
             placeholder="Country">

      <div class="popup-buttons">
        <button class="btn-save" name="update">Save</button>
        <button type="button" class="btn-cancel" onclick="closePopup('addressPopup')">Cancel</button>
      </div>

    </form>

  </div>
    </div>

    </div>
<!-- PASSWORD POPUP (GLOBAL) -->
<div class="popup" id="passwordPopup">
  <div class="popup-content">

    <h3>Change Password</h3>

    <form method="post" autocomplete="off">

      <input type="password" name="current_password" placeholder="Current Password" required>

      <input type="password" id="newPassword" name="new_password" placeholder="New Password" required onkeyup="checkPassword()">

      <input type="password" id="confirmPassword" name="confirm_password" placeholder="Confirm New Password" required onkeyup="checkPassword()">

      <div class="strength-box">
        <div id="strengthBar"></div>
      </div>
      <p id="strengthText"></p>

      <ul class="rules">
        <li id="length">❌ Minimum 6 characters</li>
        <li id="number">❌ At least 1 number</li>
        <li id="special">❌ At least 1 special character</li>
        <li id="match">❌ Passwords match</li>
      </ul>

      <div class="popup-buttons">
        <button class="btn-save" name="change_password">Update</button>
        <button type="button" class="btn-cancel" onclick="closePopup('passwordPopup')">Cancel</button>
      </div>

    </form>
  </div>
</div>

<!-- Security Section -->
<div id="security-section" class="section" style="display:none;">

  <h2>Security</h2>

  <div class="list-group">

    <!-- Change Password -->
    <div class="list-item" onclick="openPasswordPopup()">
      <div>
        <i class="fas fa-lock"></i>
        <strong>Change Password</strong><br>
        <small>Update your account password</small>
      </div>
      <i class="fas fa-chevron-right"></i>
    </div>

    <!-- 2FA -->
<!-- 2FA -->
<div class="list-item">
  <div>
    <i class="fas fa-shield-alt"></i>
    <strong>Two-Factor Authentication</strong><br>
    <small><?php echo $user->twofa_enabled ? "Enabled" : "Disabled"; ?></small>
  </div>

  <form method="post">
    <button class="btn-save" name="toggle_2fa">
      <?php echo $user->twofa_enabled ? "Disable" : "Enable"; ?>
    </button>
  </form>
</div>

    <!-- Sessions -->
    <div class="list-item" onclick="showSection('sessions-section')">
      <div>
        <i class="fas fa-history"></i>
        <strong>Active Sessions</strong><br>
        <small>See where you are logged in</small>
      </div>
      <i class="fas fa-chevron-right"></i>
    </div>

    <!-- Sign out all -->
    <div class="list-item">
      <div>
        <i class="fas fa-sign-out-alt"></i>
        <strong>Sign out from all devices</strong>
      </div>

      <form method="post">
        <button class="btn-save" name="signout_all">Sign Out</button>
      </form>
    </div>

  </div>

</div>

<div id="sessions-section" class="section" style="display:none;">

  <h2>Active Sessions</h2>

  <div class="list-group">

    <?php foreach($sessions as $session): ?>

    <div class="list-item">
      <div>
        <i class="fas fa-laptop"></i>
        <strong><?php echo $session->device ?? "Unknown Device"; ?></strong><br>
        <small><?php echo $session->login_time; ?></small>
      </div>

      <?php if($session->is_current == 0): ?>
        <form method="post">
          <input type="hidden" name="session_id" value="<?php echo $session->id; ?>">
          <button class="btn-cancel" name="logout_device">Logout</button>
        </form>
      <?php else: ?>
        <span style="color:lightgreen;">Current</span>
      <?php endif; ?>

    </div>

    <?php endforeach; ?>

  </div>

</div>


<div id="accounts-section" class="section" style="display:none;">

  <h2>Account Overview</h2>

  <div class="card-container">

    <!-- ACCOUNT INFO -->
    <div class="info-card">
      <h4><i class="fas fa-user"></i> Account Info</h4>
      <p><strong>User ID:</strong> <?= $user->id ?></p>
      <p><strong>Status:</strong> <span style="color:lightgreen;">Active</span></p>
      <p><strong>Email:</strong> <?= $user->EmailId ?></p>
    </div>

    <!-- TIMELINE -->
    <div class="info-card">
      <h4><i class="fas fa-clock"></i> Timeline</h4>
      <p><strong>Last Updated:</strong> <?= $user->UpdationDate ?></p>
      <p><strong>Joined:</strong> <?= $user->RegDate ?? 'N/A' ?></p>
    </div>

    <!-- ACTIVITY -->
    <div class="info-card">
      <h4><i class="fas fa-chart-line"></i> Activity</h4>
      <p><strong>Total Bookings:</strong> <?= $totalBookings ?></p>
      <p><strong>Last Booking:</strong> <?= $lastBooking ?? 'None' ?></p>
    </div>

    <!-- SESSIONS -->
    <div class="info-card">
      <h4><i class="fas fa-laptop"></i> Sessions</h4>
      <p><strong>Total Sessions:</strong> <?= $totalSessions ?></p>
      <p><strong>Last Login:</strong> <?= $lastLogin ?></p>
    </div>

  </div>

</div>

<div id="notifications-section" class="section" style="display:none;">

  <h2>Notifications</h2>

  <form method="post">

    <div class="card-container">

      <div class="info-card">
        <h4><i class="fas fa-envelope"></i> Email Notifications</h4>
        <label class="switch">
          <input type="checkbox" name="email_notifications" 
            <?= $user->email_notifications ? 'checked' : '' ?>>
          <span class="slider"></span>
        </label>
      </div>

      <div class="info-card">
        <h4><i class="fas fa-car"></i> Booking Updates</h4>
        <label class="switch">
          <input type="checkbox" name="booking_notifications" 
            <?= $user->booking_notifications ? 'checked' : '' ?>>
          <span class="slider"></span>
        </label>
      </div>

      <div class="info-card">
        <h4><i class="fas fa-bullhorn"></i> Promotions</h4>
        <label class="switch">
          <input type="checkbox" name="promo_notifications" 
            <?= $user->promo_notifications ? 'checked' : '' ?>>
          <span class="slider"></span>
        </label>
      </div>

    </div>

    <br>
    <button class="btn-save" name="save_notifications">
      Save Changes
    </button>

  </form>

</div>


<div id="delete-section" class="section" style="display:none;">

  <h2>Delete Account</h2>

  <div class="danger-box">
    <p>⚠️ This action is permanent. All your data will be deleted.</p>

    <button class="btn-danger" onclick="openDeleteModal()">
      Delete My Account
    </button>
  </div>

</div>

<div id="deleteModal" class="modal">
  <div class="modal-content">

    <h3>Confirm Account Deletion</h3>
    <p>⚠️This cannot be undone.</p>

    <form method="post">

    <input type="password" name="confirm_password" 
          class="delete-input"
          placeholder="Enter your password" required>

    <input type="text" name="confirm_text" 
          class="delete-input"
          placeholder="Type DELETE to confirm" required>

    <div class="modal-actions">
      <button class="btn-danger delete-btn" name="delete_account">Delete</button>
      <button type="button" class="btn-cancel cancel-btn" onclick="closeDeleteModal()">Cancel</button>
    </div>

  </form>

  </div>
</div>

<div id="privacy-section" class="section" style="display:none;">

  <h2>Privacy & Data</h2>

  <div class="list-group">

    <!-- View Data -->
    <div class="list-item" onclick="showSection('basic-info-section')">
      <div>
        <i class="fas fa-user"></i>
        <strong>View My Data</strong><br>
        <small>See all information stored about you</small>
      </div>
      <i class="fas fa-chevron-right"></i>
    </div>

    <!-- Download Data -->
    <div class="list-item" onclick="window.location='download_data_pdf.php'">
          <div>
        <i class="fas fa-download"></i>
        <strong>Download My Data</strong><br>
        <small>Get a copy of your data</small>
      </div>
      <i class="fas fa-chevron-right"></i>
    </div>

    <!-- Privacy Controls -->
    <div class="list-item" onclick="showSection('notifications-section')">
      <div>
        <i class="fas fa-sliders-h"></i>
        <strong>Privacy Controls</strong><br>
        <small>Manage your preferences</small>
      </div>
      <i class="fas fa-chevron-right"></i>
    </div>

    <!-- Activity -->
    <div class="list-item" onclick="showSection('sessions-section')">
      <div>
        <i class="fas fa-history"></i>
        <strong>Activity & Sessions</strong><br>
        <small>View login activity</small>
      </div>
      <i class="fas fa-chevron-right"></i>
    </div>

    <!-- Delete -->
    <div class="list-item" onclick="showSection('delete-section')">
      <div>
        <i class="fas fa-trash"></i>
        <strong>Delete My Data</strong><br>
        <small>Permanently remove your account</small>
      </div>
      <i class="fas fa-chevron-right"></i>
    </div>

  </div>

  <!-- Privacy Policy -->
  <div class="info-card" style="margin-top:20px;">
    <h4><i class="fas fa-file-alt"></i> Privacy Policy</h4>
    <p>
      We collect your data to manage bookings and improve your experience.
      Your data is private and not shared with third parties.
    </p>
  </div>

</div>

<script>
var options = {
  chart: {
    type: 'donut',
    height: 300
  },
  series: [<?= $activeBookings ?>, <?= $completedBookings ?>],
  labels: ['Active', 'Completed'],
  colors: ['#4db8ff', '#00e676'],
  legend: {
    position: 'bottom'
  }
};

var chart = new ApexCharts(document.querySelector("#bookingChart"), options);
chart.render();
</script>

<script>
var options = {
  chart: {
    type: 'bar',
    height: 300
  },
  series: [{
    name: 'Bookings',
    data: [5, 10, 8, 15, 7, 12] // replace with PHP later
  }],
  xaxis: {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
  }
};

new ApexCharts(document.querySelector("#barChart"), options).render();
</script>

<script>
var options = {
  chart: {
    type: 'line',
    height: 300
  },
  series: [{
    name: 'Bookings',
    data: [3, 7, 5, 12, 9, 14]
  }],
  xaxis: {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
  }
};

new ApexCharts(document.querySelector("#lineChart"), options).render();
</script>

<script src="../js/profile.js"></script>

<script>
  let totalBookingsValue = <?= $totalBookings ?>;
</script>
</body>
</html>
