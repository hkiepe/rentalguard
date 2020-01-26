<?php 
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");
$user = check_user();
include("templates/header.inc.php");

    include "config.php";

    $id = $_POST['tmp_id'];
    error_log('Wert von $id: ' . $_POST['tmp_id']);

    if($id > 0){

    // Check record exists
    $checkRecord = mysqli_query($con,"SELECT * FROM tmp_rentals WHERE tmp_id=".$id);
    $totalrows = mysqli_num_rows($checkRecord);

    if($totalrows > 0){
        // Delete record
        $query = "DELETE FROM tmp_rentals WHERE tmp_id=".$id;
        mysqli_query($con,$query);
        echo 1;
        header("Refresh:0");
        exit;
    }
    }

    echo 0;
    exit;
?>