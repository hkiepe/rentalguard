<?php 
	session_start();
	require_once("inc/config.inc.php");
	require_once("inc/functions.inc.php");
	$user = check_user();
	include("templates/header.inc.php");
?>
<div class="container main-container registration-form">
<?php
$registerForm = true; //Variable if client registration formular should be shown.
$rentForm = false; //Variable if rent formular should be shown.

/**********************************************************************
 *             1 - Enter new client into Database                     *
 **********************************************************************/

if(isset($_GET['register'])) {
	$error = false;
	$email = trim($_POST['email']);
	$pesel = trim($_POST['pesel']);
	$phone = trim($_POST['phone']);
	$fname = trim($_POST['fname']);
	$sname = trim($_POST['sname']);
	// echo $email . ", " . $pesel . ", " . $phone . ", " . $fname . ", " . $sname;
	
	if(empty($email) || empty($pesel) || empty($phone) || empty($fname) || empty($sname)) {
		echo 'Proszę wypełnić wszystkie pola<br>';
		$error = true;
	}
  
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		echo 'Proszę podać poprawny adres e-mail<br>';
		$error = true;
	}
	
	//Überprüfe, dass die E-Mail-Adresse noch nicht registriert wurde
	if(!$error) { 
		$statement = $pdo->prepare("SELECT * FROM clients WHERE email = :email");
		$result = $statement->execute(array('email' => $email));
		$user = $statement->fetch();
		
		if($user !== false) {
			echo '<br>Ten adres e-mail jest już zajęty<br>';
			$error = true;
		}
	}
	
	//Keine Fehler, wir können den Nutzer registrieren
	if(!$error) {
		
		$statement = $pdo->prepare("INSERT INTO clients (email, pesel, phone, fname, sname) VALUES (:email, :pesel, :phone, :fname, :sname)");
		$result = $statement->execute(array('email' => $email, 'pesel' => $pesel, 'phone' => $phone, 'fname' => $fname, 'sname' => $sname));
		$_SESSION['clientID'] = $pdo->lastInsertId();

		if($result) {		
			echo '<br>Klient został utworzony pomyślnie.';
			$registerForm = false;
			$rentForm = true;
			// echo "<br><br><p>" . $registerForm . ", " . $rentForm . ", " . $email . ", " . $pesel . ", " . $phone . ", " . $fname . ", " . $sname . ", " . $_SESSION['clientID'] . "</p>";
		} else {
			echo '<br>Niestety wystąpił błąd podczas zapisywania<br>';
		}
	} 
}
$user = check_user();

/**********************************************************************
 *     2 - Enter the Vehicles for the renting into the database       *
 **********************************************************************/

if(isset($_GET['rental'])) {
	$error = false;
	$vehicle = $_POST['vehicle'];
	
	/* Check if in dropdown is a value */
	if(empty($vehicle)) {
		echo '<br>Proszę podać identyfikator pojazdu.<br>';
		$registerForm = false; //Variable if client registration formular should be shown.
		$rentForm = true;
		$error = true;
	}
	
	/* Überprüfen in Datenbank, ob das Fahrzeug wirklich existiert. Wenn das vehicle gefunden wird, wird "num" um eins hoch gesetzt.
	Wenn Das Fahrzeug nicht existiert, wird "$error = true" gesetzt. */
	$statement = $pdo->prepare("SELECT COUNT(*) AS num FROM vehicles WHERE vh_handle = :vehicle");
	$statement->execute(array( 'vehicle' => $vehicle ));
	$found = $statement->fetch(PDO::FETCH_ASSOC);
	if ($found['num'] <= 0 && !empty($vehicle)) {
		$error = true;
		echo '<br>Pojazd nie istnieje w bazie danych.<br>';
			$registerForm = false;
			$rentForm = true;
			$found['num'] = 0;

	}

	/* Überprüfen in rentals table ob das Fahrzeug schon ausgeliehen wurde. Wenn Ja kann es nicht zu tmp_rentals hinzugefügt werden. */
	$statement = $pdo->prepare("SELECT COUNT(*) AS num FROM rentals WHERE re_handle = :vehicle");
	$statement->execute(array( 'vehicle' => $vehicle ));
	$found = $statement->fetch(PDO::FETCH_ASSOC);
	if ($found['num'] > 0) {
		$error = true;
		echo '<br>Pojazd jest już wypożyczony.<br>';
			$registerForm = false;
			$rentForm = true;

	}

	// // Berechnen des Warenkorbs
	// if (isset($_POST['yesno'])) {
	// 	switch ($_POST['yesno']) {
	// 		case 'day': $price = 50;
	// 			break;
	// 		case 'halfHour': $price = 8;
	// 			break;
	// 		case 'hours_select': (($_POST['hours'] * 15) > 50) ? ($price = 50) : ($_POST['hours'] * 15);
	// 			break;
	// 		case 'days_select': $price = $_POST['days'] * 50;
	// 			break;
	//    }
	// }

	// Fahrzeug zu tmp_rentals hinzufügen
	if(!$error) {
		$statement = $pdo->prepare( "INSERT INTO tmp_rentals ( tmp_re_handle, tmp_rp_id, tmp_cl_id ) VALUES ( :vehicle, :rentalpoint, :clientID )");
		$result = $statement->execute(array( 'vehicle' => $vehicle, 'rentalpoint' => $_SESSION['rentalpoint'], 'clientID' => $_SESSION['clientID'] ));
		
		if($result) {		
			echo '<br>Pojazd został dodany.';
			$registerForm = false;
			$rentForm = true;
		} else {
			echo '<br>Niestety wystąpił błąd podczas zapisywania - Pojazd jest już wypożyczony lub nie istnieje w bazie danych.';
			$registerForm = false;
			$rentForm = true;
		}
	}
}

// Create unique ID for the rental and copy the whole tmp_rental into the rentals table and delete entries of tmp_rentals table.
if(isset($_GET['create_rental'])) {
	$error = false;
	$registerForm = false;
	$rentForm = true;

	// Create random unique ID for the rental and check in rentals if ID exists. If yes try again.
    $statement = $pdo->prepare("SELECT re_unique_id FROM rentals GROUP BY re_unique_id");
    $statement->execute();
	$re_unique_id = $statement->fetchAll();
    do {
		$i = 0;
        $randID = rand();
	    foreach($re_unique_id as $row) {
			if ($row == $randID) {
				$i++;
			}
	    }
	} while ($i != 0);
	$re_unique_id = $randID;

	// Copy the whole tmp_rental into the rentals table and delete entries of tmp_rentals table.
	if(!$error) {
		// Daten werden rüber kopiert
		$statement = $pdo->prepare("INSERT INTO rentals (re_unique_id, re_handle, rp_id, cl_id) SELECT :re_unique_id, tmp_re_handle, tmp_rp_id, tmp_cl_id FROM tmp_rentals");
		$result = $statement->execute(array('re_unique_id' => $re_unique_id));
		if($result) {		
			echo '<br>Wynajem został utworzony.</a><br>';
			$registerForm = false;
			$rentForm = true;
			// Daten werden aus tmp Tabelle gelöscht.
			$statement = $pdo->prepare("TRUNCATE TABLE tmp_rentals");
			$result = $statement->execute();
		} else {
			echo '<br>Niestety wystąpił błąd. Pojazdy są już zawarte w bazie danych i nie można ich pożyczyć po raz drugi.';
			$registerForm = false;
			$rentForm = true;
		}
	}

	// Insert Price into rental table
	if(!$error) {
		$statement = $pdo->prepare("UPDATE rentals (re_handle, rp_id, cl_id) SELECT tmp_re_handle, tmp_rp_id, tmp_cl_id FROM tmp_rentals");
		$result = $statement->execute();
		if($result) {		
			echo '<br>Wynajem został utworzony.</a><br>';
			$registerForm = false;
			$rentForm = true;
			// Daten werden aus tmp Tabelle gelöscht.
			$statement = $pdo->prepare("TRUNCATE TABLE tmp_rentals");
			$result = $statement->execute();
		} else {
			echo '<br>Niestety wystąpił błąd. Pojazdy są już zawarte w bazie danych i nie można ich pożyczyć po raz drugi.';
			$registerForm = false;
			$rentForm = true;
		}
	}

	// End client Session and redirect to create client form.
	unset($_SESSION['clientID']);
	header ( 'Location: newclient.php' );
	
}

$user = check_user();

/**********************************************************************
 * 		        3 - Form for entering client Data                     *
 **********************************************************************/

if($registerForm) {
?>
<h1>Wprowadź nowego klienta</h1>
<form action="?register=1" method="post">

	<div class="form-group">
		<label for="email">Email:</label>
		<input type="email" id="email" size="40" maxlength="250" name="email" class="form-control" required>
	</div>

	<div class="form-group">
		<label for="pesel">PESEL:</label>
		<input type="text" id="pesel" size="40" maxlength="250" name="pesel" class="form-control" required>
	</div>

	<div class="form-group">
		<label for="phone">Telefon:</label>
		<input type="text" id="phone" size="40"  maxlength="250" name="phone" class="form-control" required>
	</div>

	<div class="form-group">
		<label for="fname">Imię:</label>
		<input type="text" id="fname" size="40" maxlength="250" name="fname" class="form-control" required>
	</div>

	<div class="form-group">
		<label for="sname">Nazwisko:</label>
		<input type="text" id="sname" size="40"  maxlength="250" name="sname" class="form-control" required>
	</div>

	<button type="submit" class="btn btn-lg btn-primary btn-block">Zarejestrować</button>

</form>
 
<?php
} //Ende von if($registerForm)

/**********************************************************************
 * 		        4 - Form for entering rental Data                     *
 **********************************************************************/

if($rentForm == true) {	
?>
<p>Wprowadź informacje o wynajmie dla wybranego klienta.</p>
<form action="" method="post">
	<div class="form-group">
		<fieldset>
		<legend>
			<?php
				// Display Client name at the top of the form.
				if ($_SESSION['clientID']) {
					$statement = $pdo->prepare("SELECT fname, sname FROM clients WHERE id = :clientID");
					$statement->execute(array( 'clientID' => $_SESSION['clientID'] ));
					$client = $statement->fetch(PDO::FETCH_ASSOC);
					echo "Wybrany klient: " . $client['fname'] . " " . $client['sname'];
				} else {
					print_r("Nie wybrano klienta.");
				}
			?>
		</legend>
			<label for="vehicle">Numer pojazdu:</label>
			<input type="search" name="vehicle" list="vehicle" id="vehicle" size="40" maxlength="250" class="form-control">
				<datalist id="vehicle">
					<?php
					//Get the values for the dropdown with rentalpoints
						try {
							$statement = $pdo->prepare("SELECT vehicles.vh_handle, vehicles.vh_id, vehicles.vh_rp_id FROM vehicles WHERE (vehicles.vh_handle NOT IN (SELECT rentals.re_handle FROM rentals)) AND (vehicles.vh_handle NOT IN (SELECT tmp_rentals.tmp_re_handle FROM tmp_rentals)) AND vehicles.vh_rp_id = :rentalpoint");
							$statement->execute(array('rentalpoint' => $_SESSION['rentalpoint']));
							$vehicles = $statement->fetchAll();
						} catch(Exception $ex) {
							echo($ex -> getMessage());
						}
						foreach($vehicles as $row) {
							echo '<option value="'.$row['vh_handle'].'">'.$row['vh_handle'].'</option>';
						}
					?>
				</datalist>
			</input>
		</fieldset>
	</div>
	<!-- Show Table with Vehicles in tmp_rentals -->
	<div class="panel panel-default">
 		<table class="table" id="tmp_rentals">
			<tr>
				<th>#</th>
				<th>ID pojazd</th>
				<th>Usuń pojazd</th>
			</tr>
			<?php
				$statement = $pdo->prepare("SELECT * FROM tmp_rentals WHERE tmp_cl_id = :clientID ORDER BY tmp_re_handle");
				$result = $statement->execute(array( 'clientID' => $_SESSION['clientID'] ));
				$count = 1;
				while($row = $statement->fetch()) {
					echo "<tr>";
					echo "<td>".$count++."</td>";
					echo "<td>".$row['tmp_re_handle']."</td>";
					echo '<td><button type="button" class="delete btn btn-primary" name="delete_row" value="delete_row" id="del_' . $row['tmp_id'] . '">Usuń</button></td>';
					echo "</tr>";
				}
			?>
		</table>
	</div>
	<button formaction="?rental=1" type="submit" class="btn btn-lg btn-primary btn-block">Dodaj pojazd</button>
	
	<fieldset>
		<label for="day">Ganzer Tag</label><input type="radio" name="yesno" id="day" value="day" size="40" maxlength="250" class="form-control"/>
		<label for="halfHour">Halbe Stunde</label><input type="radio" name="yesno" id="halfHour" value="halfHour" size="40" maxlength="250" class="form-control"/>
		<label for="hours_select">Stunden</label><input type="radio" onclick="javascript:yesnoCheck();" name="yesno" id="yesCheck" value="hours_select" size="40" maxlength="250" class="form-control"/>
		<label for="days_select">Tage</label><input type="radio" onclick="javascript:yesnoCheck();" name="yesno" id="noCheck" value="days_select" size="40" maxlength="250" class="form-control"/>
		<div id="ifYes" style="display:none">
			<label for="hours">Stunden</label><input type="text" name="hours" id="hours" size="40" maxlength="250" class="form-control"/>
		</div>
		<div id="ifNo" style="display:none">
			<label for="days">Tage</label><input type="text" name="days" id="days" size="40" maxlength="250" class="form-control"/>
		</div>
	</fieldset>

	<button formaction="?create_rental=1" type="submit" class="create_rental btn btn-lg btn-primary btn-block">Stworzyć wynajem</button>
</form>

<?php
	} // Ende von if($rentForm)
?>

</div>
<?php
include("templates/footer.inc.php")
?>
	<script type='text/javascript'>

	// SCRIPT for delete rows
        $(document).ready(function(){

            // Delete 
            $('.delete').click(function(){
            var el = this;
            var id = this.id;
			var splitid = id.split("_");

            // Delete id
            var deleteid = splitid[1];

            // AJAX Request
            $.ajax({
                url: 'remove.php',
                type: 'POST',
                data: { tmp_id:deleteid },
                success: function(response){
                if(response){
                // Remove row from HTML Table
				$(el).closest("tr").css('background','tomato');
				console.log((el).closest("tr"));
                $(el).closest("tr").fadeOut(800,function(){
                $(this).remove();
                });
                }else{
                alert('Invalid ID.');
                }

            }
            });

            });

		});

	// SCRIPT for select time options
	function yesnoCheck() {
        if (document.getElementById('yesCheck').checked) {
            document.getElementById('ifYes').style.display = 'block';
            document.getElementById('ifNo').style.display = 'none';
        } 
        else if(document.getElementById('noCheck').checked) {
            document.getElementById('ifNo').style.display = 'block';
            document.getElementById('ifYes').style.display = 'none';
      }
    }
    </script>