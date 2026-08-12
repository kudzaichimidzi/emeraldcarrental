<?php

session_start();

include('../includes/config.php');


if(empty($_SESSION['alogin'])){

    header("Location: login.php");

    exit();

}


$message = "";
$error = "";


/* =========================
   ADMIN INFORMATION
========================= */

$username = $_SESSION['alogin'];

$adminQuery = $dbh->prepare("

SELECT *

FROM admin

WHERE UserName = :username

LIMIT 1

");

$adminQuery->bindParam(':username',$username);

$adminQuery->execute();

$admin = $adminQuery->fetch(PDO::FETCH_OBJ);


/* =========================
   CHANGE PASSWORD
========================= */

if(isset($_POST['change_password'])){

    $currentPassword = $_POST['current_password'];

    $newPassword = $_POST['new_password'];

    $confirmPassword = $_POST['confirm_password'];


    if(empty($currentPassword) ||
       empty($newPassword) ||
       empty($confirmPassword)){

        $error = "Please fill in all password fields.";

    }

    elseif($newPassword != $confirmPassword){

        $error = "New passwords do not match.";

    }

    elseif(strlen($newPassword) < 6){

        $error = "Password must be at least 6 characters.";

    }

    elseif(!password_verify($currentPassword,$admin->Password)){

        $error = "Current password is incorrect.";

    }

    else{

        $newHash = password_hash(
            $newPassword,
            PASSWORD_DEFAULT
        );


       $update = $dbh->prepare("

        UPDATE admin

        SET Password = :password

        WHERE id = :id

        ");


        $update->bindParam(
            ':password',
            $newHash
        );


        $update->bindParam(
            ':id',
            $admin->id,
            PDO::PARAM_INT
        );


        if($update->execute()){

            $message = "Password changed successfully.";

        }

        else{

            $error = "Unable to change password.";

        }

    }

}

?>


<!DOCTYPE html>

<html lang="en">


<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>

Emerald Car Rental - Settings

</title>


<style>


*{

    box-sizing: border-box;

}


body{

    margin: 0;

    background: #0b1220;

    color: white;

    font-family: Arial, sans-serif;

}


a{

    text-decoration: none;

}


/* =========================
   SIDEBAR
========================= */


.sidebar{

    position: fixed;

    left: 0;

    top: 0;

    width: 230px;

    height: 100vh;

    background: #111827;

    border-right: 1px solid #1f2937;

    padding: 25px 15px;

}


.sidebar h2{

    text-align: center;

    margin-bottom: 35px;

}


.menu-title{

    color: #6b7280;

    font-size: 12px;

    padding-left: 15px;

    margin-bottom: 10px;

}


.sidebar a{

    display: block;

    padding: 13px 15px;

    margin-bottom: 5px;

    color: #9ca3af;

    border-radius: 8px;

}


.sidebar a:hover{

    background: #1f2937;

    color: white;

}


.sidebar a.active{

    background: #2563eb;

    color: white;

}


.sidebar .logout{

    margin-top: 30px;

    color: #ef4444;

}


/* =========================
   MAIN
========================= */


.main{

    margin-left: 230px;

    padding: 30px;

}


.header{

    margin-bottom: 30px;

}


.header h1{

    margin: 0;

}


.header p{

    color: #9ca3af;

}


/* =========================
   GRID
========================= */


.settings-grid{

    display: grid;

    grid-template-columns:
    repeat(2,1fr);

    gap: 25px;

}


/* =========================
   CARD
========================= */


.card{

    background: #111827;

    border: 1px solid #1f2937;

    border-radius: 15px;

    padding: 25px;

}


.card h2{

    margin-top: 0;

    margin-bottom: 20px;

}


/* =========================
   PROFILE
========================= */


.profile-icon{

    width: 75px;

    height: 75px;

    border-radius: 50%;

    background: #2563eb;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 32px;

    margin-bottom: 20px;

}


.profile-row{

    padding: 12px 0;

    border-bottom: 1px solid #1f2937;

}


.profile-row:last-child{

    border-bottom: none;

}


.profile-label{

    color: #6b7280;

    font-size: 13px;

    margin-bottom: 5px;

}


/* =========================
   FORM
========================= */


.form-group{

    margin-bottom: 18px;

}


.form-group label{

    display: block;

    margin-bottom: 8px;

    color: #d1d5db;

    font-size: 14px;

}


.form-group input{

    width: 100%;

    padding: 12px;

    background: #0b1220;

    border: 1px solid #374151;

    border-radius: 8px;

    color: white;

    outline: none;

}


.form-group input:focus{

    border-color: #2563eb;

}


.button{

    width: 100%;

    padding: 12px;

    background: #2563eb;

    border: none;

    border-radius: 8px;

    color: white;

    font-size: 15px;

    cursor: pointer;

}


.button:hover{

    background: #1d4ed8;

}


/* =========================
   MESSAGE
========================= */


.success{

    background: #14532d;

    color: #86efac;

    padding: 12px;

    border-radius: 8px;

    margin-bottom: 20px;

}


.error{

    background: #7f1d1d;

    color: #fca5a5;

    padding: 12px;

    border-radius: 8px;

    margin-bottom: 20px;

}


/* =========================
   COMPANY
========================= */


.company-item{

    padding: 13px 0;

    border-bottom: 1px solid #1f2937;

}


.company-item:last-child{

    border-bottom: none;

}


.company-item strong{

    display: block;

    margin-bottom: 5px;

}


.company-item span{

    color: #9ca3af;

}


/* =========================
   RESPONSIVE
========================= */


@media(max-width:900px){

    .settings-grid{

        grid-template-columns: 1fr;

    }

}


@media(max-width:700px){

    .sidebar{

        position: static;

        width: 100%;

        height: auto;

    }


    .main{

        margin-left: 0;

        padding: 15px;

    }

}


</style>

</head>


<body>


<!-- =========================
     SIDEBAR
========================= -->


<div class="sidebar">


<h2>

Emerald Cars

</h2>


<p class="menu-title">

MAIN MENU

</p>


<a href="index.php">

Dashboard

</a>


<a href="manage-booking.php">

Bookings

</a>


<a href="manage-vehicles.php">

Vehicles

</a>


<a href="clients.php">

Clients

</a>


<a href="payments.php">

Payments

</a>


<a href="reports.php">

Reports

</a>


<a href="settings.php" class="active">

Settings

</a>


<a href="logout.php" class="logout">

Logout

</a>


</div>



<!-- =========================
     MAIN
========================= -->


<div class="main">


<div class="header">


<h1>

⚙️ Settings

</h1>


<p>

Manage your administrator account and company information.

</p>


</div>


<?php

if(!empty($message)){

    echo "

    <div class='success'>

    ✅ ".$message."

    </div>

    ";

}


if(!empty($error)){

    echo "

    <div class='error'>

    ❌ ".$error."

    </div>

    ";

}

?>


<div class="settings-grid">



<!-- =========================
     ADMIN PROFILE
========================= -->


<div class="card">


<h2>

👤 Administrator Profile

</h2>


<div class="profile-icon">

👤

</div>


<div class="profile-row">


<div class="profile-label">

Username

</div>


<strong>

<?php

echo htmlentities(
    $admin->UserName
);

?>

</strong>


</div>


<div class="profile-row">


<div class="profile-label">

Full Name

</div>


<strong>

<?php

echo htmlentities(
    $admin->FullName
);

?>

</strong>


</div>


<div class="profile-row">


<div class="profile-label">

Account Type

</div>


<strong>

Administrator

</strong>


</div>


<div class="profile-row">


<div class="profile-label">

Last Updated

</div>


<strong>

<?php

echo htmlentities(
    $admin->updationDate
);

?>

</strong>


</div>


</div>



<!-- =========================
     CHANGE PASSWORD
========================= -->


<div class="card">


<h2>

🔐 Change Password

</h2>


<form method="POST">


<div class="form-group">


<label>

Current Password

</label>


<input

type="password"

name="current_password"

placeholder="Enter current password"

>


</div>


<div class="form-group">


<label>

New Password

</label>


<input

type="password"

name="new_password"

placeholder="Enter new password"

>


</div>


<div class="form-group">


<label>

Confirm New Password

</label>


<input

type="password"

name="confirm_password"

placeholder="Confirm new password"

>


</div>


<button

type="submit"

name="change_password"

class="button"

>

🔒 Change Password

</button>


</form>


</div>



<!-- =========================
     COMPANY INFORMATION
========================= -->


<div class="card">


<h2>

🚗 Company Information

</h2>


<div class="company-item">


<strong>

Company Name

</strong>


<span>

Emerald Car Rental

</span>


</div>


<div class="company-item">


<strong>

Business Type

</strong>


<span>

Car Rental Service

</span>


</div>


<div class="company-item">


<strong>

Service

</strong>


<span>

Self-Drive Car Rental

</span>


</div>


<div class="company-item">


<strong>

Currency

</strong>


<span>

USD ($)

</span>


</div>


</div>



<!-- =========================
     SYSTEM INFORMATION
========================= -->


<div class="card">


<h2>

🛡️ System Information

</h2>


<div class="company-item">


<strong>

Admin Panel

</strong>


<span>

Emerald Car Rental Administration

</span>


</div>


<div class="company-item">


<strong>

Database

</strong>


<span>

emeraldcarrental

</span>


</div>


<div class="company-item">


<strong>

System Status

</strong>


<span style="color:#22c55e;">

● Online

</span>


</div>


<div class="company-item">


<strong>

Session

</strong>


<span>

Administrator logged in

</span>


</div>


</div>


</div>


</div>


</body>

</html>