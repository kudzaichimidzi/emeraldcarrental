<?php
session_start();
include('includes/config.php');

if(isset($_POST['signup']))
{
    $fullname = $_POST['fullname'];
    $mobileno = $_POST['mobileno'];
    $email = $_POST['emailid'];
    $plainpassword = $_POST['password'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $country = $_POST['country'];



if($fullname=="" || $email=="" || $plainpassword=="")
    {
        echo "<script>alert('All fields required');</script>";
    }
    else
    {

      // 🔎 Check if email already exists
        $check = $dbh->prepare("SELECT id FROM tblusers WHERE EmailId = :email");
        $check->bindParam(':email', $email);
        $check->execute();

        if($check->rowCount() > 0){
            echo "<script>alert('Email already registered');</script>";
        } else {

        $sql = "INSERT INTO tblusers 
        (FullName, EmailId, Password, ContactNo, dob, Address, City, Country, RegDate) 
        VALUES 
        (:fullname, :email, :password, :contact, :dob, :address, :city, :country, NOW())";

        $query = $dbh->prepare($sql);

        $query->bindParam(':fullname',$fullname);
        $query->bindParam(':email',$email);
        $query->bindParam(':password',$password);
        $query->bindParam(':contact',$mobileno);
        $query->bindParam(':dob',$dob);
        $query->bindParam(':address',$address);
        $query->bindParam(':city',$city);
        $query->bindParam(':country',$country);


        if($query->execute())
        {
            echo "<script>alert('Registration Successful');</script>";
            echo "<script>window.location='login.php';</script>";
        }
        else
        {
            echo "<script>alert('Something went wrong');</script>";
        }
    }
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:
    linear-gradient(rgba(0,0,0,0.55),rgba(0,0,0,0.55)),
    url('https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?q=80&w=1600&auto=format&fit=crop');
    background-size:cover;
    background-position:center;
    overflow:hidden;
}

/* glowing circles */
body::before{
    content:"";
    position:absolute;
    width:300px;
    height:300px;
    background:#00ff99;
    border-radius:50%;
    top:-100px;
    left:-100px;
    filter:blur(120px);
    opacity:0.4;
}

body::after{
    content:"";
    position:absolute;
    width:250px;
    height:250px;
    background:#00c3ff;
    border-radius:50%;
    bottom:-100px;
    right:-100px;
    filter:blur(120px);
    opacity:0.3;
}

/* glass card */
.form-container{
    width:700px;
    padding:35px;
    border-radius:25px;
    background:rgba(255,255,255,0.08);
    backdrop-filter:blur(20px);
    border:2px solid white;
    box-shadow:0 8px 32px rgba(0,0,0,0.4);
    color:white;
    position:relative;
    z-index:2;
}

.form-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:18px;
}

@media(max-width:700px){

    .form-grid{
        grid-template-columns:1fr;
    }

    .form-container{
        width:92%;
    }

}
/* heading */
.form-container h2{
    font-size:32px;
    margin-bottom:8px;
    text-align:center;
}

.form-container p{
    color:white;
    margin-bottom:28px;
    font-size:14px;
    text-align:center;
}

/* input box */
.input-box{
    position:relative;
    margin-bottom:18px;
    color:white;
}

.input-box input{
    width:100%;
    padding:15px 18px;
    border:none;
    outline:none;
    border-radius:14px;
    background:rgba(255,255,255,0.08);
    border:1px solid rgba(255,255,255,0.1);
    color:white;
    font-size:14px;
    transition:0.3s;
}

.input-box input::placeholder{
    color:white;
}

.input-box input:focus{
    border-color:#00ff99;
    box-shadow:0 0 10px green;
}

/* password eye */
.password-box{
    position:relative;
}

.eye{
    position:absolute;
    right:18px;
    top:50%;
    transform:translateY(-50%);
    cursor:pointer;
    font-size:18px;
}

/* button */
.create-btn{
    width:100%;
    padding:15px;
    border:none;
    border-radius:14px;
    background:#00cc7a;
    color:white;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
    margin-top:10px;
    transition:0.3s;
}

.create-btn:hover{
    background:#00ff99;
    transform:translateY(-2px);
    box-shadow:0 0 18px rgba(0,255,153,0.5);
}

/* login text */
.login-text{
    text-align:center;
    margin-top:20px;
    font-size:14px;
    color:#ddd;
}

.login-text a{
    color:#00ff99;
    text-decoration:none;
    font-weight:600;
}

.login-text a:hover{
    text-decoration:underline;
}

/* mobile */
@media(max-width:500px){

    .form-container{
        width:92%;
        padding:25px;
    }

    .form-container h2{
        font-size:26px;
    }

}

</style>
</head>
<body>

<div class="form-container">

    <h2>🔐 Create Account</h2>
    <p>Join Emerald Car Services</p>

<form method="post" autocomplete="new-password">

    <div class="form-grid">

        <div class="input-box">
            <input type="text" 
                   name="fullname"
                   placeholder="Full Name" autocomplete="off" required>
        </div>

        <div class="input-box">
            <input type="text" 
                   name="mobileno"
                   placeholder="Mobile Number" autocomplete="off"  required>
        </div>

        <div class="input-box">
            <input type="email" 
       name="emailid"
       placeholder="Email"
       autocomplete="new-password"
       required>
        </div>

        <div class="input-box password-box">
<input type="password" 
       name="password"
       placeholder="Password"
       autocomplete="new-password"
       id="password"
       required>
<span class="eye" onclick="togglePassword()">👁</span>

        </div>

        <div class="input-box">
            <input type="text" 
                   name="dob"
                   placeholder="dd/mm/yyyy"  autocomplete="off" required>
        </div>

        <div class="input-box">
            <input type="text" 
                   name="address"
                   placeholder="Address" autocomplete="off" required>
        </div>

        <div class="input-box">
            <input type="text" 
                   name="city"
                   placeholder="City" autocomplete="off" required>
        </div>

        <div class="input-box">
            <input type="text" 
                   name="country"
                   placeholder="Country" autocomplete="off" required>
        </div>

    </div>

    <button type="submit" 
            name="signup"
            class="create-btn">
        Create Account
    </button>

</form>

    <div class="login-text">
        Already have account?
        <a href="login.php">Login</a>
    </div>

</div>

<script>
function togglePassword() {
    const passField = document.getElementById("password");
    const eye = document.querySelector(".eye");
    if (passField.type === "password") {
        passField.type = "text";
        eye.textContent = "🙈"; // change icon when visible
    } else {
        passField.type = "password";
        eye.textContent = "👁"; // revert icon when hidden
    }
}
</script>



</body>
</html>