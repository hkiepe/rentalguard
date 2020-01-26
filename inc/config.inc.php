<?php
/**
 * A database for renting vehicles.
 *
 * @author: Henrik Kiepe / https://www.inkontor.com
 * @license: GNU GPLv3
 */
 
//Tragt hier eure Verbindungsdaten zur Datenbank ein
$db_host = 'localhost';
$db_name = 'rentalguard';
$db_user = 'rentalguard';
$db_password = 'Lucky786';
$pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);