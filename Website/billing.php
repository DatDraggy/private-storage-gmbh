<?php
/*
 * Dateiname: billing.php
 * Autor: Marlin, Dennis
 *
 * Version: 1.1
 * letzte Änderung: 11. Februar 2019
 *
 * Inhalt: Zahlungen und zu zahlendes anzeigen, importieren, exportieren
 *
 * Verwendete Funktionen:
 *   check_user
 *
 * Definierte Funktionen:
 *
 * globale Variablen:
 */
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");
require_once("inc/permissions.php");

//Überprüfe, dass der User eingeloggt ist
//Der Aufruf von check_user() muss in alle internen Seiten eingebaut sein
$user = check_user();
$userId = $_SESSION['userid'];
$rightId = $user['right_id'];

if(in_array($rightId, $config['administration']['userBilling']) && isset($_POST['action'])){
  if($_POST['action'] === 'bezahlt' && !empty($_POST['abrechnungsid']))
  {
    $stmt = $pdo->prepare('UPDATE abrechnung SET bezahlt = 1 WHERE id = :abrechnungsid');
    $stmt->bindParam(':abrechnungsid', $_POST['abrechnungsid']);
    $stmt->execute();
  }
}

include("templates/header.inc.php");
?>

<div class="container main-container">

  <h1>Herzlich Willkommen!</h1>

  Hallo <?php echo htmlentities($user['vorname']); ?>,<br>
  Herzlich Willkommen im CSV-Import/Export Bereich!<br><br>

 



  
  <?php
  if(in_array($rightId, $config['administration']['userBilling']))
  {
    ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            Kundenabrechnung
            <a href="export_data.php" class="del_btn">CSV Export</a>
        </div>
	<div class="panel-body">
   <table class="table table-bordered">
		<thead>
        <tr>
          <th>Position</th>
          <th>UserID</th>
          <th>Preis</th>
          <th>Kennung</th>
          <th>Status</th>
        </tr>
		</thead>
		<tbody>
      <form action="action.php" method="post">
      </form>
        <?php
        //get records from database
        $stmt = $pdo->prepare("SELECT id, user_id, preis, kennung FROM abrechnung WHERE bezahlt = 0");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        $count = 1;
        foreach ($rows as $row)
        { ?>
          <tr>
            <td><?php echo $count++; ?></td>
            <td><?php echo $row['user_id']; ?></td>
            <td><?php echo $row['preis']; ?></td>
            <td><?php echo $row['kennung']; ?></td>
            <td>
              <form method="post">
                <input hidden name="abrechnungsid" value="<?php echo $row['id']; ?>">
                <button type="submit" name="action" value="bezahlt" class="edit_btn">Bezahlt</button>
              </form>
            </td>
          </tr>
        <?php
        }
        if($stmt->rowCount() === 0)
        { ?>
          <tr>
            <td colspan="5">Keine Buchungen zur Abrechnung vorhanden.....</td>
          </tr>
        <?php } ?>
        <p><input type="submit"class="edit_btn" value="Status speichern"/></p>
        </tbody>
      </table>
    </div>
  </div>
  <form enctype="multipart/form-data" action="payment_import.php" method="POST">
  <input type="hidden" name="MAX_FILE_SIZE" value="30000">
  Diese Datei hochladen: <input name="file" type="file">
  <input type="text" name="Import">
  <input type="submit" class="edit_btn" value="CSV Import">
</form>

  		
<?php
  }
include("templates/footer.inc2.php");
?>