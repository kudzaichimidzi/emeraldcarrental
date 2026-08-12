<?php
session_start();
include('../includes/config.php');

if(!isset($_SESSION['temp_user'])){
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['temp_user'];

$error = "";
$success = "";

/* VERIFY OTP */
if(isset($_POST['verify'])){

$stmt = $dbh->prepare("SELECT otp_code, otp_expiry, otp_attempts FROM tblusers WHERE id=:id");
$stmt->execute([':id'=>$userId]);
$user = $stmt->fetch(PDO::FETCH_OBJ);

    $otp = $_POST['otp'];

    if($user->otp_attempts >= 3){
        $error = "❌ Too many attempts. Try again later.";
    }
    else if($otp != $user->otp_code){

        $dbh->prepare("UPDATE tblusers SET otp_attempts = otp_attempts + 1 WHERE id=:id")
            ->execute([':id'=>$userId]);

        // 🔥 re-fetch updated attempts
        $stmt = $dbh->prepare("SELECT otp_attempts FROM tblusers WHERE id=:id");
        $stmt->execute([':id'=>$userId]);
        $updated = $stmt->fetch(PDO::FETCH_OBJ);

        $left = 3 - $updated->otp_attempts;

        $error = "❌ Wrong OTP. Attempts left: " . $left;

       

        echo "<script>
        setTimeout(()=>{ window.location='verify-otp.php'; }, 800);
        </script>";


        }
    
    else if(strtotime($user->otp_expiry) < time()){
        $error = "❌ OTP expired. Please resend.";
    }
    else{
        // SUCCESS LOGIN
        $_SESSION['user_id'] = $userId;

        $stmt = $dbh->prepare("SELECT FullName, EmailId FROM tblusers WHERE id=:id");
        $stmt->execute([':id'=>$userId]);
        $u = $stmt->fetch(PDO::FETCH_OBJ);

        $_SESSION['fullname'] = $u->FullName;
        $_SESSION['login'] = $u->EmailId;

        $dbh->prepare("UPDATE tblusers SET otp_attempts=0 WHERE id=:id")
            ->execute([':id'=>$userId]);

        $success = "Verified successfully! Redirecting...";
          // 🚀 Redirect immediately to home page
        header("Location: ../index.php");
        exit();

       
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>OTP Verification</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body{
    margin:0;
    font-family:Arial;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:#0f172a;
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

/* SHAKE */
@keyframes shake{
0%{transform:translateX(0)}
25%{transform:translateX(-6px)}
50%{transform:translateX(6px)}
75%{transform:translateX(-6px)}
100%{transform:translateX(0)}
}
.shake{animation:shake 0.4s;}

/* INPUTS */
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

/* SPINNER */
.spinner{
    display:none;
    border:3px solid #ccc;
    border-top:3px solid #38bdf8;
    border-radius:50%;
    width:20px;
    height:20px;
    animation:spin 1s linear infinite;
    margin:auto;
}
@keyframes spin{100%{transform:rotate(360deg)}}

/* TEXT */
.error{color:#f87171;margin-top:10px}
.success{color:#4ade80;margin-top:10px}
.timer{color:#facc15;margin-top:10px}
</style>
</head>

<body>

<div class="otp-box" id="box">

<h2>🔐 OTP Verification</h2>
<p>Enter 6 digit OTP sent to your email</p>

<?php if($error): ?>
<div class="error" id="errorMsg"><?= $error ?></div>
<?php endif; ?>

<?php if($success): ?>
<div class="success"><?= $success ?></div>
<?php endif; ?>

<form method="POST" onsubmit="showLoader()">

<div class="otp-inputs">
<input maxlength="1">
<input maxlength="1">
<input maxlength="1">
<input maxlength="1">
<input maxlength="1">
<input maxlength="1">
</div>

<input type="hidden" name="otp" id="otp">

<button class="btn" id="btn">
<span id="text">Verify OTP</span>
<div class="spinner" id="spin"></div>
</button>

</form>

<div class="timer" id="timer">OTP expires in 05:00</div>

<p>Attempts left: 
<?php
$stmt = $dbh->prepare("SELECT otp_attempts FROM tblusers WHERE id=:id");
$stmt->execute([':id'=>$userId]);
$u = $stmt->fetch(PDO::FETCH_OBJ);

echo 3 - $u->otp_attempts;
?>
</p>

</div>

<script>

const inputs = document.querySelectorAll("input");

inputs.forEach((inp,i)=>{
inp.addEventListener("input",()=>{
if(inp.value && i < inputs.length-1){
inputs[i+1].focus();
}
});
});

document.querySelector("form").addEventListener("submit",()=>{
let otp="";
inputs.forEach(i=>otp+=i.value);
document.getElementById("otp").value = otp;
});

function showLoader(){
document.getElementById("text").style.display="none";
document.getElementById("spin").style.display="block";
}

/* TIMER */
let t = 300;
setInterval(()=>{
let m = Math.floor(t/60);
let s = t%60;
document.getElementById("timer").innerText =
`OTP expires in ${m}:${s<10?'0':''}${s}`;
if(t>0) t--;
},1000);

/* SHAKE ERROR */
window.onload=()=>{
let e=document.getElementById("errorMsg");
if(e && e.innerText.trim() !== ""){
document.getElementById("box").classList.add("shake");
}
};
</script>

</body>
</html>
