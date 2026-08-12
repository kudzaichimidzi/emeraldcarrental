<?php
session_start();
include('../includes/config.php');

if(!isset($_SESSION['temp_user'])){
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['temp_user'];

if(isset($_POST['otp'])){

    $otp = $_POST['otp'];

    $stmt = $dbh->prepare("SELECT otp_code, otp_expiry FROM tblusers WHERE id=:id");
    $stmt->execute([':id'=>$userId]);
    $user = $stmt->fetch(PDO::FETCH_OBJ);

    if($user && $otp == $user->otp_code && strtotime($user->otp_expiry) > time()){

        $_SESSION['user_id'] = $userId;

   // 🔥 ADD THIS
    $stmt = $dbh->prepare("SELECT FullName FROM tblusers WHERE id=:id");
    $stmt->execute([':id'=>$userId]);
    $u = $stmt->fetch(PDO::FETCH_OBJ);

    $_SESSION['fullname'] = $u->FullName;

        unset($_SESSION['temp_user']);

        echo "success";
        exit();

    } else {

        echo "error";
        exit();
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>OTP Verification</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body{
    margin:0;
    font-family: Arial;
    background: linear-gradient(135deg,#0f172a,#1e293b);
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    color:white;
}

/* CARD */
.otp-box{
    width:340px;
    background:rgba(255,255,255,0.08);
    padding:25px;
    border-radius:15px;
    text-align:center;
}

/* OTP INPUTS */
.otp-inputs{
    display:flex;
    justify-content:space-between;
    margin:20px 0;
}

.otp-inputs input{
    width:45px;
    height:50px;
    font-size:22px;
    text-align:center;
    border-radius:8px;
    border:none;
    outline:none;
}

.otp-inputs input:focus{
    outline:2px solid #38bdf8;
}

/* BUTTON */
.btn{
    width:100%;
    padding:12px;
    border:none;
    border-radius:8px;
    background:#38bdf8;
    font-weight:bold;
    cursor:pointer;
}

/* SUCCESS TEXT */
.success{
    display:none;
    color:#4ade80;
    margin-bottom:10px;
}

/* ERROR TEXT */
.error{
    display:none;
    color:#f87171;
    margin-bottom:10px;
}

/* LOADER OVERLAY */
.overlay{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.6);
    display:none;
    justify-content:center;
    align-items:center;
    flex-direction:column;
    z-index:999;
}

.spinner{
    width:60px;
    height:60px;
    border:6px solid #ccc;
    border-top:6px solid #38bdf8;
    border-radius:50%;
    animation:spin 1s linear infinite;
}

@keyframes spin{
    100%{transform:rotate(360deg);}
}

.timer{
    margin-top:10px;
    color:#facc15;
    font-size:13px;
}

.success-screen{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:#0f172a;
    display:none;
    justify-content:center;
    align-items:center;
    flex-direction:column;
    z-index:1000;
    text-align:center;
}

.success-screen .check{
    font-size:70px;
    color:#4ade80;
    animation:pop 0.4s ease;
}

.success-screen h2{
    margin-top:10px;
}

.success-screen p{
    color:#94a3b8;
    font-size:13px;
}

@keyframes pop{
    0%{transform:scale(0)}
    100%{transform:scale(1)}
}
</style>
</head>

<body>

<div class="otp-box">

<h2>🔐 OTP Verification</h2>
<p>Enter 6-digit OTP</p>

<div class="success" id="successMsg">OTP Verified Successfully!</div>
<div class="error" id="errorMsg">Invalid OTP</div>

<form id="otpForm" method="POST" action="verify-otp.php">
<div class="otp-inputs">
    <input maxlength="1">
    <input maxlength="1">
    <input maxlength="1">
    <input maxlength="1">
    <input maxlength="1">
    <input maxlength="1">
</div>

<input type="hidden" id="otp" name="otp">

<div class="success-screen" id="successScreen">
    <div class="check">✔</div>
    <h2>Verified Successfully</h2>
    <p>Redirecting...</p>
</div>

<button class="btn" type="submit">Verify OTP</button>

</form>

<div class="timer" id="timer">OTP expires in 05:00</div>
<div style="margin-top:12px;font-size:13px;">
    Didn’t receive code? 
    <a href="#" id="resend" style="color:#38bdf8;opacity:0.5;pointer-events:none;">
        Resend OTP
    </a>
</div>

</div>

<!-- LOADER -->
<div class="overlay" id="overlay">
    <div class="spinner"></div>
    <p>Verifying...</p>
</div>

<script>

const inputs = document.querySelectorAll(".otp-inputs input");

/* auto move */
inputs.forEach((input, i) => {
    input.addEventListener("input", () => {
        if(input.value && i < inputs.length - 1){
            inputs[i+1].focus();
        }
    });
});

/* submit */
document.getElementById("otpForm").addEventListener("submit", function(e){
    e.preventDefault();

    let otp = "";
    inputs.forEach(i => otp += i.value);

    document.getElementById("otp").value = otp;

    document.getElementById("overlay").style.display = "flex";

    // 👉 send to PHP instead of fake check
    fetch("verify-otp.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "otp=" + otp
    })
    .then(res => res.text())
    .then(data => {

        document.getElementById("overlay").style.display = "none";

        if(data === "success"){
            document.getElementById("successScreen").style.display = "flex";

            setTimeout(() => {
                window.location = "../index.php";
            }, 2000);

        } else {
            document.getElementById("errorMsg").style.display = "block";
        }

    });

});

let timeLeft = 300;

const timerEl = document.getElementById("timer");
const resendBtn = document.getElementById("resend");

/* TIMER */
let countdown = setInterval(() => {

    let m = Math.floor(timeLeft / 60);
    let s = timeLeft % 60;

    timerEl.innerText = `OTP expires in ${m}:${s < 10 ? '0' : ''}${s}`;

    timeLeft--;

    if(timeLeft < 0){
        clearInterval(countdown);
        timerEl.innerText = "OTP expired";

        // enable resend
        resendBtn.style.pointerEvents = "auto";
        resendBtn.style.opacity = "1";
    }

}, 1000);


/* RESEND CLICK */
resendBtn.addEventListener("click", function(e){
    e.preventDefault();

    fetch("resend-otp.php")
    .then(res => res.text())
    .then(data => {

        if(data === "wait"){
            alert("Please wait 60 seconds before resending OTP");
            return;
        }

        if(data === "sent"){
            alert("OTP sent successfully!");

            // reset timer
            timeLeft = 300;

            resendBtn.style.pointerEvents = "none";
            resendBtn.style.opacity = "0.5";

            inputs.forEach(i => i.value = "");
            inputs[0].focus();
        }

    });

});


</script>

</body>
</html>