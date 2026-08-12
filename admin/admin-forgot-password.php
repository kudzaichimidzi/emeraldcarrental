<?php
session_start();

// allow admin to reset password without login
$_SESSION['reset_admin'] = true;

header("Location: changepassword.php");
exit();
?>
