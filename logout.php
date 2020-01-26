<?php 
session_start();
session_destroy();
unset($_SESSION['userid']);
unset($_SESSION['rentalpoint']);

//Remove Cookies
setcookie("identifier","",time()-(3600*24*365)); 
setcookie("securitytoken","",time()-(3600*24*365)); 

require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");

include("templates/header.inc.php");
header( "refresh:8;url=index.php" );
?>

<div class="container main-container">
Wylogowanie się powiodło. <a href="login.php">Wróć do logowania</a> lub <a href="index.php">wróć do strony głównej</a>.
</div>
<?php 
include("templates/footer.inc.php")
?>