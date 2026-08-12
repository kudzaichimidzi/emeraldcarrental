<?php
session_start();
include('../includes/config.php');

if(!isset($_GET['id'])){
    echo "Invalid receipt";
    exit();
}

$id = intval($_GET['id']);

/* FETCH BOOKING + USER + CAR */
$sql = "SELECT 
            b.*, 
            v.VehiclesTitle, 
            v.PricePerDay, 
            v.FuelType, 
            v.SeatingCapacity
        FROM tblbooking b
        JOIN tblvehicles v ON v.id = b.VehicleId
        WHERE b.id = :id";


$query = $dbh->prepare($sql);
$query->bindParam(':id', $id, PDO::PARAM_INT);
$query->execute();

$result = $query->fetch(PDO::FETCH_OBJ);

if(!$result){
    echo "Booking not found";
    exit();
}

/* CALCULATE */
$paymentStatus = ($result->Status == 1) ? "Paid" : "Unpaid";
$from = strtotime($result->FromDate);
$to = strtotime($result->ToDate);
$days = ($to - $from) / (60 * 60 * 24);

$pricePerDay = $result->PricePerDay;
$subtotal = $pricePerDay * $days;
$tax = $subtotal * 0.1; // 10% tax (optional)
$total = $subtotal + $tax;


include('../phpqrcode/qrlib.php');

$qrText = "Booking ID: ".$result->id;
$qrFile = "../temp/qr_".$result->id.".png";

QRcode::png($qrText, $qrFile, QR_ECLEVEL_L, 4);

/* FORMAT */
$bookingNumber = "BK-" . str_pad($result->id, 4, "0", STR_PAD_LEFT);
$receiptNumber = "RC-" . rand(10000,99999);
$bookingDate = date("d M Y", strtotime($result->PostingDate));

$statusText = "Pending";
if($result->Status == 1){
    $statusText = "Approved";
} elseif($result->Status == 2){
    $statusText = "Cancelled";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Receipt</title>
    <style>
        body{
            font-family: Arial;
            background:#f4f4f4;
        }
        .receipt-box{
            width:800px;
            margin:auto;
            background:#fff;
            padding:20px;
            border-radius:10px;
        }
        .header{
            display:flex;
            align-items:center;
            border-bottom:2px solid #eee;
            padding-bottom:10px;
        }
        .header img{
            width:80px;
            margin-right:15px;
        }
        h2{
            margin:0;
        }
        .section{
            margin-top:20px;
        }
        .section h3{
            border-bottom:1px solid #ddd;
            padding-bottom:5px;
        }
        table{
            width:100%;
        }
        td{
            padding:5px;
        }
        .total{
            font-weight:bold;
            font-size:18px;
        }
        .status{
            padding:5px 10px;
            background:green;
            color:#fff;
            border-radius:5px;
            display:inline-block;
        }
        .print-btn{
            margin-top:20px;
            padding:10px 20px;
            background:#007bff;
            color:#fff;
            border:none;
            cursor:pointer;
        }

 

.status.Approved { background:green; }
.status.Pending { background:orange; }
.status.Cancelled { background:red; }
    </style>
</head>

<body>

<div class="receipt-box">

    <!-- HEADER -->
    <div class="header">
        <img src="../admin/vehicleimages/logo.jpg" alt="Logo">
        <h2>Emerald Car Rental</h2>
    </div>

    <!-- TOP INFO -->
    <div class="section">
        <table>
            <tr>
                <td><b>Receipt No:</b> <?php echo $receiptNumber; ?></td>
                <td><b>Booking No:</b> <?php echo $bookingNumber; ?></td>
            </tr>
            <tr>
                <td><b>Booking Date:</b> <?php echo $bookingDate; ?></td>
                <td><b>Status:</b> <span class="status <?php echo $statusText; ?>">
                <?php echo $statusText; ?>
                </span></td>
            </tr>
        </table>
    </div>

    <!-- CUSTOMER -->
    <div class="section">
        <h3>Customer Details</h3>
        <table>

            <tr><td>Name:</td><td><?php echo $result->name; ?></td></tr>
            <tr><td>Email:</td><td><?php echo $result->email; ?></td></tr>
            <tr><td>Phone:</td><td><?php echo $result->phone; ?></td></tr>

        </table>
    </div>

    <!-- CAR -->
    <div class="section">
        <h3>Car Details</h3>
        <table>
            <tr><td>Car:</td><td><?php echo $result->VehiclesTitle; ?></td></tr>
            <tr><td>Fuel:</td><td><?php echo $result->FuelType; ?></td></tr>
            <tr><td>Seats:</td><td><?php echo $result->SeatingCapacity; ?></td></tr>
        </table>
    </div>

    <!-- BOOKING -->
    <div class="section">
        <h3>Booking Details</h3>
        <table>
            <tr><td>From:</td><td><?php echo $result->FromDate; ?></td></tr>
            <tr><td>To:</td><td><?php echo $result->ToDate; ?></td></tr>
            <tr><td>Days:</td><td><?php echo $days; ?></td></tr>
        </table>
    </div>

    <!-- PAYMENT -->
    <div class="section">
        <h3>Payment</h3>
        <table>
            <tr><td>Price/Day:</td><td>$<?php echo $pricePerDay; ?></td></tr>
            <tr><td>Subtotal:</td><td>$<?php echo $subtotal; ?></td></tr>
            <tr><td>Tax (10%):</td><td>$<?php echo $tax; ?></td></tr>
            <tr class="total"><td>Total:</td><td>$<?php echo $total; ?></td></tr>
            <tr>
                <td>Payment Status:</td>
                <td><?php echo $paymentStatus; ?></td>
            </tr>
       
       
       
        </table>
    </div>

    <div class="section">
    <h3>QR Code</h3>
    <img src="../temp/qr_<?php echo $result->id; ?>.png" width="120">
    <p>Scan for booking verification</p>
</div>


    <!-- TERMS -->
    <div class="section">
        <h3>Terms</h3>
        <p>
            Car must be returned in original condition.<br>
            Late returns may incur extra charges.<br>
            Valid ID required at pickup.
        </p>
    </div>
     
<a href="receipt.php?id=<?php echo $result->id; ?>">
        View Receipt
</a>

    <!-- PRINT -->
    <button class="print-btn" onclick="window.print()">Print Receipt</button>
    <a href="receipt_pdf.php?id=<?php echo $result->id; ?>" target="_blank">
    <button class="print-btn">Download PDF</button>
</a>

</div>

</body>
</html>