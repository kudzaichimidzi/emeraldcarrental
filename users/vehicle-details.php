
<?php
session_start();
include('../includes/config.php');

if(!isset($_GET['vhid'])){
    header("Location: car-listing.php");
    exit();
}

$id = intval($_GET['vhid']);

$sql = "SELECT v.*, b.BrandName
        FROM tblvehicles v
        JOIN tblbrands b ON b.id = v.VehiclesBrand
        WHERE v.id = :id";

$query = $dbh->prepare($sql);
$query->bindParam(':id', $id, PDO::PARAM_INT);
$query->execute();

$car = $query->fetch(PDO::FETCH_OBJ);

if(!$car){
    echo "Vehicle not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>
<?php echo htmlentities($car->BrandName . " " . $car->VehiclesTitle); ?> Details
</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>

body{
    background: #0b1220;
    color: #ffffff;
    font-family: Arial, sans-serif;
}

.container{
    max-width: 1150px;
}


/* =========================
   PAGE TITLE
========================= */

.page-title{
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 30px;
}


/* =========================
   CAR IMAGE
========================= */

.car-gallery{
    background: #111827;
    border-radius: 18px;
    overflow: hidden;
}

.car-gallery img{
    width: 100%;
    height: 430px;
    object-fit: cover;
}


/* =========================
   CAR INFORMATION
========================= */

.info-card{
    background: #111827;
    border: 1px solid #1f2937;
    border-radius: 18px;
    padding: 25px;
}

.info-card h4{
    font-size: 24px;
    margin-bottom: 15px;
}

.overview{
    color: #9ca3af;
    line-height: 1.7;
    margin-bottom: 25px;
}


/* =========================
   SPECIFICATIONS
========================= */

.specs{
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.spec-item{
    background: #0b1220;
    border: 1px solid #1f2937;
    border-radius: 10px;
    padding: 14px;
    color: #d1d5db;
}

.spec-item i{
    color: #3b82f6;
    margin-right: 8px;
}


/* =========================
   PRICE
========================= */

.price{
    color: #22c55e;
    font-size: 28px;
    font-weight: 700;
}


/* =========================
   BOOKING CARD
========================= */

.booking-card{
    background: #111827;
    border: 1px solid #1f2937;
    border-radius: 18px;
    padding: 25px;
}

.booking-card h5{
    font-size: 21px;
    margin-bottom: 15px;
}

.book-btn{
    background: #2563eb;
    border: none;
    color: #ffffff;
    border-radius: 10px;
    padding: 13px;
    font-weight: 600;
    transition: 0.3s;
}

.book-btn:hover{
    background: #1d4ed8;
    color: #ffffff;
    transform: translateY(-2px);
}


/* =========================
   BACK BUTTON
========================= */

.back-btn{
    color: #9ca3af;
    text-decoration: none;
    display: inline-block;
    margin-bottom: 20px;
}

.back-btn:hover{
    color: #ffffff;
}


/* =========================
   RESPONSIVE
========================= */

@media(max-width: 768px){

    .page-title{
        font-size: 26px;
    }

    .car-gallery img{
        height: 280px;
    }

    .specs{
        grid-template-columns: 1fr;
    }

}

</style>

</head>


<body>

<div class="container py-5">

<a href="car-listing.php" class="back-btn">
    <i class="bi bi-arrow-left"></i> Back to Cars
</a>


<h2 class="page-title">

    🚗 <?php echo htmlentities($car->BrandName . " " . $car->VehiclesTitle); ?>

</h2>


<!-- =========================
     IMAGE GALLERY
========================= -->

<div id="carCarousel" class="carousel slide mb-4" data-bs-ride="carousel">

    <div class="carousel-inner car-gallery">

        <?php if(!empty($car->Vimage1)): ?>

        <div class="carousel-item active">

            <img
                src="../cars/<?php echo htmlentities($car->Vimage1); ?>"
                class="d-block w-100"
            >

        </div>

        <?php endif; ?>


        <?php if(!empty($car->Vimage2)): ?>

        <div class="carousel-item">

            <img
                src="../cars/<?php echo htmlentities($car->Vimage2); ?>"
                class="d-block w-100"
            >

        </div>

        <?php endif; ?>


        <?php if(!empty($car->Vimage3)): ?>

        <div class="carousel-item">

            <img
                src="../cars/<?php echo htmlentities($car->Vimage3); ?>"
                class="d-block w-100"
            >

        </div>

        <?php endif; ?>

    </div>


    <button
        class="carousel-control-prev"
        type="button"
        data-bs-target="#carCarousel"
        data-bs-slide="prev">

        <span class="carousel-control-prev-icon"></span>

    </button>


    <button
        class="carousel-control-next"
        type="button"
        data-bs-target="#carCarousel"
        data-bs-slide="next">

        <span class="carousel-control-next-icon"></span>

    </button>

</div>


<!-- =========================
     VEHICLE INFORMATION
========================= -->

<div class="row g-4">


    <div class="col-lg-8">

        <div class="info-card">

            <h4>Vehicle Overview</h4>

            <?php if(!empty($car->VehiclesOverview)): ?>

                <p class="overview">
                    <?php echo nl2br(htmlentities($car->VehiclesOverview)); ?>
                </p>

            <?php else: ?>

                <p class="overview">
                    No vehicle overview available.
                </p>

            <?php endif; ?>


            <div class="specs">


                <div class="spec-item">

                    <i class="bi bi-cash"></i>

                    Price per day:

                    <strong class="price">
                        $<?php echo htmlentities($car->PricePerDay); ?>
                    </strong>

                </div>


                <div class="spec-item">

                    <i class="bi bi-people"></i>

                    Seating Capacity:

                    <?php echo htmlentities($car->SeatingCapacity); ?>

                </div>


                <div class="spec-item">

                    <i class="bi bi-calendar"></i>

                    Model Year:

                    <?php echo htmlentities($car->ModelYear); ?>

                </div>


                <div class="spec-item">

                    <i class="bi bi-fuel-pump"></i>

                    Fuel Type:

                    <?php echo htmlentities($car->FuelType); ?>

                </div>


                <div class="spec-item">

                    <i class="bi bi-tag"></i>

                    Brand:

                    <?php echo htmlentities($car->BrandName); ?>

                </div>


            </div>

        </div>

    </div>


    <!-- =========================
         BOOKING
    ========================= -->

    <div class="col-lg-4">

        <div class="booking-card">

            <h5>Ready to Book?</h5>

            <p class="text-secondary">
                Reserve this vehicle and enjoy a comfortable journey.
            </p>

            <a
                href="booking.php?car=<?php echo $car->id; ?>"
                class="btn book-btn w-100">

                <i class="bi bi-check-circle"></i>

                Book Now

            </a>

        </div>

    </div>


</div>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>