<?php
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");

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
  Hier ist eine Übersicht ihrer Räume!<br><br>


  <div class="panel panel-default">

    <table class="table">
      <tr>
        <th>Kennung/th>
        <th>Kosten</th>
        <th>Code</th>
      </tr>
      <?php
      $statement = $pdo->prepare("SELECT bestellungen.kennung, preis, code FROM bestellungen INNER JOIN raeume ON raeume.kennung = bestellungen.kennung INNER JOIN preise ON preise.groesse = raeume.groesse WHERE user_id = :userId AND aktiv = 1");
      $statement->bindParam(':userId', $userId);
      $result = $statement->execute();
      $count = 1;
      while ($row = $statement->fetch()) { ?>
        <tr>
          <td><?php echo $row['kennung']; ?></td>
          <td><?php echo $row['kosten']; ?></td>
          <td><?php echo $row['code']; ?></td>
          <td><a href="user_delete.php?userid=<?php echo $row['id']; ?>" class="del_btn">Delete</a></td>
        </tr>
      <?php } ?>


    </table>
  </div>


</div>
<?php
include("templates/footer.inc.php")
?>
