<?php
session_start();

include('includes/config.php');

// If already logged in, respect redirect param if present
if(isset($_SESSION['user_id']))
    {
    if(!empty($_GET['redirect'])){
        header("Location: " . $_GET['redirect']);
    } else {
        header("Location: app.php?page=home"); // default after login
    }
    exit();

    }


/* =========================
   AUTO LOGIN (REMEMBER ME)
========================= *//*
if(isset($_COOKIE['remember_user']) && !isset($_SESSION['user_id'])){

    $id = $_COOKIE['remember_user'];

    $stmt = $dbh->prepare("SELECT id, FullName FROM tblusers WHERE id=:id");
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch(PDO::FETCH_OBJ);

    if($user){
        $_SESSION['user_id'] = $user->id;
        $_SESSION['fullname'] = $user->FullName;

        header("Location: index.php");
        exit();
    }
}*/

/* =========================
   LOGIN PROCESS
========================= */
if(isset($_POST['login'])) {

    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];

    $sql = "SELECT id, FullName, EmailId, Password, twofa_enabled
            FROM tblusers 
            WHERE EmailId = :email AND is_active =1";

    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email);
    $query->execute();

    $user = $query->fetch(PDO::FETCH_OBJ);

    if($user && password_verify($password, $user->Password)) {

        /* =========================
           2FA CHECK
        ========================= */
        if($user->twofa_enabled){

            $otp = rand(100000,999999);
            $expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

            $stmt = $dbh->prepare("UPDATE tblusers SET otp_code=:otp, otp_expiry=:exp WHERE id=:id");
            $stmt->execute([
                ':otp' => $otp,
                ':exp' => $expiry,
                ':id' => $user->id
            ]);

           $_SESSION['temp_user'] = $user->id;
            $_SESSION['otp_email'] = $user->EmailId;
            header("Location: users/verify-otp.php");
            exit();

        } else {

            /* =========================
               LOGIN SUCCESS
            ========================= */

            $_SESSION['user_id']  = $user->id;
            $_SESSION['login']    = $user->EmailId;
            $_SESSION['fullname'] = $user->FullName;

            $userId = $user->id;

            // Remember Me
            if(isset($_POST['remember_me'])){
                setcookie("remember_user", $userId, time() + (86400 * 30), "/");
            }

            // Session tracking
            $dbh->prepare("UPDATE login_activity SET is_current=0 WHERE user_id=?")
                ->execute([$userId]);

            $stmt = $dbh->prepare("
                INSERT INTO login_activity (user_id, device_name, browser, location, is_current) 
                VALUES (?,?,?,?,?)
            ");
            $stmt->execute([$userId, "Chrome", "Chrome", "Ahmedabad", 1]);

            // Redirect
            // Redirect after login
            if(!empty($_GET['redirect'])){
                header("Location: " . $_GET['redirect']);
            } else {
                header("Location: app.php?page=home");
            }
            exit();
            }


            header("Location: /emeraldcarrental/" . $redirect);
            exit();
        }

    } else {
        echo "<script>alert('Invalid Email or Password');</script>";
    }

?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
  
     
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    


  </head>
<body>
<!-- floating icons background -->

   <div class="floating-icons">
        <i class="fas fa-car"></i>
        <i class="fas fa-key"></i>
        <i class="fas fa-lock"></i>
        <i class="fas fa-shield-alt"></i>
        <i class="fas fa-road"></i>
    </div>

<div class="login-box" style="margin-top:100px; max-width:400px;">
    <h3 class="text-center">🔐 Emerald - The Best Services: Your Journey Starts Here</h3>
    <form method="post" class="mt-4" autocomplete="off">

<!-- <div class="form-group mb-3">
    <input type="email" name="email" class="form-control" 
           placeholder="Enter Email" autocomplete="off" value="">
</div>-->

<div class="form-group mb-3">
    <input type="email" name="email" class="form-control" 
           placeholder="Enter Email" autocomplete="off" required
           value="<?php echo isset($_COOKIE['remember_user']) ? htmlspecialchars($_COOKIE['remember_user']) : ''; ?>">
</div>


<div class="form-group mb-3 position-relative">
    <input type="password" name="password" class="form-control" id="pass"
           placeholder="Enter Password" autocomplete="new-password" required value="">
<span class="eye" onclick="toggle('pass',this)">
  <i class="fa-solid fa-eye"></i>
</span>
</div>


            <div class="remember-box">
        <label class="remember-label">
            <input type="checkbox" name="remember_me">
            Remember Me
        </label>
        </div>

        <div class="text-end mt-2">
            <a href="forgot-password.php" style="color:#22d3ee; text-decoration:none;">Forgot Password?</a>
        </div>

        <div class="form-group">
        <input type="submit" name="login" value="Login" class="form-control login-btn">
        </div>

      <div class="form-group mt-2">
     <button type="button" class="form-control cancel-btn" onclick="confirmCancel()">
      Cancel
      </button>


    </div>


            <p>Don't have an account? <a href="signup.php">Register</a></p>
            <p><a href="/emeraldcarrental/admin/admin-login.php">Admin Login</a></p>

        </div>
    </form>
</div>

<!-- Custom Cancel Modal -->
<div id="cancelModal" class="cancel-modal">
  <div class="cancel-content">
    <h4>Are you sure you want to cancel?</h4>
    <div class="modal-actions">
      <button class="btn-yes" onclick="window.location.href='index.php'">Yes</button>
      <button class="btn-no" onclick="closeCancel()">No</button>
    </div>
  </div>
</div>

<script>
function toggle(id, el) {
  const input = document.getElementById(id);
  const icon = el.querySelector("i");

  if (input.type === "password") {
    input.type = "text";
    icon.classList.remove("fa-eye");
    icon.classList.add("fa-eye-slash");
  } else {
    input.type = "password";
    icon.classList.remove("fa-eye-slash");
    icon.classList.add("fa-eye");
  }
}

</script>

<script src="js/login.js"></script>
</body>
</html>


