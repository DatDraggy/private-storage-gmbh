<?php
/*
 * Dateiname: rooms.php
 * Autor: Marlin, Dennis
 *
 * Version: 0.9
 * letzte Änderung: 11. Februar 2019
 *
 * Inhalt: Räume reservieren, Code anpassen und kündigen
 *
 * Verwendete Funktionen:
 *   check_user
 *
 * Definierte Funktionen:
 *
 * globale Variablen:
 *   _SESSION
 */
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");

//Überprüfe, dass der User eingeloggt ist
//Der Aufruf von check_user() muss in alle internen Seiten eingebaut sein
$user = check_user();
$userId = $_SESSION['userid'];
if (!empty($_POST['roomcode']) && !empty($_POST['roomid']) && $_POST['action'] == 'change')
{
  if (strlen($_POST['roomcode']) >= 4 && is_numeric($_POST['roomcode']))
  {
    $statement = $pdo->prepare('SELECT bestellungen.kennung, preis, code FROM bestellungen INNER JOIN raeume ON raeume.kennung = bestellungen.kennung INNER JOIN preise ON preise.groesse = raeume.groesse WHERE user_id = :userId AND aktiv = 1 AND raeume.kennung = :kennung');
    $statement->bindParam(':kennung', $_POST['roomid']);
    $statement->bindParam(':userId', $userId);
    $statement->execute();
    $row = $statement->fetch();
    if ($statement->rowCount() == 1)
    {
      $statement = $pdo->prepare('UPDATE raeume SET code = :code WHERE kennung = :kennung');
      $statement->bindParam(':code', $_POST['roomcode']);
      $statement->bindParam(':kennung', $_POST['roomid']);
      $statement->execute();
    }
  }
}
else if ($_POST['action'] == 'delete' && !empty($_POST['roomid']))
{
  $statement = $pdo->prepare('SELECT bestellungen.kennung, preis, code FROM bestellungen INNER JOIN raeume ON raeume.kennung = bestellungen.kennung INNER JOIN preise ON preise.groesse = raeume.groesse WHERE user_id = :userId AND aktiv = 1 AND raeume.kennung = :kennung');
  $statement->bindParam(':kennung', $_POST['roomid']);
  $statement->bindParam(':userId', $userId);
  $statement->execute();
  $row = $statement->fetch();
  if ($statement->rowCount() == 1)
  {
    $endOfMonth = strtotime(date("Y-m-t"));
    $statement = $pdo->prepare('UPDATE bestellungen SET bis = :bis WHERE user_id = :userId AND aktiv = 1 AND bis = 0 AND kennung = :kennung');
    $statement->bindParam(':bis', $endOfMonth);
    $statement->bindParam(':userId', $userId);
    $statement->bindParam(':kennung', $_POST['roomid']);
    $statement->execute();
  }
  else
  {
    $statement = $pdo->prepare('SELECT bestellungen.kennung, preis, code FROM bestellungen INNER JOIN raeume ON raeume.kennung = bestellungen.kennung INNER JOIN preise ON preise.groesse = raeume.groesse WHERE user_id = :userId AND aktiv = 0 AND bis = 0 AND raeume.kennung = :kennung');
    $statement->bindParam(':kennung', $_POST['roomid']);
    $statement->bindParam(':userId', $userId);
    $statement->execute();
    $row = $statement->fetch();
    if ($statement->rowCount() == 1)
    {
      $endOfMonth = strtotime(date("Y-m-t"));
      $statement = $pdo->prepare('UPDATE bestellungen SET bis = UNIX_TIMESTAMP() WHERE user_id = :userId AND aktiv = 0 AND bis = 0 AND kennung = :kennung');
      $statement->bindParam(':userId', $userId);
      $statement->bindParam(':kennung', $_POST['roomid']);
      $statement->execute();
    }
  }
}

$statement = $pdo->prepare("SELECT right_id FROM user_personal WHERE user_id = :userId");
$statement->bindParam(':userId', $userId);
$result = $statement->execute();
$row = $statement->fetch();
if ($statement->rowCount() === 1)
{
  $rightId = $row['right_id'];
}
include("templates/header.inc.php");
?>

<div class="container main-container">

  <h1>Herzlich Willkommen!</h1>

  Hallo <?php echo htmlentities($user['vorname']); ?>,<br>
  Hier ist eine Übersicht ihrer gemieteten Räume!<br><br>


  <div class="panel panel-default">

    <table class="table">
      <tr>
        <th>Kennung</th>
        <th>Kosten</th>
        <th>Zugangscode</th>
        <th>Status</th>
        <th>Läuft bis</th>
      </tr>
      <?php
      $statement = $pdo->prepare("SELECT bestellungen.kennung, preis, code, aktiv, bis FROM bestellungen INNER JOIN raeume ON raeume.kennung = bestellungen.kennung INNER JOIN preise ON preise.groesse = raeume.groesse WHERE user_id = :userId AND (aktiv = 1 OR bis = 0)");
      $statement->bindParam(':userId', $userId);
      $result = $statement->execute();
      $count = 1;
      while ($row = $statement->fetch())
      { ?>
        <tr>
          <td><?php echo $row['kennung']; ?></td>
          <td><?php echo $row['preis']; ?>€ p.M.</td>
          <td><?php echo $row['code']; ?></td>
          <td><?php echo($row['aktiv'] == 1 ? 'Bestätigt' : 'Außenstehend') ?></td>
          <td><?php echo($row['bis'] == 0 ? 'Unbegrenzt' : date("Y-m-d", $row['bis'])) ?></td>
          <td>
            <form method="post">
              <input name="roomcode" id="roomcode" value="<?php echo $row['code']; ?>"><input hidden name="roomid" value="<?php echo $row['kennung']; ?>">
              <button type="submit" name="action" value="change" class="edit_btn">Zugangscode setzen</button>
              <button type="submit" name="action" value="delete" class="del_btn">Raum kündigen</button>
            </form>
          </td>
        </tr>
      <?php } ?>


    </table>
  </div>


</div>
<?php
include("templates/footer.inc.php")
?>
