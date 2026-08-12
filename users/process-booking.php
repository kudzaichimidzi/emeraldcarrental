<?php
session_start();
include('../includes/config.php');

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;

// Generate booking number
$bookingNumber = time();

// Collect form data
$firstname      = $_POST['fullname'];
$userEmail      = $_POST['email'];
$phone          = $_POST['phone'];
$pickupLocation = $_POST['pickup'];
$dropoffLocation= $_POST['dropoff'];
$fromDate       = $_POST['fromdate'];
$toDate         = $_POST['todate'];
$pickupTime     = $_POST['pickuptime'];
$dropoffTime    = $_POST['dropofftime'];
$vehicleId      = $_POST['car'];

// CHECK IF SAME BOOKING ALREADY EXISTS

$check = $dbh->prepare("

SELECT COUNT(*)

FROM tblbooking

WHERE email=:email

AND VehicleId=:vehicle

AND FromDate=:fromdate

AND ToDate=:todate

");


$check->execute([

":email"=>$userEmail,

":vehicle"=>$vehicleId,

":fromdate"=>$fromDate,

":todate"=>$toDate

]);


if($check->fetchColumn()>0){

echo "

<script>

alert('Booking already exists');

window.location='booking.php';

</script>

";

exit();

}

$payment        = $_POST['paymentmethod'];
$license        = $_POST['licenseNumber'];
$country        = $_POST['countryOfIssuance'];
$totalPrice     = $_POST['totalprice'];

// Fetch car details
$sql = "SELECT VehiclesTitle, PricePerDay FROM tblvehicles WHERE id=:vid";
$query = $dbh->prepare($sql);
$query->bindParam(':vid',$vehicleId,PDO::PARAM_INT);
$query->execute();
$car = $query->fetch(PDO::FETCH_OBJ);

$carModel   = $car->VehiclesTitle;
$pricePerDay= $car->PricePerDay;

// Insert booking
$sql = "INSERT INTO tblbooking 
(user_id,BookingNumber, name, DOB, Phone, Email, PickupLocation, DropoffLocation, FromDate, ToDate, PickupTime, DropoffTime, VehicleId, PaymentMethod, LicenseNumber, CountryOfIssuance, TotalAmount, Status) 
VALUES 
(:user_id,:bookingNumber, :fullname, :dob, :phone, :email, :pickup, :dropoff, :fromdate, :todate, :pickuptime, :dropofftime, :car, :paymentmethod, :licenseNumber, :countryOfIssuance, :totalprice, 0)";

$query = $dbh->prepare($sql);

$query->bindParam(':user_id', $user_id);
$query->bindParam(':bookingNumber', $bookingNumber);
$query->bindParam(':fullname', $firstname);
$query->bindParam(':dob', $_POST['dob']); // only if present
$query->bindParam(':phone', $phone);
$query->bindParam(':email', $userEmail);
$query->bindParam(':pickup', $pickupLocation);
$query->bindParam(':dropoff', $dropoffLocation);
$query->bindParam(':fromdate', $fromDate);
$query->bindParam(':todate', $toDate);
$query->bindParam(':pickuptime', $pickupTime);
$query->bindParam(':dropofftime', $dropoffTime);
$query->bindParam(':car', $vehicleId, PDO::PARAM_INT);
$query->bindParam(':paymentmethod', $payment);
$query->bindParam(':licenseNumber', $license);
$query->bindParam(':countryOfIssuance', $country);
$query->bindParam(':totalprice', $totalPrice);



if($query->execute()){


$bookingId = $dbh->lastInsertId();


echo "

<script>

alert('Booking successful! Your booking number is $bookingNumber');

window.location='confirmation.php?booking=$bookingId';

</script>

";


}

else{


echo "

<script>

alert('Error: Could not save booking');

window.location='booking.php';

</script>

";


}

?>