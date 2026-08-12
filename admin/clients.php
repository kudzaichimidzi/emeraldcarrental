<?php

session_start();

include('../includes/config.php');


if(empty($_SESSION['alogin'])){

    header("Location: login.php");

    exit();

}


/* =========================
   CLIENT STATISTICS
========================= */


/* Total unique clients */

$totalClients = $dbh->query("

SELECT COUNT(DISTINCT email)

FROM tblbooking

")->fetchColumn();



/* Total bookings */

$totalBookings = $dbh->query("

SELECT COUNT(*)

FROM tblbooking

")->fetchColumn();



/* Active clients */

$activeClients = $dbh->query("

SELECT COUNT(DISTINCT email)

FROM tblbooking

WHERE Status = 1

")->fetchColumn();



/* =========================
   CLIENT LIST
========================= */

 $clients = $dbh->query("

SELECT

    b.email,

    MAX(b.name) AS name,

    MAX(b.phone) AS phone,

    MAX(b.CountryOfIssuance) AS country,

    COUNT(*) AS totalBookings,

    SUM(
        GREATEST(DATEDIFF(b.ToDate, b.FromDate),1)
        * b.PricePerDay
    ) AS totalSpent,

    MAX(b.PostingDate) AS lastBooking

FROM tblbooking b

GROUP BY b.email

ORDER BY lastBooking DESC

");


?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<title>Clients - Emerald Car Rental</title>


<link rel="stylesheet" href="css/clients.css">


</head>


<body>


<!-- =========================
     SIDEBAR
========================= -->

<div class="sidebar">


<h2>
Emerald Cars
</h2>


<p class="menu-title">
MAIN MENU
</p>


<a href="index.php">
Dashboard
</a>


<a href="manage-booking.php">
Bookings
</a>


<a href="manage-vehicles.php">
Vehicles
</a>


<a href="clients.php" class="active">
Clients
</a>




<a href="payments.php">
Payments
</a>


<a href="reports.php">
Reports
</a>


<a href="settings.php">
Settings
</a>


<a href="admin-login.php" class="logout">
Logout
</a>


</div>



<!-- =========================
     MAIN
========================= -->

<div class="main">


<!-- TOP BAR -->

<div class="topbar">


<div>

<h2>
Clients
</h2>

<p>
Manage your Emerald Car Rental clients
</p>

</div>


<div class="admin">

👤

<?php echo $_SESSION['alogin']; ?>

</div>


</div>



<!-- =========================
     STATISTICS
========================= -->

<div class="cards">


<div class="card">

<div class="icon">
👥
</div>

<div>

<p>
Total Clients
</p>

<h2>
<?php echo $totalClients; ?>
</h2>

</div>

</div>



<div class="card">

<div class="icon">
📘
</div>

<div>

<p>
Total Bookings
</p>

<h2>
<?php echo $totalBookings; ?>
</h2>

</div>

</div>



<div class="card">

<div class="icon">
🟢
</div>

<div>

<p>
Active Clients
</p>

<h2>
<?php echo $activeClients; ?>
</h2>

</div>

</div>


</div>



<!-- =========================
     CLIENT TABLE
========================= -->

<div class="section">


<div class="section-header">

<div>

<h2>
All Clients
</h2>

<p>
Clients who have made bookings
</p>

</div>


<input
type="text"
id="clientSearch"
placeholder="🔍 Search clients..."
onkeyup="searchClients()"
>


</div>



<div class="table-container">


<table id="clientsTable">


<thead>

<tr>

<th>#</th>

<th>Client</th>

<th>Contact</th>

<th>Country</th>

<th>Bookings</th>

<th>Total Spent</th>

<th>Last Booking</th>

<th>Status</th>

<th>Action</th>

</tr>

</thead>


<tbody>


<?php

$count = 1;


while($client = $clients->fetch(PDO::FETCH_ASSOC)){

?>


<tr>


<td>

<?php echo $count; ?>

</td>



<td>

<div class="client-name">

<div class="avatar">

<?php

$name = $client['name'];

echo strtoupper(substr($name,0,1));

?>

</div>


<div>

<strong>

<?php echo htmlentities($client['name']); ?>

</strong>

<br>

<small>

<?php echo htmlentities($client['email']); ?>

</small>

</div>

</div>

</td>



<td>

<?php echo htmlentities($client['phone']); ?>

</td>



<td>

<?php

if(!empty($client['country'])){

    echo htmlentities($client['country']);

}
else{

    echo "—";

}

?>

</td>



<td>

<span class="booking-count">

<?php echo $client['totalBookings']; ?>

</span>

</td>



<td>

<strong>

$<?php

if($client['totalSpent'] == ""){

    echo "0";

}
else{

    echo number_format($client['totalSpent'],2);

}

?>

</strong>

</td>



<td>

<?php

if(!empty($client['lastBooking'])){

    echo date("d M Y",strtotime($client['lastBooking']));

}
else{

    echo "—";

}

?>

</td>



<td>

<?php

if($client['totalBookings'] > 0){

    echo "<span class='status active-status'>Active</span>";

}
else{

    echo "<span class='status'>Inactive</span>";

}

?>

</td>



<td>

<a

href="client-details.php?email=<?php echo urlencode($client['email']); ?>"

class="view-btn">

View

</a>

</td>


</tr>


<?php

$count++;

}

?>


</tbody>


</table>


</div>


</div>


</div>



<script>

function searchClients(){

    var input =
        document.getElementById("clientSearch");

    var filter =
        input.value.toLowerCase();

    var table =
        document.getElementById("clientsTable");

    var rows =
        table.getElementsByTagName("tr");


    for(var i = 1; i < rows.length; i++){

        var text =
            rows[i].textContent.toLowerCase();


        if(text.indexOf(filter) > -1){

            rows[i].style.display = "";

        }
        else{

            rows[i].style.display = "none";

        }

    }

}

</script>


</body>

</html>