
<hr>
<div class="container">
  <div class="light-wrapper">
    <div class="container inner">
      <h3>Sie haben die Wahl von 3 verschiedenen Räumen</h3>
      <p></p>
      <div class="divide20"></div>
      <div class="pricing row">
        <?php
        require_once('../inc/shared.inc.php');
        $dbConnection = buildDatabasConnection();
        try{
          $sql = "SELECT raeume.groesse, preis FROM raeume INNER JOIN preise ON raeume.groesse = preise.groesse GROUP BY raeume.groesse";
          $stmt = $dbConnection->prepare("SELECT raeume.groesse, preis FROM raeume INNER JOIN preise ON raeume.groesse = preise.groesse GROUP BY raeume.groesse");
          $stmt->execute();
          $rows = $stmt->fetchAll();
        } catch (PDOException $e) {
          notifyOnException('Database Select', $config, $sql, $e);
        }

        foreach($rows as $row) {
          $groesse = $row['groesse'];
          $preis = $row['groesse'];
          ?>
          <div class="col-sm-4">
            <div class="plan">
              <h3><?php echo $groesse; ?>m²</h3>
              <h4><span class="amount"><span>€</span><?php echo $preis; ?></span></h4>
              <div class="features">
                <ul>
                  <li></li>
                  <li></li>
                  <img class="bestellen" src="images/lager<?php echo $groesse; ?>.png">
                  <li></li>
                  <li></li>
                </ul>
              </div>
              <div class="select">
                <div><a href="#" class="btn-lg btn-primary btn-block1">Bestellen</a></div>
              </div>
            </div>
          </div>
          <?php
        }
        ?>
      </div>
      <footer>
        <p>Powered by StorageGmbH</p>
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