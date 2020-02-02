<?php
    
    //include "..\config.php"; //------> noch umtauschen für PDO Verbindung !!!!!!!!!!!

    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $pesel = isset($_POST['pesel']) ? $_POST['pesel'] : '';
/*     $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $fname = isset($_POST['fname']) ? $_POST['fname'] : '';
    $sname = isset($_POST['sname']) ? $_POST['sname'] : ''; */

    $comma_separated = implode(",", $_POST);
    echo $comma_separated;

    $ok = true;
    $messages = array();

    if ( !isset($email) || empty($email) ) {
        $ok = false;
        $messages[] = 'email cannot be empty!';
    }

    if ( !isset($pesel) || empty($pesel) ) {
        $ok = false;
        $messages[] = 'pesel cannot be empty!';
    }

    if ($ok) {
        if ($email === 'dcode' && $pesel === 'dcode') {
            $ok = true;
            $messages[] = 'Successful login!';
        } else {
            $ok = false;
            $messages[] = 'Incorrect email/pesel combination!';
        }
    }

    echo json_encode(
        array(
            'ok' => $ok,
            'messages' => $messages
        )
    );

?>