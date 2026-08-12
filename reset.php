<?php
include('includes/config.php');

if(isset($_GET['token'])){
    $token = $_GET['token'];

    // Verify token
    $sql = "SELECT EmailId, reset_expiry FROM tblusers WHERE reset_token=:token";
    $query = $dbh->prepare($sql);
    $query->bindParam(':token',$token);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if($user && strtotime($user['reset_expiry']) > time()){
        if(isset($_POST['submit'])){
            $new = $_POST['newpassword'];
            $confirm = $_POST['confirmpassword'];

            if($new === $confirm){
                $newHash = password_hash($new, PASSWORD_DEFAULT);

                $update = "UPDATE tblusers 
                           SET Password=:pass, reset_token=NULL, reset_expiry=NULL 
                           WHERE reset_token=:token";
                $q = $dbh->prepare($update);
                $q->bindParam(':pass',$newHash);
                $q->bindParam(':token',$token);
                $q->execute();

                echo "<script>alert('Password reset successful'); window.location='login.php';</script>";
            } else {
                echo "<script>alert('Passwords do not match');</script>";
            }
        }
    } else {
        echo "<script>alert('Invalid or expired token');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reset Password</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: linear-gradient(rgba(0,0,0,0.6),rgba(0,0,0,0.6)),
              url('admin/vehicleimages/rolls royce.jpg') no-repeat center center fixed;
  background-size: cover;
  font-family: 'Poppins', sans-serif;
}

.card {
  width: 420px;
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

.eye {
  position: absolute;
  right: 15px;
  top: 10px;
  cursor: pointer;
  color: #ccc;
}

.btn-primary {
  background: #0c1a38;
  border: none;
  border-radius: 12px;
  font-weight: 600;
}

.btn-primary:hover {
  background: green;
  box-shadow: 0 0 15px rgba(37,99,235,0.5);
}
</style>
</head>
<body>
<div class="card">
  <h3>Reset Password</h3>
  <form method="post">
    <div class="mb-3 position-relative">
      <input type="password" name="newpassword" class="form-control" placeholder="New Password" id="pass1" required>
      <span class="eye" onclick="toggle('pass1',this)">👁</span>
      <div id="passwordHelp" class="mt-1"></div>
    </div>
    <div class="mb-3 position-relative">
      <input type="password" name="confirmpassword" class="form-control" placeholder="Confirm Password" id="pass2" required>
      <span class="eye" onclick="toggle('pass2',this)">👁</span>
    </div>
    <button type="submit" name="submit" class="btn btn-primary w-100">Reset Password</button>
    <a href="login.php" class="btn btn-outline-light w-100 mt-2">Back to Login</a>
  </form>
</div>

<script>
function toggle(id, el){
    let input = document.getElementById(id);
    if(input.type === "password"){
        input.type = "text";
        el.innerText = "🙈";
    } else {
        input.type = "password";
        el.innerText = "👁";
    }
}

document.addEventListener("DOMContentLoaded", function() {
  const pass1 = document.getElementById("pass1");
  const help = document.getElementById("passwordHelp");

  pass1.addEventListener("input", function() {
    const val = this.value;
    if (val.length < 8) {
      help.textContent = "Password too short (min 8 chars)";
      help.style.color = "red";
    } else if (!/[A-Z]/.test(val) || !/[0-9]/.test(val) || !/[!@#$%^&*]/.test(val)) {
      help.textContent = "Add uppercase, number & symbol for stronger password";
      help.style.color = "orange";
    } else {
      help.textContent = "Strong enough ✔";
      help.style.color = "lightgreen";
    }
  });
});
</script>
</body>
</html>
