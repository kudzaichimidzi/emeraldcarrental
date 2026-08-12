<?php

session_start();

include('../includes/config.php');

if(empty($_SESSION['login'])){
    header("Location: ../login.php");
    exit();
}

$useremail = $_SESSION['login'];

$msg = "";
$error = "";


// =========================
// SUBMIT TESTIMONIAL
// =========================

if(isset($_POST['testimonial'])){

    $testimonial = trim($_POST['testimonial']);

    if($testimonial == ""){

        $error = "Testimonial cannot be empty.";

    }
    else{

        $sql = "
        INSERT INTO tbltestimonial
        (
            UserEmail,
            Testimonial,
            status
        )
        VALUES
        (
            :useremail,
            :testimonial,
            0
        )
        ";

        $query = $dbh->prepare($sql);

        $query->execute([
            ":useremail" => $useremail,
            ":testimonial" => $testimonial
        ]);

        $msg = "Testimonial submitted successfully. It is waiting for admin approval.";
    }
}


// =========================
// USER INFORMATION
// =========================

$sql = "
SELECT
    FullName,
    Address,
    City,
    Country
FROM tblusers
WHERE EmailId = :useremail
";

$query = $dbh->prepare($sql);

$query->bindParam(
    ":useremail",
    $useremail,
    PDO::PARAM_STR
);

$query->execute();

$user = $query->fetch(PDO::FETCH_OBJ);


// =========================
// GET TESTIMONIALS
// =========================

$sql = "
SELECT
    Testimonial,
    PostingDate,
    status
FROM tbltestimonial
WHERE UserEmail = :useremail
ORDER BY PostingDate DESC
";

$query = $dbh->prepare($sql);

$query->bindParam(
    ":useremail",
    $useremail,
    PDO::PARAM_STR
);

$query->execute();

$testimonials = $query->fetchAll(PDO::FETCH_OBJ);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>My Testimonials | Emerald Cars</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
rel="stylesheet"
>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"
rel="stylesheet"
>


<style>

/* =========================
   GENERAL
========================= */

*{
    box-sizing:border-box;
}

body{

    margin:0;

    background:#0b1220;

    color:white;

    font-family:Arial,sans-serif;

}


/* =========================
   SIDEBAR
========================= */

.sidebar{

    position:fixed;

    left:0;

    top:0;

    width:230px;

    height:100vh;

    background:#111827;

    border-right:1px solid #1f2937;

    padding:25px 15px;

}


.sidebar h2{

    text-align:center;

    margin-bottom:35px;

}


.menu-title{

    color:#6b7280;

    font-size:12px;

    padding-left:15px;

    margin-bottom:10px;

}


.sidebar a{

    display:block;

    padding:13px 15px;

    margin-bottom:5px;

    color:#9ca3af;

    text-decoration:none;

    border-radius:8px;

    transition:.3s;

}


.sidebar a:hover{

    background:#1f2937;

    color:white;

}


.sidebar a.active{

    background:#2563eb;

    color:white;

}


.sidebar .logout{

    margin-top:30px;

    color:#ef4444;

}


/* =========================
   MAIN
========================= */

.main{

    margin-left:230px;

    padding:30px;

}


/* =========================
   TOP BAR
========================= */

.topbar{

    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-bottom:30px;

}


.topbar h2{

    margin:0;

    font-size:28px;

}


.topbar p{

    color:#9ca3af;

    margin-top:7px;

}


.user-box{

    background:#111827;

    border:1px solid #1f2937;

    padding:10px 15px;

    border-radius:10px;

}


/* =========================
   PROFILE
========================= */

.profile-card{

    background:#111827;

    border:1px solid #1f2937;

    border-radius:15px;

    padding:25px;

    margin-bottom:25px;

}


.profile{

    display:flex;

    align-items:center;

    gap:15px;

}


.avatar{

    width:55px;

    height:55px;

    border-radius:50%;

    background:#2563eb;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:22px;

    font-weight:bold;

}


.profile h3{

    margin:0;

}


.profile p{

    margin:5px 0 0;

    color:#9ca3af;

}


/* =========================
   CONTENT GRID
========================= */

.content{

    display:grid;

    grid-template-columns:1fr 1fr;

    gap:25px;

}


/* =========================
   SECTION
========================= */

.section{

    background:#111827;

    border:1px solid #1f2937;

    border-radius:15px;

    padding:25px;

}


.section h2{

    margin-top:0;

    margin-bottom:20px;

}


/* =========================
   FORM
========================= */

textarea{

    width:100%;

    min-height:160px;

    padding:15px;

    background:#0b1220;

    color:white;

    border:1px solid #374151;

    border-radius:10px;

    resize:vertical;

    outline:none;

}


textarea:focus{

    border-color:#2563eb;

}


.submit-btn{

    margin-top:15px;

    background:#2563eb;

    color:white;

    border:none;

    padding:12px 20px;

    border-radius:8px;

    cursor:pointer;

}


.submit-btn:hover{

    background:#1d4ed8;

}


/* =========================
   ALERTS
========================= */

.success{

    background:#14532d;

    color:#86efac;

    padding:12px;

    border-radius:8px;

    margin-bottom:20px;

}


.error{

    background:#7f1d1d;

    color:#fecaca;

    padding:12px;

    border-radius:8px;

    margin-bottom:20px;

}


/* =========================
   TESTIMONIAL
========================= */

.testimonial{

    background:#0b1220;

    border:1px solid #1f2937;

    border-radius:10px;

    padding:18px;

    margin-bottom:15px;

}


.testimonial p{

    color:#d1d5db;

    line-height:1.6;

}


.testimonial-date{

    color:#6b7280;

    font-size:13px;

}


.status{

    display:inline-block;

    padding:5px 10px;

    border-radius:20px;

    font-size:12px;

    margin-top:10px;

}


.pending{

    background:#78350f;

    color:#fcd34d;

}


.approved{

    background:#14532d;

    color:#86efac;

}


/* =========================
   EMPTY
========================= */

.empty{

    text-align:center;

    color:#6b7280;

    padding:30px;

}


/* =========================
   MOBILE
========================= */

@media(max-width:900px){

    .content{

        grid-template-columns:1fr;

    }

}


@media(max-width:650px){

    .sidebar{

        position:static;

        width:100%;

        height:auto;

    }

    .main{

        margin-left:0;

        padding:15px;

    }

    .topbar{

        flex-direction:column;

        align-items:flex-start;

        gap:15px;

    }

}

</style>

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
MY ACCOUNT
</p>


<a href="../index.php">
<i class="bi bi-house"></i>
Home
</a>


<a href="car-listing.php">
<i class="bi bi-car-front"></i>
Browse Cars
</a>


<a href="my-bookings.php">
<i class="bi bi-calendar-check"></i>
My Bookings
</a>


<a href="testimonials.php" class="active">
<i class="bi bi-chat-quote"></i>
My Testimonials
</a>


<a href="profile.php">
<i class="bi bi-person"></i>
My Profile
</a>


<a href="logout.php" class="logout">
<i class="bi bi-box-arrow-right"></i>
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
My Testimonials
</h2>

<p>
Share your experience with Emerald Cars
</p>

</div>


<div class="user-box">

<i class="bi bi-person-circle"></i>

<?php echo htmlentities($user->FullName); ?>

</div>

</div>


<!-- PROFILE -->

<div class="profile-card">

<div class="profile">

<div class="avatar">

<?php

echo strtoupper(
    substr($user->FullName,0,1)
);

?>

</div>


<div>

<h3>

<?php echo htmlentities($user->FullName); ?>

</h3>

<p>

<?php echo htmlentities($useremail); ?>

</p>

</div>

</div>

</div>


<!-- CONTENT -->

<div class="content">


<!-- =========================
     SUBMIT
========================= -->

<div class="section">

<h2>
<i class="bi bi-pencil-square"></i>
Leave a Testimonial
</h2>


<?php

if($msg != ""){

?>

<div class="success">

<?php echo $msg; ?>

</div>

<?php

}

?>


<?php

if($error != ""){

?>

<div class="error">

<?php echo $error; ?>

</div>

<?php

}

?>


<form method="post">

<label>

Tell us about your experience

</label>


<textarea
name="testimonial"
placeholder="Write your experience with Emerald Cars..."
required
></textarea>


<button
type="submit"
name="testimonial"
class="submit-btn"
>

<i class="bi bi-send"></i>

Submit Testimonial

</button>

</form>

</div>


<!-- =========================
     TESTIMONIALS
========================= -->

<div class="section">

<h2>

<i class="bi bi-chat-quote"></i>

Your Testimonials

</h2>


<?php

if(count($testimonials) > 0){

foreach($testimonials as $t){

?>

<div class="testimonial">


<p>

"<?php echo htmlentities($t->Testimonial); ?>"

</p>


<div class="testimonial-date">

<i class="bi bi-calendar"></i>

<?php

echo date(
    "d M Y",
    strtotime($t->PostingDate)
);

?>

</div>


<?php

if($t->status == 1){

?>

<span class="status approved">

<i class="bi bi-check-circle"></i>

Approved

</span>

<?php

}
else{

?>

<span class="status pending">

<i class="bi bi-clock"></i>

Pending Approval

</span>

<?php

}

?>


</div>

<?php

}

}
else{

?>

<div class="empty">

<i class="bi bi-chat-square-text"
style="font-size:35px;">
</i>

<p>

You haven't submitted any testimonials yet.

</p>

</div>

<?php

}

?>

</div>


</div>


</div>


</body>

</html>