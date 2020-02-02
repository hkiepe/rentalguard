<?php
session_start();
require_once("../inc/config.inc.php");
$_SESSION['clientID'] = 134;
?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Ajax Form</title>
        <style>
            /* Login form CSS with JS and Ajax */
            #form-messages {
                background-color: rgb(255, 232, 232);
                border: 1px solid red;
                color: red;
                display: none;
                font-size: 12px;
                font-weight: bold;
                margin-bottom: 10px;
                padding:20px 25px;
                max-width: 250px;
            }
        </style>
    </head>
    <body>
        <div class="form">
            
            <label for="email" spellcheck="false">Email</label>
            <input type="text" id="email"><br>
                
            <label for="pesel">PESEL</label>
            <input type="password" id="pesel"><br>
                
<!--             <label for="phone">Telefon</label>
            <input type="text" id="phone" placeholder="Your Phone" spellcheck="false"><br>
                
            <label for="fname">Forename</label>
            <input type="text" id="fname" placeholder="Your Forename" spellcheck="false"><br>

            <label for="sname">Surename</label>
            <input type="text" id="sname" placeholder="Your Surename" spellcheck="false"><br> -->

            <button type="submit" id="btn-submit" label="Login">Login</button>

            <ul id="form-messages">
                <li>Generic Error 1</li>
            </ul>

        </div>

        <script src="script.js"></script>
    </body>
</html>
