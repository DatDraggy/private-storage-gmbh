<?php
/*
 * Dateiname: internal_save.php
 * Autor: Marlin, Dennis, Jason
 *
 * Version: 1.1
 * letzte Änderung: 11. Februar 2019
 *
 * Inhalt: Zeigt alle User für Mitarbeiter an
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

//Überprüfe, dass der User eingeloggt ist
//Der Aufruf von check_user() muss in alle internen Seiten eingebaut sein
$user = check_user();
$userId = $_SESSION['userid'];
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
  Herzlich Willkommen im internen Bereich!<br><br>
  <?php
  if ($rightId === 1)
  {
    ?>
    <div class="panel panel-default">

      <table class="table">
        <tr>
          <th>#</th>
          <th>Vorname</th>
          <th>Nachname</th>
          <th>E-Mail</th>
        </tr>
        <?php
        $statement = $pdo->prepare("SELECT * FROM users ORDER BY id");
        $result = $statement->execute();
        $count = 1;
        while ($row = $statement->fetch())
        {
          echo "<tr>";
          echo "<td>" . $count++ . "</td>";
          echo "<td>" . $row['vorname'] . "</td>";
          echo "<td>" . $row['nachname'] . "</td>";
          echo '<td><a href="mailto:' . $row['email'] . '">' . $row['email'] . '</a></td>';
          echo "</tr>";
        }
        ?>
      </table>
    </div>
    <?php
  }
  ?>


</div>
<?php
include("templates/footer.inc.php")
?>
