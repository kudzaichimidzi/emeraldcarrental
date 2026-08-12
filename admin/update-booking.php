<?php
session_start();
include('../includes/config.php');

if(!isset($_SESSION['alogin'])){
    exit("unauthorized");
}

if(!isset($_GET['id']) || !isset($_GET['status'])){
    exit("invalid request");
}

$id = intval($_GET['id']);
$status = intval($_GET['status']);

if($status == 1 || $status == 2){

    // check booking exists
    $check = $dbh->prepare("SELECT id FROM tblbooking WHERE id=:id");
    $check->bindParam(':id', $id);
    $check->execute();

    if($check->rowCount() == 0){
        exit("not found");
    }

    $sql = "UPDATE tblbooking SET Status=:status WHERE id=:id";
    $query = $dbh->prepare($sql);

    $query->bindParam(':status', $status, PDO::PARAM_INT);
    $query->bindParam(':id', $id, PDO::PARAM_INT);

    if($query->execute()){
        echo "success";
    } else {
        echo "error";
    }

} else {
    echo "invalid";
}
?>