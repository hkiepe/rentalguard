<?php
	session_start();
	require_once("inc/config.inc.php");
	require_once("inc/functions.inc.php");

	//Überprüfe, dass der User eingeloggt ist
	//Der Aufruf von check_user() muss in alle internen Seiten eingebaut sein
	$user = check_user();

	include("templates/header.inc.php");
?>

<div class="container main-container">

	<h1>Serdecznie witamy</h1>

	Witam <?php echo htmlentities($user['vorname']); ?>,<br>
	Witamy w strefie wewnętrznej!<br><br>

	<div class="panel panel-default">
 
		<table class="table">
			<tr>
				<th>#</th>
				<th>ID wynajem</th>
				<th>ID pojazd</th>
				<!-- <th>E-Mail</th> -->
			</tr>
			<?php 
			$statement = $pdo->prepare("SELECT * FROM rentals WHERE rp_id = :rentalpoint ORDER BY re_handle");
			$result = $statement->execute(array( 'rentalpoint' => $_SESSION['rentalpoint'] ));
			$count = 1;
			while($row = $statement->fetch()) {
				echo "<tr>";
				echo "<td>".$count++."</td>";
				echo "<td>".$row['re_id']."</td>";
				echo "<td>".$row['re_handle']."</td>";
				// echo '<td><a href="mailto:'.$row['email'].'">'.$row['email'].'</a></td>';
				echo "</tr>";
			}
			?>
		</table>
	</div>


</div>

<?php 
	include("templates/footer.inc.php")
?>
