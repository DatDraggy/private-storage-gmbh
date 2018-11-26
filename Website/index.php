<?php 
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");
include("templates/header.inc.php")
?>

  

    

    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron">
      <div class="container">
        <h1>Herzlich Willkommen bei StorageGmbH</h1>
        <p></p>
        <p><a class="btn btn-primary btn-lg" href="register.php" role="button">Jetzt registrieren</a></p>
      </div>
    </div>

    <div class="container">
      <!-- Example row of columns -->
      <div class="row">
        <div class="col-md-4">
          <h2>Mieträume in unterschiedlichen Größen</h2>
          <ul>
          	<li>Registrierung & loslegen</li> 
          	<li>Monatliche Laufzeit</li>
          	<li>Bezahlung einfach per Lastschrift</li>
          </ul>
        </div>
      </div>
	</div> <!-- /container -->
      

  
<?php 
include("templates/footer.inc.php")
?>
