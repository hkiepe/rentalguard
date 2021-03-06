<!DOCTYPE html>
<html lang="pl">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Veloking - Baza danych wynajmu</title>

    <!-- CSS for Rentalform -->
    <link href="css/hide-show-fields-form.css" rel="stylesheet">

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap-theme.min.css"> 

    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet">

    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
    <script type="text/javascript" src='js/jquery-3.4.1.js'></script>
  </head>
  <body>

  <!-- <?php echo "<pre>";
  echo "SESSION ";
	print_r($_SESSION);
	echo "</pre>"; ?> -->

  <nav class="navbar navbar-inverse navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Menu</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php"> Veloking - 
            <?php
              // Display Rentalpoint name for the user where he is logged in.
              if ($_SESSION) {
                $statement = $pdo->prepare("SELECT rp_name FROM rentalpoints WHERE rp_id = :rentalpoint");
                $statement->execute(array( 'rentalpoint' => $_SESSION['rentalpoint'] )); 
                $rentalpoint = $statement->fetch(PDO::FETCH_ASSOC);
                echo "Punkt wynajmu: " . $rentalpoint['rp_name'];
              }
              else {
                print_r("Nie wybrano punktu wynajmu");
              }
            ?>
          </a>
        </div>
        <?php if(!is_checked_in()): ?>
        <div id="navbar" class="navbar-collapse collapse">
          <!-- <form class="navbar-form navbar-right" action="login.php" method="post">
			<table class="login" role="presentation">
				<tbody>
					<tr>
						<td>							
							<div class="input-group">
								<div class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></div>
								<input class="form-control" placeholder="E-Mail" name="email" type="email" required>								
							</div>
						</td>
						<td><input class="form-control" placeholder="Passwort" name="passwort" type="password" value="" required></td>
						<td><button type="submit" class="btn btn-success">Login</button></td>
					</tr>
					<tr>
						<td><label style="margin-bottom: 0px; font-weight: normal;"><input type="checkbox" name="angemeldet_bleiben" value="remember-me" title="Angemeldet bleiben"  checked="checked" style="margin: 0; vertical-align: middle;" /> <small>Angemeldet bleiben</small></label></td>
						<td><small><a href="passwortvergessen.php">Passwort vergessen</a></small></td>
						<td></td>
					</tr>					
				</tbody>
			</table>		
          
            
          </form>          -->
        </div><!--/.navbar-collapse -->
        <?php else: ?>
          <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
              <li><a href="internal.php">Przegląd</a></li>
              <li><a href="closerental.php">Zakończ wynajem</a></li>   
              <li><a href="newrental.php">Wynajmij pojazd</a></li>   
              <li><a href="settings.php">Ustawienia</a></li>
              <li><a href="logout.php">Wyloguj</a></li>
            </ul>   
          </div><!--/.navbar-collapse -->
        <?php endif; ?>
      </div>
    </nav>