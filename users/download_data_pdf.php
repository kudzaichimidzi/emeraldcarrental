<?php
session_start();
include('../includes/config.php');

require('../fpdf/fpdf.php');

if(!isset($_SESSION['user_id'])){
    exit("Unauthorized");
}

$userId = $_SESSION['user_id'];

/* GET USER */
$sql = "SELECT FullName, EmailId, ContactNo, Address, City, Country, profile_image 
        FROM tblusers WHERE id=:id";

$q = $dbh->prepare($sql);
$q->bindParam(':id', $userId);
$q->execute();
$user = $q->fetch(PDO::FETCH_OBJ);

/* GET BOOKINGS */
$sql = "SELECT id, FromDate, ToDate, Status 
        FROM tblbooking WHERE user_id=:uid";

$q = $dbh->prepare($sql);
$q->bindParam(':uid', $userId);
$q->execute();
$bookings = $q->fetchAll(PDO::FETCH_OBJ);

$totalBookings = count($bookings);

$activeBookings = 0;
$completedBookings = 0;

foreach($bookings as $b){
    if($b->Status == 0 || $b->Status == 1){
        $activeBookings++;
    }
    if($b->Status == 2){
        $completedBookings++;
    }
}

/* CREATE PDF */
$pdf = new FPDF();
$pdf->AddPage();

/* PAGE BORDER */
$pdf->SetDrawColor(000,000,000); // light gray
$pdf->Rect(5, 5, 200, 287);

/* LOGO */
$pdf->Image('../admin/vehicleimages/logo.jpg',10,10,25);

/* MOVE TEXT NEXT TO LOGO */
$pdf->SetXY(40, 12);

$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Emerald Car Rental',0,1);

/* SUBTITLE */
$pdf->SetX(40);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,'User Data Report',0,1);

$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,'User Data Report',0,1,'C');

$pdf->Ln(46);

/* PROFILE IMAGE */
if(!empty($user->profile_image)){
    $imgPath = "../uploads/".$user->profile_image;
    if(file_exists($imgPath)){
       // $pdf->Image($imgPath, 85, 40, 40);
        $pdf->Image($imgPath, 80, 45, 50);
    }
}

$pdf->Ln(10);

/* USER NAME */
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,$user->FullName,0,1,'C');

/* EMAIL */
$pdf->SetFont('Arial','',11);
$pdf->Cell(0,8,$user->EmailId,0,1,'C');

/* PHONE */
$pdf->Cell(0,8,$user->ContactNo,0,1,'C');

/* LOCATION */
$pdf->Cell(0,8,$user->City . ', ' . $user->Country,0,1,'C');

$pdf->Ln(10);

/* DASHBOARD BOXES */

$startX = 20;   // left margin
$startY = $pdf->GetY();
$boxWidth = 50;
$boxHeight = 25;
$gap = 10;

/* COLORS */
$blue = [0,123,255];
$green = [40,167,69];
$orange = [255,193,7];

/* FUNCTION TO DRAW BOX */
function drawBox($pdf, $x, $y, $w, $h, $title, $value, $color){
    // background
    $pdf->SetFillColor($color[0], $color[1], $color[2]);
    $pdf->Rect($x, $y, $w, $h, 'F');

    // text color white
    $pdf->SetTextColor(255,255,255);

    // title
    $pdf->SetFont('Arial','',10);
    $pdf->SetXY($x, $y+5);
    $pdf->Cell($w,5,$title,0,2,'C');

    // value
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell($w,8,$value,0,0,'C');
}

/* DRAW BOXES */
drawBox($pdf, $startX, $startY, $boxWidth, $boxHeight, 'Total', $totalBookings, $blue);

drawBox($pdf, $startX + $boxWidth + $gap, $startY, $boxWidth, $boxHeight, 'Active', $activeBookings, $green);

drawBox($pdf, $startX + 2*($boxWidth + $gap), $startY, $boxWidth, $boxHeight, 'Completed', $completedBookings, $orange);

/* MOVE CURSOR DOWN */
$pdf->Ln(35);


/* SECTION TITLE */
$pdf->SetFillColor(0,123,255);
$pdf->SetTextColor(255,255,255);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,' Booking History',0,1,'L',true);

$pdf->Ln(3);

/* LOOP BOOKINGS */
$pdf->SetFont('Arial','',10);
$pdf->SetTextColor(0,0,0);

foreach($bookings as $b){

    // status text
    $status = "Pending";
    $statusColor = [255,193,7]; // yellow

    if($b->Status == 1){
        $status = "Approved";
        $statusColor = [40,167,69]; // green
    }
    if($b->Status == 2){
        $status = "Cancelled";
        $statusColor = [220,53,69]; // red
    }

    /* CARD BACKGROUND */
    $pdf->SetFillColor(245,245,245);
    $pdf->Cell(0,10,'',0,1,true);

    $startX = 10;
    $startY = $pdf->GetY() - 10;

    /* BOOKING TEXT */
    $pdf->SetXY($startX, $startY+2);

    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(0,5,"Booking ID: ".$b->id,0,1);

    $pdf->SetFont('Arial','',10);
    $pdf->Cell(0,5,"From: ".$b->FromDate."   To: ".$b->ToDate,0,1);

    /* STATUS BADGE */
    $pdf->SetXY(150, $startY+3);
    $pdf->SetFillColor($statusColor[0], $statusColor[1], $statusColor[2]);
    $pdf->SetTextColor(255,255,255);
    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(40,6,$status,0,0,'C',true);

    $pdf->Ln(5);

    /* RESET TEXT */
    $pdf->SetTextColor(0,0,0);
}


/* RESET TEXT COLOR */
$pdf->SetTextColor(0,0,0);

$pdf->SetY(-20);
$pdf->SetFont('Arial','I',10);
$pdf->SetTextColor(100,100,100);

$pdf->Cell(0,10,'Generated on '.date('d M Y'),0,1,'C');



$pdf->Output();