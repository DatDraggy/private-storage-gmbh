<?php
/*
 * Dateiname: logout.php
 * Autor: Marlin
 *
 * Version: 1
 * letzte Änderung: 11. Februar 2019
 *
 * Inhalt: Zerstört session und loggt user aus
 *
 * Verwendete Funktionen:
 *
 * Definierte Funktionen:
 *
 * globale Variablen:
 */
session_start();
session_destroy();
unset($_SESSION['userid']);

//Remove Cookies
setcookie("identifier", "", time() - (3600 * 24 * 365));
setcookie("securitytoken", "", time() - (3600 * 24 * 365));

require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");

include("templates/header.inc.php");
?>

  <div class="container main-container">
    Der Logout war erfolgreich. <a href="login.php">Zurück zum Login</a>.
  </div>
<?php
include("templates/footer.inc.php")
?>