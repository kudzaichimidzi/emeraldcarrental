<?php
session_start();
session_destroy();
header("Location: index.php");

?>

<a href="#" class="nav-link text-white">Logout</a>