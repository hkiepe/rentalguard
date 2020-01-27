<?php
    session_start();
    require_once("inc/config.inc.php");
    require_once("inc/functions.inc.php");
    $user = check_user();
    include("templates/header.inc.php");
    // echo "<pre>";
    // print_r($_SESSION);
    // print_r($_POST);
    // print_r($_GET);
    // echo "</pre>";

/**********************************************************************
 *             1 - Enter new client into Database                     *
 **********************************************************************/

if(isset($_GET['register_new_client'])) {
	$error = false;
	$email = trim($_POST['email']);
	$pesel = trim($_POST['pesel']);
	$phone = trim($_POST['phone']);
	$fname = trim($_POST['fname']);
	$sname = trim($_POST['sname']);
	
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
		} else {
			echo '<br>Niestety wystąpił błąd podczas zapisywania<br>';
		}
	} 
}
$user = check_user();
/**********************************************************************
 *             2 - Select Client                                      *
 **********************************************************************/

if(isset($_GET['select_client'])) {
    $_SESSION['clientID'] = $_POST['client'];
    $error = false;
    // echo "<br>Client Selection";
    // echo "<pre>";
    // print_r($_SESSION);
    // print_r($_POST);
    // print_r($_GET);
    // echo "</pre>";
}

$user = check_user();
/**********************************************************************
 *     3 - Enter the Vehicles for the renting into tmp_rental       *
 **********************************************************************/

if(isset($_GET['register_vehicle'])) {
	$error = false;
    $vehicle = $_POST['vehicle'];
    // $_SESSION['clientID'] = $_POST['client'];
	
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
    // echo "<br>Enter into tmp_rentals";
    // echo "<pre>";
    // print_r($_SESSION);
    // print_r($_POST);
    // print_r($_GET);
    // echo "</pre>";
}

$user = check_user();
/*************************************************************************************
 *                            3 - Count the sales cart                               *
 *************************************************************************************/
if(isset($_GET['register_cart'])) {
	$error = false;
    $vehicle = $_POST['vehicle'];
    
    // Berechnen des Warenkorbs für alle Fahrzeuge in tmp_rental und Preise zu tmp_rentals hinzufügen.
	if (isset($_POST['yesno'])) {
	 	switch ($_POST['yesno']) {
	 		case 'day': $price = 50;
	 			break;
	 		case 'halfHour': $price = 8;
	 			break;
	 		case 'hours_select': (($_POST['hours'] * 15) > 50) ? ($price = 50) : ($_POST['hours'] * 15);
	 			break;
	 		case 'days_select': $price = $_POST['days'] * 50;
	 			break;
	    }
    }

    $statement = $pdo->prepare("UPDATE tmp_rentals SET tmp_price = :price");
    $result = $statement->execute(array( 'price' => $price ));

    if($result) {		
        echo '<br>Cena została dodana.';
        $registerForm = false;
        $rentForm = true;
    } else {
        echo '<br>Nie można dodać ceny.';
        $registerForm = false;
        $rentForm = true;
    }
}

$user = check_user();
/*************************************************************************************
 *     4 - Copy the Vehicles for the renting into rental and delete tmp_rental       *
 *************************************************************************************/

if(isset($_GET['register_whole_rental'])) {
	$error = false;

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
		$statement = $pdo->prepare("INSERT INTO rentals (re_unique_id, re_handle, rp_id, cl_id, price) SELECT :re_unique_id, tmp_re_handle, tmp_rp_id, tmp_cl_id, tmp_price FROM tmp_rentals");
		$result = $statement->execute(array('re_unique_id' => $re_unique_id));
		if($result) {		
			echo '<br>Wynajem został utworzony.</a><br>';
			// Daten werden aus tmp Tabelle gelöscht.
			$statement = $pdo->prepare("TRUNCATE TABLE tmp_rentals");
			$result = $statement->execute();
		} else {
			echo '<br>Niestety wystąpił błąd. Pojazdy są już zawarte w bazie danych i nie można ich pożyczyć po raz drugi.';
		}
	}

    // End client Session and redirect to create client form.
    unset($_SESSION['rentalpoint']);
	header ( 'Location: newrental.php' );
	
}

$user = check_user();

?>
<!--/**************************************************************************
     **************************************************************************
     **                                                                      **
     **                           Data Form                                  **
     **                                                                      **
     **************************************************************************
     ************************************************************************** -->

<form class="p-3" action="" method="post">
    <a href="#" onclick="swap('hidemeta')">Zarejestruj klienta albo wyszukaj istniejący klient</a>
    <div id="hidemeta" style="display:none">
        <!--/**********************************************************************
        *                        Neukunden anlegen                               *
        ********************************************************************** -->
        <a href="#" class="change-price">Wprowadź klienta lub wybierz klienta</a>
        <div id="newClient" class="none-sq-price">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" class="form-control" id="email" name="email">
            </div>
            <div class="form-group">
                <label for="pesel">PESEL</label>
                <input type="text" class="form-control" id="pesel" name="pesel">
            </div>
            <div class="form-group">
                <label for="phone">Telefon</label>
                <input type="text" class="form-control" id="phone" name="phone">
            </div>
            <div class="form-group">
                <label for="fname">Imię:</label>
                <input type="text" class="form-control" id="fname" name="fname">
            </div>
            <div class="form-group">
                <label for="sname">Nazwisko:</label>
                <input type="text" class="form-control" id="sname" name="sname">
            </div>
            <button formaction="?register_new_client=1" class="btn btn-lg btn-primary btn-block">Zarejestrować</button>
        </div>
    <div id="selectClient" class="sq-price">
        <div class="form-group">
            <!--/**********************************************************************
            *                       Kunden auswählen                               *
            ********************************************************************** -->
            <label for="client">Wyszukaj istniejący klient</label>
            <input type="select" class="form-control" list="client" name="client">
                <datalist id="client">
					<?php
					//Get the values for client names
						try {
							$statement = $pdo->prepare("SELECT id, email FROM clients");
							$statement->execute();
                            $clients = $statement->fetchAll();
                            print_r($clients);
						} catch(Exception $ex) {
							echo($ex -> getMessage());
						}
						foreach($clients as $row) {
							echo '<option name="'.$row['id'].'" value="'.$row['id'].'">'.$row['email'].'</option>';
						}
					?>
                </datalist>
            </iput>
        </div>
        <button formaction="?select_client=1" class="btn btn-lg btn-primary btn-block">wybierz klienta</button>
    </div>
    </div>
    <br>
    <!--/**********************************************************************
     *               Kunden suchen und Fahrezuge eintragen                    *
     ********************************************************************** -->
    <a href="#" onclick="swap('hidemetb')">Wprowadź pojazdy</a>
    <div id="hidemetb" style="display:none">
        <div class="form-group">
                <?php
                    // Display Selected Client Name
                    if (isset($_SESSION['clientID'])) {
                        $statement = $pdo->prepare("SELECT id, fname, sname FROM clients WHERE id = :clientID");
                        $statement->execute(array( 'clientID' => $_SESSION['clientID'] ));
                        $client = $statement->fetch(PDO::FETCH_ASSOC);
                        echo "Wybrany klient: " . $client['fname'] . " " . $client['sname'];
                    } else {
                        print_r("Nie wybrano klienta.");
                    }
                ?><br>
            <label for="vehicle">Wybierz pojazd</label>
            <input type="select" class="form-control" list="vehicle" name="vehicle" placeholder="Proszę wybrać pojazd">
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
        </div>
        <div class="form-group">
	        <!-- Show Table with Vehicles in tmp_rentals -->
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
        <button formaction="?register_vehicle=1" class="btn btn-lg btn-primary btn-block">Dodaj pojazdy</button>
    </div><br>
    <!--/**************************************************************************
     *                   Warenkorb auswählen und eintragen                        *
     ************************************************************************** -->
    <a href="#" onclick="swap('hidemetc')">Oblicz koszyk</a>
    <div id="hidemetc" style="display:none">
        <fieldset>
            <label for="day">Cały dzień</label><input type="radio" name="yesno" id="day" value="day"/>
            <label for="halfHour">Pół godziny</label><input type="radio" name="yesno" id="halfHour" value="halfHour"/>
            <label for="hours_select">Godzin</label><input type="radio" onclick="javascript:yesnoCheck();" name="yesno" id="yesCheck" value="hours_select"/>
            <label for="days_select">Dni</label><input type="radio" onclick="javascript:yesnoCheck();" name="yesno" id="noCheck" value="days_select"/>
            <div id="ifYes" style="display:none">
                <label for="hours">Godzin</label><input type="text" name="hours" id="hours"/>
            </div>
            <div id="ifNo" style="display:none">
                <label for="days">Dni</label><input type="text" name="days" id="days"/>
            </div>
        </fieldset>
        <button formaction="?register_cart=1" class="btn btn-lg btn-primary btn-block">Oblicz koszyk</button>
    </div><br>
        <!--/**************************************************************************
     *                             Warenkorb abschicken                               *
     ****************************************************************************** -->
    <a href="#" onclick="swap('hidemetd')">Wyślij koszyk</a>
    <div id="hidemetd" style="display:none">
        <button formaction="?register_whole_rental=1" class="btn btn-lg btn-primary btn-block">Wyślij koszyk</button>
    </div>
    <!-- usw. -->
</form>
<?php
include("templates/footer.inc.php")
?>
<script src="https://code.jquery.com/jquery-3.4.1.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"></script>
<script type="text/javascript">
    var text = new Array();
    text[0] = 'hidemeta';
    text[1] = 'hidemetb';
    text[2] = 'hidemetc';
    text[3] = 'hidemetd';
    // usw.
    function swap(id) {
        if(document.getElementById(id).style.display=="none") {
            for(i=0;i<text.length;i++) {
                document.getElementById(text[i]).style.display="none";
            }
            document.getElementById(id).style.display="block";
        } else {
            document.getElementById(id).style.display="none";
        }
    }

// SCRIPT for delete rows
    $(document).ready(function(){

        // Delete 
        $('.delete').click(function(){
        var el = this;
        var id = this.id;
        var splitid = id.split("_");

        // Delete id
        var deleteid = splitid[1];
            console.log("Test");
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

    $('.none-sq-price').hide();
$('.change-price').on('click',
  function() {
    $('.sq-price, .none-sq-price').toggle(200);
  }
);
</script>