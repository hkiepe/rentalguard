<?php 
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");
include("templates/header.inc.php");
?>
<div class="container main-container registration-form">
<h1>Rejestracja</h1>
<?php
$showFormular = true; //Variable ob das Registrierungsformular anezeigt werden soll
 
if(isset($_GET['register'])) {
	$error = false;
	$vorname = trim($_POST['vorname']);
	$nachname = trim($_POST['nachname']);
	$email = trim($_POST['email']);
	$passwort = $_POST['passwort'];
	$passwort2 = $_POST['passwort2'];
	
	if(empty($vorname) || empty($nachname) || empty($email)) {
		echo 'Proszę wypełnić wszystkie pola<br>';
		$error = true;
	}
  
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		echo 'Proszę podać poprawny adres e-mail<br>';
		$error = true;
	} 	
	if(strlen($passwort) == 0) {
		echo 'Proszę podać hasło<br>';
		$error = true;
	}
	if($passwort != $passwort2) {
		echo 'Hasła muszą się zgadzać<br>';
		$error = true;
	}
	
	//Überprüfe, dass die E-Mail-Adresse noch nicht registriert wurde
	if(!$error) { 
		$statement = $pdo->prepare("SELECT * FROM users WHERE email = :email");
		$result = $statement->execute(array('email' => $email));
		$user = $statement->fetch();
		
		if($user !== false) {
			echo 'Ten adres e-mail jest już zajęty<br>';
			$error = true;
		}	
	}
	
	//Keine Fehler, wir können den Nutzer registrieren
	if(!$error) {	
		$passwort_hash = password_hash($passwort, PASSWORD_DEFAULT);
		
		$statement = $pdo->prepare("INSERT INTO users (email, passwort, vorname, nachname) VALUES (:email, :passwort, :vorname, :nachname)");
		$result = $statement->execute(array('email' => $email, 'passwort' => $passwort_hash, 'vorname' => $vorname, 'nachname' => $nachname));
		
		if($result) {		
			echo 'Zostałeś pomyślnie zarejestrowany. <a href="login.php">Aby się zalogować</a>';
			$showFormular = false;
		} else {
			echo 'Niestety wystąpił błąd podczas zapisywania<br>';
		}
	} 
}
 
if($showFormular) {
?>

<form action="?register=1" method="post">

<div class="form-group">
<label for="inputVorname">Imię:</label>
<input type="text" id="inputVorname" size="40" maxlength="250" name="vorname" class="form-control" required>
</div>

<div class="form-group">
<label for="inputNachname">Nazwisko:</label>
<input type="text" id="inputNachname" size="40" maxlength="250" name="nachname" class="form-control" required>
</div>

<div class="form-group">
<label for="inputEmail">Email:</label>
<input type="email" id="inputEmail" size="40" maxlength="250" name="email" class="form-control" required>
</div>

<div class="form-group">
<label for="inputPasswort">Twoje hasło:</label>
<input type="password" id="inputPasswort" size="40"  maxlength="250" name="passwort" class="form-control" required>
</div> 

<div class="form-group">
<label for="inputPasswort2">Powtórz hasło:</label>
<input type="password" id="inputPasswort2" size="40" maxlength="250" name="passwort2" class="form-control" required>
</div> 
<button type="submit" class="btn btn-lg btn-primary btn-block">Zarejestrować</button>
</form>
 
<?php
} //Ende von if($showFormular)

if(!$showFormular) {
	header( "refresh:8;url=index.php" );
	?>

    <!-- <div class="jumbotron">
      <div class="container">
        <p>
		Formularz rejestracyjny jest obecnie wyłączony. <a href='mailto:invo@veloking.pl'>Skontaktuj się z nami</a>, jeśli potrzebujesz loginu.
        </p>
        <p>
        </p>
      </div>
    </div> -->
<?php
}
?>
	

</div>
<?php 
include("templates/footer.inc.php")
?>