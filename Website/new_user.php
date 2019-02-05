<?php
session_start();
require_once("inc/config.inc.php");
require_once("inc/shared.inc.php");
require_once("inc/functions.inc.php");
$pdo = buildDatabaseConnection($config);

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


  <div class="container main-container registration-form">
    <h1>Kunden hinzufügen</h1>
    <?php
    $showFormular = true; //Variable ob das Registrierungsformular anezeigt werden soll

    if (isset($_GET['register'])) {
      $error = false;
      $vorname = trim($_POST['vorname']);
      $nachname = trim($_POST['nachname']);
      $email = trim($_POST['email']);
      $passwort = $_POST['passwort'];
      $passwort2 = $_POST['passwort2'];

      if (empty($vorname) || empty($nachname) || empty($email)) {
        echo 'Bitte alle Felder ausfüllen<br>';
        $error = true;
      }

      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo 'Bitte eine gültige E-Mail-Adresse eingeben<br>';
        $error = true;
      }
      if (strlen($passwort) == 0) {
        echo 'Bitte ein Passwort angeben<br>';
        $error = true;
      }
      if ($passwort != $passwort2) {
        echo 'Die Passwörter müssen übereinstimmen<br>';
        $error = true;
      }

      //Überprüfe, dass die E-Mail-Adresse noch nicht registriert wurde
      if (!$error) {
        $statement = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $result = $statement->execute(array('email' => $email));
        $user = $statement->fetch();

        if ($user !== false) {
          echo 'Diese E-Mail-Adresse ist bereits vergeben<br>';
          $error = true;
        }
      }

      //Keine Fehler, wir können den Nutzer registrieren
      if (!$error) {
        $passwort_hash = password_hash($passwort, PASSWORD_DEFAULT);

        $statement = $pdo->prepare("INSERT INTO users (email, passwort, vorname, nachname) VALUES (:email, :passwort, :vorname, :nachname)");
        $result = $statement->execute(array(
          'email' => $email,
          'passwort' => $passwort_hash,
          'vorname' => $vorname,
          'nachname' => $nachname
        ));

        if ($result) {
          echo 'Der User wurde erfolgreich angelegt. <a href="internal.php">zurück zur Übersicht</a>';
          $showFormular = false;
        } else {
          echo 'Beim Abspeichern ist leider ein Fehler aufgetreten<br>';
        }
      }
    }

    if ($showFormular) {
      ?>

      <form action="?register=1" method="post">

        <div class="form-group">
          <label for="inputVorname">Vorname:</label>
          <input type="text" id="inputVorname" size="40" maxlength="250" name="vorname" class="form-control" required>
        </div>

        <div class="form-group">
          <label for="inputNachname">Nachname:</label>
          <input type="text" id="inputNachname" size="40" maxlength="250" name="nachname" class="form-control" required>
        </div>

        <div class="form-group">
          <label for="inputEmail">E-Mail:</label>
          <input type="email" id="inputEmail" size="40" maxlength="250" name="email" class="form-control" required>
        </div>

        <div class="form-group">
          <label for="inputPasswort">Dein Passwort:</label>
          <input type="password" id="inputPasswort" size="40" maxlength="250" name="passwort" class="form-control" required>
        </div>

        <div class="form-group">
          <label for="inputPasswort2">Passwort wiederholen:</label>
          <input type="password" id="inputPasswort2" size="40" maxlength="250" name="passwort2" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-lg btn-primary btn-block">Kunde anlegen</button>
      </form>

      <?php
    } //Ende von if($showFormular)


    ?>
  </div>
<?php
include("templates/footer.inc2.php")
?>