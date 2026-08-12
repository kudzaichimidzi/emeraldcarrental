<?php
session_start();
include('../includes/config.php');

if(!isset($_SESSION['alogin']) && !isset($_SESSION['reset_admin'])){
    header('location:login.php');
    exit();
}

$msg = "";
$error = "";

if(isset($_POST['submit'])){
    $current = $_POST['password'];
    $new = $_POST['newpassword'];
    $confirm = $_POST['confirmpassword'];
if(isset($_SESSION['alogin'])){
    $username = $_SESSION['alogin']; // logged in admin
} else {
    $username = "admin"; // default admin username
}
    if($new !== $confirm){
        $error = "New Password and Confirm Password do not match!";
    } else {

        $sql ="SELECT Password FROM admin WHERE UserName=:username";
        $query= $dbh->prepare($sql);
        $query->bindParam(':username',$username,PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if($result && password_verify($current, $result['Password'])){

            $newHash = password_hash($new, PASSWORD_DEFAULT);

            $update = "UPDATE admin SET Password=:newpassword WHERE UserName=:username";
            $q = $dbh->prepare($update);
            $q->bindParam(':newpassword',$newHash,PDO::PARAM_STR);
            $q->bindParam(':username',$username,PDO::PARAM_STR);
            $q->execute();

             // 🔐 VERY IMPORTANT PART (PUT HERE)
    unset($_SESSION['reset_admin']); // remove reset access
    session_destroy(); // logout completely

    header("Location: admin-login.php?msg=reset");
    exit();

        } else {
            $error = "<strong style='color:#ef4444;'>ERROR !!</strong> Current password is incorrect!";
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Change Password</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/change-password.css">


</head>

<body>

<div class="container mt-5">
<div class="row justify-content-center">
<div class="col-md-6">

<div class="card p-4">
<h4 class="text-center mb-3">🔐 Change Password</h4>

<?php if($error){ ?>
<div class="errorWrap"><?php echo $error; ?></div>
<?php } ?>

<?php if($msg){ ?>
<div class="succWrap"><?php echo $msg; ?></div>
<?php } ?>

<form method="post">

<div class="mb-3 position-relative">
<label>Current Password</label>
<input type="password" class="form-control" name="password" id="pass1" required>
<span class="eye" onclick="toggle('pass1',this)">👁</span>
</div>

<div class="mb-3 position-relative">
<label>New Password</label>
<input type="password" class="form-control" name="newpassword" id="pass2" required>
<span class="eye" onclick="toggle('pass2',this)">👁</span>
</div>

<div class="mb-3 position-relative">
<label>Confirm Password</label>
<input type="password" class="form-control" name="confirmpassword" id="pass3" required>
<span class="eye" onclick="toggle('pass3',this)">👁</span>
</div>

<div class="text-center">
<button class="btn btn-primary" name="submit">Save Changes</button>
</div>

<div class="text-center mt-3">
    <a href="index.php" style="color:#22d3ee; text-decoration:none;">
        ← Back to Admin Login
    </a>
</div>

</form>

</div>

</div>
</div>
</div>

<script src="../js/change-password.js"></script>

</body>
</html>