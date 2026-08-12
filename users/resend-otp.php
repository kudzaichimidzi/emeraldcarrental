<?php
session_start();


if(!$user){
    echo "error";
    exit();
}
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

include('../includes/config.php');

if(!isset($_SESSION['temp_user'])){
    echo "unauthorized";
    exit();
}

$userId = $_SESSION['temp_user'];

/* 🔒 COOLDOWN CHECK (60 seconds) */
$stmt = $dbh->prepare("SELECT otp_sent_time FROM tblusers WHERE id=:id");
$stmt->execute([':id'=>$userId]);
$data = $stmt->fetch(PDO::FETCH_OBJ);

if($data && $data->otp_sent_time != null && strtotime($data->otp_sent_time) > time() - 60){
    echo "wait";
    exit();
}

/* 🔥 GENERATE NEW OTP */
$otp = rand(100000,999999);
$expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

/* UPDATE DB */
$stmt = $dbh->prepare("
    UPDATE tblusers 
    SET otp_code=:otp, otp_expiry=:exp, otp_sent_time=NOW()
    WHERE id=:id
");

$stmt->execute([
    ':otp'=>$otp,
    ':exp'=>$expiry,
    ':id'=>$userId
]);


$stmt = $dbh->prepare("SELECT EmailId FROM tblusers WHERE id=:id");
$stmt->execute([':id'=>$userId]);
$user = $stmt->fetch(PDO::FETCH_OBJ);
$mail = new PHPMailer(true);

try {

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;

    $mail->Username = 'kudzaichimidzi@gmail.com';
    $mail->Password = 'rccranagrdnfaaoj'; // IMPORTANT
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('kudzaichimidzi@gmail.com', 'OTP System');

    $mail->addAddress($user->EmailId);

    $mail->isHTML(true);
    $mail->Subject = "Your OTP Code";
    $mail->Body = "
        <h3>Your OTP Code</h3>
        <p><b>$otp</b></p>
        <p>This OTP is valid for 5 minutes.</p>
    ";

    $mail->send();

} catch(Exception $e) {
    echo $mail->ErrorInfo;
    exit();
}


/* SUCCESS */
echo "sent";
exit();
?>