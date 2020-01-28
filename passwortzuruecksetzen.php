<?php 
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");
if(!isset($_GET['userid']) || !isset($_GET['code'])) {
	error("Niestety podczas odwiedzania tej strony nie został wysłany kod umożliwiający zresetowanie hasła");
}



$showForm = true; 
$userid = $_GET['userid'];
$code = $_GET['code'];
 
//Abfrage des Nutzers
$statement = $pdo->prepare("SELECT * FROM users WHERE id = :userid");
$result = $statement->execute(array('userid' => $userid));
$user = $statement->fetch();
 
//Überprüfe dass ein Nutzer gefunden wurde und dieser auch ein Passwortcode hat
if($user === null || $user['passwortcode'] === null) {
	error("Użytkownik nie został znaleziony lub nie poprosił o nowe hasło.");
}
 
if($user['passwortcode_time'] === null || strtotime($user['passwortcode_time']) < (time()-24*3600) ) {
	error("Niestety Twój kod wygasł. Użyj ponownie funkcji zapomnianego hasła.");
}
 
 
//Überprüfe den Passwortcode
if(sha1($code) != $user['passwortcode']) {
	error("Podany kod był nieprawidłowy. Upewnij się, że masz dokładny link w adresie URL. Jeśli kilkakrotnie używałeś funkcji zapomnienia hasła, wywołaj link w najnowszym e-mailu.");
}
 
//Der Code war korrekt, der Nutzer darf ein neues Passwort eingeben
 
if(isset($_GET['send'])) {
	$passwort = $_POST['passwort'];
	$passwort2 = $_POST['passwort2'];
	
	if($passwort != $passwort2) {
		$msg =  "Wprowadź identyczne hasła";
	} else { //Speichere neues Passwort und lösche den Code
		$passworthash = password_hash($passwort, PASSWORD_DEFAULT);
		$statement = $pdo->prepare("UPDATE users SET passwort = :passworthash, passwortcode = NULL, passwortcode_time = NULL WHERE id = :userid");
		$result = $statement->execute(array('passworthash' => $passworthash, 'userid'=> $userid ));
		
		if($result) {
			$msg = "Twoje hasło zostało pomyślnie zmienione";
			$showForm = false;
		}
	}
}

include("templates/header.inc.php");
?>

 <div class="container small-container-500">
 
<h1>Przypisz nowe hasło</h1>
<?php 
if(isset($msg)) {
	echo $msg;
}

if($showForm):
?>

<form action="?send=1&amp;userid=<?php echo htmlentities($userid); ?>&amp;code=<?php echo htmlentities($code); ?>" method="post">
<label for="passwort">Proszę wprowadzić nowe hasło:</label><br>
<input type="password" id="passwort" name="passwort" class="form-control" required><br>
 
<label for="passwort2">Wpisz ponownie hasło:</label><br>
<input type="password" id="passwort2" name="passwort2" class="form-control" required><br>
 
<input type="submit" value="Passwort speichern" class="btn btn-lg btn-primary btn-block">
</form>
<?php 
endif;
?>

</div> <!-- /container -->
 

<?php 
include("templates/footer.inc.php")
?>