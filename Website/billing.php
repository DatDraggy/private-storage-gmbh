<?php
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");
require_once("inc/permissions.php");

//Überprüfe, dass der User eingeloggt ist
//Der Aufruf von check_user() muss in alle internen Seiten eingebaut sein
$user = check_user();
$userId = $_SESSION['userid'];
$statement = $pdo->prepare("SELECT right_id FROM user_personal WHERE user_id = :userId");
$statement->bindParam(':userId', $userId);
$result = $statement->execute();
$row = $statement->fetch();
if ($statement->rowCount() === 1) {
  $rightId = $row['right_id'];
}
include("templates/header.inc.php");
?>

<div class="container main-container">

  <h1>Herzlich Willkommen!</h1>

  Hallo <?php echo htmlentities($user['vorname']); ?>,<br>
  Herzlich Willkommen im CSV-Import/Export Bereich!<br><br>

 



  
  <?php
  if(in_array($user['right_id'], $config['administration']['userBilling'])) {
    ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            Kundenabrechnung
            <a href="exportData.php" class="del_btn">CSV Export</a>
        </div>
	<div class="panel-body">
        <table class="table table-bordered">
		<thead>
        <tr>
          <th>Position</th>
          <th>UserID</th>
          <th>Menge</th>
          <th>Datum</th>
        </tr>
		</thead>
		<tbody>
       <?php
        //get records from database
        $stmt = $pdo->prepare("SELECT * FROM zahlungen ORDER BY id DESC");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        foreach ($rows as $row) { ?>
          <tr>
            <td><?php echo $count++; ?></td>
            <td><?php echo $row['user_id']; ?></td>
            <td><?php echo $row['menge']; ?></td>
            <td><?php echo $row['datum']; ?></td>
          </tr>
        <?php }
        } else { ?>
          <tr>
            <td colspan="5">Keine Buchungen zur Abrechnung vorhanden.....</td>
          </tr>
        <?php } ?>

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
include("templates/footer.inc2.php")
?>
