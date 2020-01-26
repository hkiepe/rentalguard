<?php
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");

//Überprüfe, dass der User eingeloggt ist
//Der Aufruf von check_user() muss in alle internen Seiten eingebaut sein
$user = check_user();

include("templates/header.inc.php");

if(isset($_GET['save'])) {
	$save = $_GET['save'];
	
	if($save == 'rental') {
		$vehicle = trim($_POST['vehicles']);
		
		if($vehicle == "") {
			$error_msg = "Proszę podać identyfikator pojazdu.";
		} else {
            // Überprüfen in Datenbank, ob der Verleih wirklich existiert. Wenn das vehicle gefunden wird, wird "num" um eins hoch gesetzt.          
            $statement = $pdo->prepare("SELECT COUNT(*) AS num FROM rentals WHERE re_handle = :vehicle");
            $statement->execute(array( 'vehicle' => $vehicle )); 
            $found = $statement->fetch(PDO::FETCH_ASSOC);
            if ($found['num'] > 0) {
                $statement = $pdo->prepare( "DELETE FROM rentals WHERE re_handle = :vehicle");
                $result = $statement->execute(array( 'vehicle' => $vehicle ));
    
                $statement = $pdo->prepare( "UPDATE vehicles SET vh_rp_id = :rentalpoint WHERE vh_handle = :vehicle");
                $result = $statement->execute(array( 'vehicle' => $vehicle, 'rentalpoint' => $_SESSION['rentalpoint'] ));
                
                $success_msg = "Dane zostały zapisane pomyślnie.";
            } else {
                $error_msg = "Pojazd nie jest wynajmowany";
            }
		}
	}
}

$user = check_user();

?>

<div class="container main-container">

<h1>Zakończ wynajem</h1>

<?php 
if(isset($success_msg) && !empty($success_msg)):
?>
	<div class="alert alert-success">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
	  	<?php echo $success_msg; ?>
	</div>
<?php 
endif;
?>

<?php 
if(isset($error_msg) && !empty($error_msg)):
?>
	<div class="alert alert-danger">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
	  	<?php echo $error_msg; ?>
	</div>
<?php 
endif;
?>

<div>

  <!-- Formular um Verleih einzutragen-->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="data">
    	<br>
    	<form action="?save=rental" method="post" class="form-horizontal">
    		<div class="form-group">
    			<label for="inputRental" class="col-sm-2 control-label">Numer pojazdu</label>
    			<div class="col-sm-10">
				<input type="search" name="vehicles" list="vehicles">
					<datalist id="vehicles">
						<?php
							//Get the values for the dropdown with rentalpoints
							try {
								$statement = $pdo->prepare("SELECT * FROM rentals");
								$statement->execute();
								$vehicles = $statement->fetchAll();
							} catch(Exception $ex) {
								echo($ex -> getMessage());
							}
							foreach($vehicles as $row) {
								echo '<option value="'.$row['re_handle'].'">'.$row['re_handle'].'</option>';
							}
						?>
					</datalist>	
				</input>
    			</div>
    		</div>
    		
    		<div class="form-group">
			    <div class="col-sm-offset-2 col-sm-10">
			      <button type="submit" class="btn btn-primary">Zapisz</button>
			    </div>
			</div>
    	</form>
    </div>
  </div>

</div>


</div>
<?php 
include("templates/footer.inc.php")
?>
