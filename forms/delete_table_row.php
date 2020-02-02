<?php
    
    include "..\config.php"; //------> noch umtauschen für PDO Verbindung !!!!!!!!!!!
    
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
    error_log('TEST1');
    echo 0;
    exit;
?>