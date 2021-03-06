<hr>
<div class="container">
  <div class="light-wrapper">
    <div class="container inner">
      <h3 align="center"><b>Sie haben die Wahl von 3 unterschiedlichen Räumen</b></h3>
      <p></p>
      <div class="divide20"></div>
      <div class="pricing row">
        <?php
        /*
         * Dateiname: footer.inc.php
         * Autor: Dennis, Marlin
         *
         * Version: 1.2
         * letzte Änderung: 11. Februar 2019
         *
         * Inhalt: Footer für Seiten mit Raumanzeige
         *
         * Verwendete Funktionen:
         *   build_database_connection
         *   notify_on_exception
         *
         * Definierte Funktionen:
         *
         * globale Variablen:
         */
        require_once(__DIR__ . '/../inc/shared.inc.php');
        $dbConnection = build_database_connection($config);
        try
        {
          $sql = "SELECT groesse, preis FROM preise GROUP BY groesse ORDER By groesse ASC";
          $stmt = $dbConnection->prepare("SELECT groesse, preis FROM preise GROUP BY groesse ORDER By groesse ASC");
          $stmt->execute();
          $rows = $stmt->fetchAll();
        }
        catch (PDOException $e)
        {
          notify_on_exception('Database Select', $config, $sql, $e);
        }
        //"SELECT kennung FROM raeume WHERE groesse = :groesse AND kennung NOT IN (SELECT kennung FROM bestellungen WHERE aktiv = 1) ORDER BY nummer ASC LIMIT 1"
        foreach ($rows as $row)
        {
          $groesse = $row['groesse'];
          $preis = $row['preis'];

          try
          {
            $sql = "SELECT kennung FROM raeume WHERE kennung NOT IN (SELECT kennung FROM bestellungen WHERE (aktiv = 1 OR bis = 0)) GROUP BY groesse ORDER BY nummer ASC";
            $stmt = $dbConnection->prepare("SELECT kennung FROM raeume WHERE groesse = :groesse AND kennung NOT IN (SELECT kennung FROM bestellungen WHERE (aktiv = 1 OR bis = 0)) ORDER BY nummer ASC");
            $stmt->bindParam(':groesse', $groesse);
            $stmt->execute();
            $row = $stmt->fetch();
          }
          catch (PDOException $e)
          {
            notify_on_exception('Database Select', $config, $sql, $e);
          }
          ?>
          <div class="col-sm-4">
            <div class="plan">
              <h2><span class="available"><b><?php echo $groesse; ?>m²</b></span></h2>
              <h4><span class="amount"><span>€</span><?php echo $preis; ?> / p.M.<br/> incl. 19% MwSt.</span></h4>
              <div class="features">
                <ul>
                  <li><h2><span class="amount"><span>€</span>2,00 exkl. MwSt. / m²</span></h2></li>
                  <li><h2><span class="available"><?php echo 'Verfügbarkeit: ' . $stmt->rowCount(); ?>&nbsp;von 4</span>
                  </li>
                  <img class="bestellen" src="images/lager<?php echo $groesse; ?>.png">
                  <li></li>
                  <li></li>
                </ul>
              </div>
              <div class="select">
                <div><a href="<?php echo 'order.php?groesse=' . $groesse ?>" <?php if ($stmt->rowCount() === 0)
                  {
                    echo 'disabled';
                  } ?> class="btn-lg btn-primary btn-block1">Bestellen</a></div>
              </div>
            </div>
          </div>
          <?php
        }
        ?>
      </div>
      <footer>
        <p>Powered by Cringe</p>
      </footer>
    </div>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="js/bootstrap.min.js"></script>
    </body>
    </html>