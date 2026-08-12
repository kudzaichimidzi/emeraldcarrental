<?php
session_start();
$codeError = false;
include('../includes/config.php');

// 🔐 hashed admin reset code (generate once using password_hash)
$storedCode = '$2y$10$C57VxOB0z54AWtp4C.TdDONFnq48m3QCo2gVaBv796CnN0el/fYbi';//123

// 🔐 INIT SESSION SECURITY
if(!isset($_SESSION['code_attempts'])){
    $_SESSION['code_attempts'] = 0;
}
if(!isset($_SESSION['code_lock_time'])){
    $_SESSION['code_lock_time'] = 0;
}

if(isset($_POST['login'])){
    $username = isset($_POST['admin_username']) ? trim($_POST['admin_username']) : '';
    $password = isset($_POST['admin_password']) ? $_POST['admin_password'] : '';
    $code = isset($_POST['admin_code']) ? trim($_POST['admin_code']) : '';

    // ⛔ LOCK CHECK (3 attempts → 60 sec block)
    if($_SESSION['code_attempts'] >= 3){
        $elapsed = time() - $_SESSION['code_lock_time'];
        if($elapsed < 60){
            echo "<script>alert('Too many attempts. Try again in 60 seconds');</script>";
            exit();
        } else {
            $_SESSION['code_attempts'] = 0;
        }
    }

    // 🔐 ADMIN CODE RESET FLOW
    if(!empty($code)){
        if(password_verify($code, $storedCode)){
            $_SESSION['code_attempts'] = 0;
            $dbh->query("INSERT INTO admin_logs (action) VALUES ('Admin reset success')");
            $_SESSION['reset_admin'] = true;
            header("Location: changepassword.php");
            exit();
        } 
        else 
            {
            $_SESSION['code_attempts']++;
            $_SESSION['code_lock_time'] = time();
            $dbh->query("INSERT INTO admin_logs (action) VALUES ('Admin reset failed')");
           $codeError = true;
        }
    }

    // 🔑 NORMAL LOGIN FLOW
    if(!empty($username) && !empty($password)){
        $sql = "SELECT * FROM admin WHERE UserName = :username";
        $query = $dbh->prepare($sql);
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);

        if($result && password_verify($password, $result->Password)){
            $_SESSION['alogin'] = $result->UserName;
            $_SESSION['fullname'] = $result->FullName;
            $dbh->query("INSERT INTO admin_logs (action) VALUES ('Admin logged in')");
            header("Location: index.php");
            exit();

        } else {
            echo "<script>alert('Invalid Username or Password');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>

<link rel="stylesheet" href="../css/admin-login.css">
<style>

</style>
</head>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.7.6/lottie.min.js"></script>

<body>
    <div class="glass-overlay"></div>

<div class="container">
  <div class="row align-items-stretch justify-content-center">
    
    <!-- Lottie Animation Column -->
    <div class="col-md-6 d-flex">
      <div id="sideAnimation" class="w-100 h-100"></div>
    </div>
    
    <!-- Form Column -->
    <div class="col-md-6">
      <div class="card p-4 text-center h-100">
        <div id="adminAnimation" style="width:150px; height:150px; margin:0 auto;"></div> 
      <h3 class="mb-3">🔐 Admin Login</h3>
        <?php
        if(isset($_GET['msg']) && $_GET['msg'] == "reset"){
            echo "<p style='color:lightgreen;'>✅ Password changed successfully! Please login.</p>";
        }
        ?>
        <form method="post">

          <!-- Username -->
<div class="input-group mb-3">
  <span class="input-group-text"><i class="bi bi-person"></i></span>
  <input type="text" name="admin_username" class="form-control" placeholder="Username" autocomplete="off" required>
</div>

<div class="input-group mb-3 position-relative">
<span class="input-group-text">
  <i class="fas fa-lock" id="lockIcon"></i>
</span>
  <input type="password" name="admin_password" class="form-control" placeholder="Password" id="pass" autocomplete="new-password" required>
  <span class="eye" onclick="toggle('pass',this)">👁</span>
</div>

<div id="codeBox" class="mb-3" style="display:none;">
  <div class="code-wrapper">
    <span>🔑</span>
<input type="text" name="admin_code" id="codeInput" class="form-control" placeholder="Enter Admin Code">  </div>
</div>
  <!-- 👇 ADD HERE -->
  <p id="codeError" style="color:red; display:none; margin-top:5px;">
    ❌ Invalid Admin Code
  </p>

<a href="#" onclick="showCode()">Forgot Password?</a>
    
<div>
<button class="btn btn-primary w-100" name="login">Login</button>
  </div>        
          <div class="text-center mt-3">
            <a href="../login.php" style="color:#22d3ee; text-decoration:none;">← Back to User Login</a>
          </div>
        </form>
      </div>
    </div>
    
  </div>
</div>

<script>
  const hasError = <?php echo $codeError ? 'true' : 'false'; ?>;
</script>

<script src="../js/admin-login.js"></script>
</body>
</html>
