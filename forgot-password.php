<?php
include('includes/config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';


if(isset($_POST['submit'])){
    $email = $_POST['forgot_email'];

    // Check if user exists
    $sql = "SELECT id FROM tblusers WHERE EmailId = :email";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email',$email,PDO::PARAM_STR);
    $query->execute();

    if($query->rowCount() > 0){
        // Generate token + expiry
        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", strtotime("+30 minutes"));

        // Save token in DB
        $update = "UPDATE tblusers SET reset_token=:token, reset_expiry=:expiry WHERE EmailId=:email";
        $q = $dbh->prepare($update);
        $q->bindParam(':token',$token);
        $q->bindParam(':expiry',$expiry);
        $q->bindParam(':email',$email);
        $q->execute();

        // Build reset link
        $link = "http://yourdomain.com/reset.php?token=".$token;

        // Send email with PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'kudzaichimidzi@gmail.com';
            $mail->Password = 'rccranagrdnfaaoj'; // app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('kudzaichimidzi@gmail.com', 'Emerald Car Rental');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = "Password Reset Request";
            $mail->Body = "
                <h2>Password Reset</h2>
                <p>Hello,</p>
                <p>You requested to reset your password. Click the link below to set a new one:</p>
                <p><a href='".$link."' style='color:#2563eb;font-weight:bold;'>Reset Password</a></p>
                <p>This link will expire in 30 minutes.</p>
                <p>If you didn’t request this, please ignore this email.</p>
                <br>
                <p>— Emerald Car Rental Security Team</p>
            ";

            $mail->send();
            echo "<script>alert('Reset link sent to your email');</script>";
        } catch (Exception $e) {
            echo "<script>alert('Email failed: ".$mail->ErrorInfo."');</script>";
        }
    } else {
        echo "<script>alert('Email not found');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Forgot Password</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: linear-gradient(rgba(0,0,0,0.6),rgba(0,0,0,0.6)),
              url('admin/vehicleimages/forgot password.jpg') no-repeat center center fixed;
  background-size: cover;
  font-family: 'Poppins', sans-serif;
}

.card {
  width: 400px;
  padding: 30px;
  border-radius: 20px;
  background: rgba(255,255,255,0.08);
  backdrop-filter: blur(15px);
  border: 1px solid rgba(255,255,255,0.2);
  box-shadow: 0 8px 32px rgba(0,0,0,0.4);
  color: white;
}

.card h3 {
  text-align: center;
  margin-bottom: 20px;
  font-weight: 600;
}

.form-control {
  background: rgba(255,255,255,0.1);
  border: none;
  color: white;
  border-radius: 12px;
}

.form-control::placeholder {
  color: #ccc;
}

.btn-primary {
  background: #00cc7a;
  border: none;
  border-radius: 12px;
  font-weight: 600;
}

.btn-primary:hover {
  background: #00ff99;
  box-shadow: 0 0 15px rgba(0,255,153,0.5);
}
</style>
</head>
<body>
<div class="card">
  <h3>Forgot Password</h3>
  <form method="post">
    <div class="mb-3">
      <input type="email" name="forgot_email" class="form-control" placeholder="Enter your email" required>
    </div>
    <button type="submit" name="submit" class="btn btn-primary w-100">Send Reset Link</button>
    <a href="login.php" class="btn btn-outline-light w-100 mt-2">Back to Login</a>
  </form>
</div>
</body>
</html>

