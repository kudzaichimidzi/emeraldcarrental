<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

require('../fpdf/fpdf.php');
include('../includes/config.php');
include('../phpqrcode/qrlib.php'); // make sure you have PHP QR Code library here

if(!isset($_GET['id'])){
    exit("Invalid");
}

$id = intval($_GET['id']);

/* FETCH DATA */
$sql = "SELECT 
            tblbooking.*, 
            tblvehicles.VehiclesTitle, 
            tblvehicles.PricePerDay
        FROM tblbooking
        LEFT JOIN tblvehicles ON tblvehicles.id = tblbooking.VehicleId
        WHERE tblbooking.id = :id";

$query = $dbh->prepare($sql);
$query->bindParam(':id',$id,PDO::PARAM_INT);
$query->execute();
$result = $query->fetch(PDO::FETCH_OBJ);

if(!$result){
    exit("Not found");
}

/* CALCULATE */
$days = ceil((strtotime($result->ToDate) - strtotime($result->FromDate)) / (60*60*24));
if($days <= 0){
    $days = 1;
}
$price = $result->PricePerDay ? $result->PricePerDay : 0;
$total = $days * $price;
$invoiceNo = "INV-".date("Y")."-".$result->id;

/* QR DATA */
$qrData = "Booking ID: ".$result->id."\n".
          "Name: ".$result->name."\n".
          "From: ".$result->FromDate."\n".
          "To: ".$result->ToDate."\n".
          "Total: $".$total;

/* Generate QR locally */
$qrFile = "../temp/qr_".$result->id.".png";
QRcode::png($qrData, $qrFile, QR_ECLEVEL_L, 4);

/* PDF */
$pdf = new FPDF();
$pdf->AddPage();

/* LOGO */
$pdf->Image('../admin/vehicleimages/logo.jpg',10,10,30);

/* COMPANY NAME */
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Emerald Car Rental',0,1,'C');

$pdf->SetFont('Arial','',12);
$pdf->Cell(0,10,'Booking Receipt',0,1,'C');

$pdf->Ln(10);

/* BOOKING INFO BOX */
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'Booking Details',1,1);

$pdf->SetFont('Arial','',11);
$pdf->Cell(50,10,'Booking ID:',1);
$pdf->Cell(0,10,$result->id,1,1);

$pdf->Cell(50,10,'Invoice No:',1);
$pdf->Cell(0,10,$invoiceNo,1,1);


$pdf->Cell(50,10,'Customer Name:',1);
$pdf->Cell(0,10,$result->name,1,1);

$pdf->Cell(50,10,'Email:',1);
$pdf->Cell(0,10,$result->email,1,1);

$pdf->Cell(50,10,'Phone:',1);
$pdf->Cell(0,10,$result->phone,1,1);

$pdf->Ln(5);

/* CAR DETAILS */
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'Car Details',1,1);

$pdf->SetFont('Arial','',11);
$pdf->Cell(50,10,'Car:',1);
$pdf->Cell(0,10,$result->VehiclesTitle ? $result->VehiclesTitle : 'N/A',1,1);

$pdf->Cell(50,10,'Pickup Location:',1);
$pdf->Cell(0,10,$result->PickupLocation,1,1);

$pdf->Cell(50,10,'Dropoff Location:',1);
$pdf->Cell(0,10,$result->DropoffLocation,1,1);

$pdf->Ln(5);

/* DATE DETAILS */
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'Rental Period',1,1);

$pdf->SetFont('Arial','',11);
$pdf->Cell(50,10,'From Date:',1);
$pdf->Cell(0,10,$result->FromDate,1,1);

$pdf->Cell(50,10,'To Date:',1);
$pdf->Cell(0,10,$result->ToDate,1,1);

$pdf->Cell(50,10,'Total Days:',1);
$pdf->Cell(0,10,$days,1,1);

$pdf->Ln(5);

/* PRICE TABLE */
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'Payment Details',1,1);

$pdf->SetFont('Arial','',11);
$pdf->Cell(50,10,'Price per Day:',1);
$pdf->Cell(0,10,'$'.$price,1,1);

$pdf->Cell(50,10,'Total Amount:',1);
$pdf->Cell(0,10,'$'.$total,1,1);

$pdf->Cell(50,10,'Status:',1);
if($result->Status == 0){
    $statusText = 'Pending';
} elseif($result->Status == 1){
    $statusText = 'Approved';
} elseif($result->Status == 2){
    $statusText = 'Cancelled';
} else {
    $statusText = 'Unknown';
}
$pdf->Cell(0,10,$statusText,1,1);

$pdf->Ln(10);

/* QR CODE */
$pdf->Ln(5);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'Scan for Booking Info',0,1,'C');

if(file_exists($qrFile)){
    $pdf->Image($qrFile,80,$pdf->GetY(),50,50);
    $pdf->Ln(55);
}

/* FOOTER */
$pdf->SetFont('Arial','I',10);
$pdf->Cell(0,10,'Thank you for choosing Emerald Car Rental!',0,1,'C');

$file = "../temp/receipt_".$result->id.".pdf";
$pdf->Output("I", "receipt_".$result->id.".pdf"); // show in browser

//$pdf->Output("F", $file); // save PDF

$mail = new PHPMailer(true);

try {
    // SERVER SETTINGS
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;

    $mail->Username = 'kudzaichimidzi@gmail.com';
    $mail->Password = 'rccranagrdnfaaoj';
    /*$mail->SMTPSecure = 'tls';*/
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // SENDER
    $mail->setFrom('kudzaichimidzi@gmail.com', 'Emerald Car Rental');

    // RECEIVER
    if(!empty($result->email)){
    $mail->addAddress($result->email, $result->name);
    }
    // ATTACH PDF
    $mail->addAttachment($file);

    // EMAIL CONTENT
    $mail->isHTML(true);
    $mail->Subject = "Your Booking Receipt - ".$statusText;
    $mail->Body = "
        Hello ".$result->name.",<br><br>
        Thank you for booking with Emerald Car Rental.<br>
        Your receipt is attached.<br><br>
        Booking ID: ".$result->id."
    ";

    $mail->send();
    echo "Email sent successfully!";

} catch (Exception $e) {
    echo "Email failed: ".$mail->ErrorInfo;
}


/* DELETE FILE AFTER SENDING */
if(file_exists($file)){
    unlink($file);
}

if(file_exists($qrFile)){
    unlink($qrFile);
}

?>
