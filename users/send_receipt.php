
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require('../fpdf/fpdf.php');
include('../includes/config.php');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if($id <= 0) die("Invalid booking ID");

// Fetch booking
$sql = "SELECT tblbooking.*, tblvehicles.VehiclesTitle, tblvehicles.PricePerDay
        FROM tblbooking
        LEFT JOIN tblvehicles ON tblvehicles.id = tblbooking.VehicleId
        WHERE tblbooking.id = :id";
$query = $dbh->prepare($sql);
$query->bindParam(':id',$id,PDO::PARAM_INT);
$query->execute();
$result = $query->fetch(PDO::FETCH_OBJ);
if(!$result) die("Booking not found");

// Build PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Booking Receipt',0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,10,'Booking ID: '.$result->id,0,1);
$pdf->Cell(0,10,'Name: '.$result->name,0,1);
$pdf->Cell(0,10,'Email: '.$result->email,0,1);

$file = "../temp/receipt_".$result->id.".pdf";
$pdf->Output("F", $file); // save PDF

// Send email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'kudzaichimidzi@gmail.com';
    $mail->Password = 'rccranagrdnfaaoj';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('kudzaichimidzi@gmail.com', 'Emerald Car Rental');
    $mail->addAddress($result->email, $result->name);
    $mail->addAttachment($file);

    $mail->isHTML(true);
    $mail->Subject = "Your Booking Receipt";
    $mail->Body = "Hello ".$result->name.",<br>Your receipt is attached.";

    $mail->send();
    echo "Email sent successfully!";
} catch (Exception $e) {
    echo "Email failed: ".$mail->ErrorInfo;
}

// Clean up
if(file_exists($file)) unlink($file);
?>
