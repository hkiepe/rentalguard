<?php 
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");
include("templates/header.inc.php")
?>

  

    

    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron">
      <div class="container">
        <h1>Gdański system wynajmu Veloking</h1>
        <p>
          Witamy w naszym systemie wynajmu. Zaloguj się do pracy lub zarejestruj, jeśli nie masz loginu.
        </p>
        <p>
          <?php
            if (!$_SESSION) {
              echo '<a class="btn btn-primary btn-lg" href="login.php" role="button">Zaloguj sie</a> ';
              echo '<a class="btn btn-primary btn-lg" href="register.php" role="button">zarejestruj się</a>';
            }
          ?>
        </p>
      </div>
    </div>

    <!-- <div class="container">
      <div class="row">
        <div class="col-md-4">
          <h2>Features</h2>
          <ul>
          	<li>Registrierung & Login</li> 
          	<li>Interner Mitgliederbereich</li>
          	<li>Neues Zusenden eines Passworts</li>
          	<li>Programmcode leicht verständlich und erweiterbar</li>
          	<li>Responsive Webdesign, ideal für PC, Tablet und Smartphone</li>
          </ul>
         
        </div>
        <div class="col-md-4">
          <h2>Dokumentation</h2>
          <p>Auf unserer Website erhaltet ihr eine umfangreiche Einführung in das Loginscript. Ziel ist es nicht einfach nur dieses Script zu dokumentieren, sondern euch zu befähigen eigene Login- und Mitgliederscripts zu erstellen. In den verschiedenen Artikeln auf unserer Website erhaltet umfangreiche Informationen dazu. </p>
          <p><a class="btn btn-default" href="http://www.php-einfach.de/experte/php-codebeispiele/loginscript/" target="_blank" role="button">Weitere Informationen &raquo;</a></p>
       </div>
        <div class="col-md-4">
          <h2>Webhosting</h2>
          <p>Möchtet ihr diesen Loginscript für eure Website nutzen, so benötigt ihr PHP fähigen Webspace. Auf unserer Website haben wir die verschiedenen Webhosting-Angebote ausführlich getesten damit ihr den idealen Webspace für eure Website findet.</p>
          <p><a class="btn btn-default" href="http://www.webhosterwissen.de" target="_blank" role="button">Weitere Informationen &raquo;</a></p>
        </div>
      </div> -->
	</div>
      

  
<?php 
include("templates/footer.inc.php")
?>
