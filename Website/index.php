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
    <p align="center"><a class="btn btn-primary btn-lg" href="register.php" role="button">Jetzt registrieren</a></p>
  </div>
</div>

<div class="container">
  <!-- Example row of columns -->
  <div class="row">
    <div class="col-md-12" align="center">
      <h2>Mit uns schaffen Sie sich Platz</h2>
      <p>Unser Selfstorage Service in Elmshorn bietet Ihnen die Lösung für kurz- und langfristige Lagerengpässe; egal ob privat oder gewerblich.<br/><br/>
        <u><b>Jeder kennt es:</b></u><br/> Umzüge – vielleicht sogar ins Ausland, Familienzuwachs, Haushaltsauflösungen, Auslandssemester oder einfach Hobbys die saisonal zusätzlichen Platz erfordern –
        man muss spontan reagieren und schnell eine geeignete Lösung finden.
        Wir unterstützen Sie bei Ihrer Planung/Projekt! Mit unserer Unterstützung spüren Sie, wie einfach alles sein.
        Mit unserem Support können Sie ganz individuell einlagern oder auslagern.<br/><br/>
        Die Lagerräume bieten wir Ihnen komfortable in 3 unterschiedlichen Größen an.
        Unsere Lagerräume sind modern, beheizt, alarmgesichert und videoüberwacht; eine bequeme Gesamtlösung vor Ort in Elmshorn.
        Mit uns können Sie Ihre Lagergüter, trocken, sicher und zeitlich flexibel einlagern!<br/><br/>
        Für gewerbliche Nutzer: Sie benötigen Archiv- und Lagerflächen. Mit und bei uns finden Sie die richtigen Flächen!<br/>
        Unsere Lagerflächen sind arbeitstäglich zugänglich in den Zeiten von
        <em>08.00 -17.00 Uhr</em> und nach vorheriger Vereinbarungen auch zu allen anderen Zeiten (einschl. Sonn- und Feiertage).
        Lagerhilfen dürfen vor Ort kostenfrei genutzt werden – Hubwagen</p>
    </div>
  </div>
</div> <!-- /container -->


<?php
include("templates/footer.inc.php")
?>
