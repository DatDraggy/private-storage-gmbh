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
  Herzlich Willkommen im internen Bereich!<br><br>
  <?php
  if(in_array($user['right_id'], $config['administration']['userOverview'])) {
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
        while ($row = $statement->fetch()) { ?>
          <tr>
            <td><?php echo $count++; ?></td>
            <td><?php echo $row['vorname']; ?></td>
            <td><?php echo $row['nachname']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><a href="settings.php?userid=<?php echo $row['id']; ?>" class="edit_btn">Edit</a></td>
            <td><a href="user_delete.php?userid=<?php echo $row['id']; ?>" class="del_btn">Delete</a></td>
          </tr>
        <?php } ?>


      </table>

    </div>
<a href="new_user.php" class="btn btn-lg btn-primary btn-block">Kunden neu anlegen</a>		
    <?php
  }
  ?>

</div>
            
<?php
include("templates/footer.inc.php")
?>
