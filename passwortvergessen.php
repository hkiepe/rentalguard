<?php 
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");

include("templates/header.inc.php");
?>
 <div class="container small-container-330">
	<h2 >Zapomniałem hasła</h2>


<?php 
$showForm = true;
 
if(isset($_GET['send']) ) {
	if(!isset($_POST['email']) || empty($_POST['email'])) {
		$error = "<b>Proszę podać adres e-mail</b>";
	} else {
		$statement = $pdo->prepare("SELECT * FROM users WHERE email = :email");
		$result = $statement->execute(array('email' => $_POST['email']));
		$user = $statement->fetch();		
 
		if($user === false) {
			$error = "<b>Nie znaleziono użytkownika</b>";
		} else {
			
			$passwortcode = random_string();
			$statement = $pdo->prepare("UPDATE users SET passwortcode = :passwortcode, passwortcode_time = NOW() WHERE id = :userid");
			$result = $statement->execute(array('passwortcode' => sha1($passwortcode), 'userid' => $user['id']));
			
			$empfaenger = $user['email'];
			$betreff = "Nowe hasło do twojego konta na www.rentalguard.org"; //Ersetzt hier den Domain-Namen
			$from = "Od: Imię Nazwisko <inf@veloking.pl>"; //Ersetzt hier euren Name und E-Mail-Adresse
			$url_passwortcode = getSiteURL().'passwortzuruecksetzen.php?userid='.$user['id'].'&code='.$passwortcode; //Setzt hier eure richtige Domain ein
			$text = 'Witam '.$user['vorname'].',
			zażądano nowego hasła do twojego konta na rentalguard.org. Aby przypisać nowe hasło, przejdź do następującej witryny w ciągu 24 godzin:
'.$url_passwortcode.'
 
Jeśli pamiętasz hasło lub nie poprosiłeś o nie, zignoruj ​​ten e-mail.
 
Pozdrawiam
twój zespół Veloking';
			
			//echo $text;
			 
			mail($empfaenger, $betreff, $text, $from);
 
			echo "Link do zresetowania hasła został wysłany na Twój adres e-mail.";	
			$showForm = false;
		}
	}
}
 
if($showForm):
?> 
	Wpisz tutaj swój adres e-mail, aby poprosić o nowe hasło.<br><br>
	 
	<?php
	if(isset($error) && !empty($error)) {
		echo $error;
	}
	
	?>
	<form action="?send=1" method="post">
		<label for="inputEmail">Email</label>
		<input class="form-control" placeholder="E-Mail" name="email" type="email" value="<?php echo isset($_POST['email']) ? htmlentities($_POST['email']) : ''; ?>" required>
		<br>
		<input  class="btn btn-lg btn-primary btn-block" type="submit" value="Neues Passwort">
	</form> 
<?php
endif; //Endif von if($showForm)
?>

</div> <!-- /container -->
 

<?php 
include("templates/footer.inc.php")
?>