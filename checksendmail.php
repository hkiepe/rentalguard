<?php
//the subject
$sub = "Test Sendmail from XAMPP";
//the message
$msg = "This is a testmessage only to show if sendmail is working on XAMPP";
//recipient email here
$rec = "hkiepe@inkontor.com";
//send email
mail($rec,$sub,$msg);
?>