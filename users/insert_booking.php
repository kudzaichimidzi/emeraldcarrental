<?php

session_start();

include('../includes/config.php');


// ===============================
// CHECK LOGIN
// ===============================

if(!isset($_SESSION['user_id'])){

    echo "<script>
    alert('Please login first');
    window.location='login.php';
    </script>";

    exit();

}


// ===============================
// CHECK POST
// ===============================

if($_SERVER["REQUEST_METHOD"] != "POST"){

    exit("Invalid Request");

}



// ===============================
// COLLECT FORM DATA
// ===============================

$name = trim($_POST['fullname']);
$phone = trim($_POST['phone']);
$email = trim($_POST['email']);

$dob = $_POST['dob'];

$license = trim($_POST['licenseNumber']);
$country = trim($_POST['countryOfIssuance']);

$pickup = trim($_POST['pickup']);
$dropoff = trim($_POST['dropoff']);

$from = $_POST['fromdate'];
$to = $_POST['todate'];

$ptime = $_POST['pickuptime'];
$dtime = $_POST['dropofftime'];

$car = $_POST['car'];

$payment = $_POST['paymentmethod'];

$user_id = $_SESSION['user_id'];



// ===============================
// REQUIRED FIELD VALIDATION
// ===============================


if(
$name=="" ||
$phone=="" ||
$email=="" ||
$dob=="" ||
$license=="" ||
$country=="" ||
$pickup=="" ||
$dropoff=="" ||
$from=="" ||
$to=="" ||
$ptime=="" ||
$dtime=="" ||
$car=="" ||
$payment==""
){

    echo "<script>
    alert('Please fill all fields');
    window.history.back();
    </script>";

    exit();

}



// ===============================
// EMAIL VALIDATION
// ===============================

if(!filter_var($email,FILTER_VALIDATE_EMAIL)){


    echo "<script>
    alert('Invalid email');
    window.history.back();
    </script>";

    exit();

}



// ===============================
// PHONE VALIDATION
// ===============================

if(!preg_match('/^[0-9]+$/',$phone)){


    echo "<script>
    alert('Phone number must contain numbers only');
    window.history.back();
    </script>";

    exit();

}



// ===============================
// DATE VALIDATION
// ===============================

if(strtotime($from) > strtotime($to)){


    echo "<script>
    alert('Invalid booking dates');
    window.history.back();
    </script>";

    exit();

}




// ===============================
// AGE CHECK
// ===============================

$birth = new DateTime($dob);
$now = new DateTime();

$age = $now->diff($birth)->y;


if($age < 18){

    echo "<script>
    alert('Driver must be at least 18 years old');
    window.history.back();
    </script>";

    exit();

}



// ===============================
// GET VEHICLE DETAILS
// ===============================


$getCar = $dbh->prepare(
"SELECT VehiclesTitle, VehicleType, PricePerDay 
FROM tblvehicles 
WHERE id=:id AND status=1"
);


$getCar->bindParam(':id',$car);

$getCar->execute();


$vehicle = $getCar->fetch(PDO::FETCH_OBJ);



if(!$vehicle){


    echo "<script>
    alert('Selected vehicle does not exist');
    window.history.back();
    </script>";

    exit();

}



$carModel = $vehicle->VehiclesTitle;

$carType = $vehicle->VehicleType;

$pricePerDay = $vehicle->PricePerDay;




// ===============================
// CHECK DOUBLE BOOKING
// ===============================


$check = $dbh->prepare(

"SELECT id 
FROM tblbooking
WHERE VehicleId=:car
AND Status IN (0,1)
AND (FromDate <= :to AND ToDate >= :from)"

);


$check->bindParam(':car',$car);
$check->bindParam(':from',$from);
$check->bindParam(':to',$to);


$check->execute();



if($check->rowCount() > 0){


    echo "<script>
    alert('Car already booked for these dates');
    window.history.back();
    </script>";

    exit();

}




// ===============================
// CALCULATE DAYS
// ===============================


$days = (strtotime($to)-strtotime($from))/(60*60*24);


if($days < 1){

    $days = 1;

}



$totalAmount = $days * $pricePerDay;



// ===============================
// GENERATE BOOKING NUMBER
// ===============================


$bookingNumber = "BK".date("Ymd").rand(1000,9999);




// ===============================
// STATUS
// ===============================


// 0 = Pending
$status = 0;




// ===============================
// INSERT BOOKING
// ===============================


$sql = "

INSERT INTO tblbooking

(
user_id,
BookingNumber,
name,
email,
phone,
dob,
VehicleId,
CarType,
CarModel,
LicenseNumber,
CountryOfIssuance,
PickupLocation,
DropoffLocation,
FromDate,
ToDate,
PickupTime,
DropoffTime,
PaymentMethod,
PricePerDay,
TotalAmount,
Status

)

VALUES

(

:user_id,
:bno,
:name,
:email,
:phone,
:dob,
:vehicle,
:type,
:model,
:license,
:country,
:pickup,
:dropoff,
:from,
:to,
:ptime,
:dtime,
:payment,
:price,
:total,
:status

)

";



$query = $dbh->prepare($sql);



$query->bindParam(':user_id',$user_id);
$query->bindParam(':bno',$bookingNumber);

$query->bindParam(':name',$name);
$query->bindParam(':email',$email);
$query->bindParam(':phone',$phone);

$query->bindParam(':dob',$dob);

$query->bindParam(':vehicle',$car);

$query->bindParam(':type',$carType);

$query->bindParam(':model',$carModel);

$query->bindParam(':license',$license);

$query->bindParam(':country',$country);

$query->bindParam(':pickup',$pickup);

$query->bindParam(':dropoff',$dropoff);

$query->bindParam(':from',$from);

$query->bindParam(':to',$to);

$query->bindParam(':ptime',$ptime);

$query->bindParam(':dtime',$dtime);

$query->bindParam(':payment',$payment);

$query->bindParam(':price',$pricePerDay);

$query->bindParam(':total',$totalAmount);

$query->bindParam(':status',$status);



if($query->execute()){


    $bookingId = $dbh->lastInsertId();


    header("Location: confirmation.php?booking=".$bookingId);

    exit();


}

else{


    echo "<script>
    alert('Booking failed');
    window.history.back();
    </script>";

}



?>