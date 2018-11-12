<?php
require_once('config.php');
require_once('inc/shared.inc.php');
session_start();
$pdo = buildDatabaseConnection($config);

if (!empty($row)) {
  $ownScore = $row['score'];
}
if(isset($_GET['login'])) {
  $email = $_POST['email'];
  $passwort = $_POST['passwort'];

  try {
    $sql = "SELECT id, passwort FROM users WHERE email = '$email'";
    $stmt = $pdo->prepare("SELECT id,passwort FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch();
  } catch (PDOException $e) {
    notifyOnException('Database Select', $config, $sql, $e);
  }
  if (!empty($row)) {
    //Überprüfung des Passworts
    if (password_verify($passwort, $user['passwort'])) {
      $_SESSION['userid'] = $user['id'];
      die('Login erfolgreich. Weiter zu <a href="geheim.php">internen Bereich</a>');
    } else {
      $errorMessage = "E-Mail oder Passwort war ungültig<br>";
    }
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title> Login StorageGmbH</title>
  <link rel="stylesheet" a href="css\style1.css">

</head>
<body>
<div class="container">
  <img src="images/avatar.png"/>
  <form>
    <div class="form-input">
      <input type="text" name="text" placeholder="eMail als Benutzername"/>
    </div>
    <div class="form-input">
      <input type="password" name="password" placeholder="Passwort"/>
    </div>
    <input type="submit" type="submit" value="LOGIN" class="button"/>
    <form>
      <input type="button" class="button" value="Registrieren" onclick="window.location.href='registrieren.php'" />
    </form>
    <form>
      <input type="button" class="button" value="Passwort vergessen" onclick="window.location.href='passwortvergessen.php'" />
    </form>

  </form>
</div>

<?php
$showFormular = false; //Variable ob das Registrierungsformular anezeigt werden soll

if(isset($_GET['register'])) {
  $error = false;
  $email = $_POST['email'];
  $passwort = $_POST['passwort'];
  $passwort2 = $_POST['passwort2'];

  if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo 'Bitte eine gültige E-Mail-Adresse eingeben<br>';
    $error = true;
  }
  if(strlen($passwort) == 0) {
    echo 'Bitte ein Passwort angeben<br>';
    $error = true;
  }
  if($passwort != $passwort2) {
    echo 'Die Passwörter müssen übereinstimmen<br>';
    $error = true;
  }

  //Überprüfe, dass die E-Mail-Adresse noch nicht registriert wurde
  if(!$error) {
    try {
      $sql = "SELECT id FROM users WHERE email = '$email'";
      $stmt = $dbConnection->prepare("SELECT id FROM users WHERE email = :email");
      $stmt->bindParam(':email', $email);
      $stmt->execute();
      $row = $stmt->fetch();
    } catch (PDOException $e) {
      notifyOnException('Database Select', $config, $sql, $e);
    }
    if (!empty($row)) {
      echo 'Diese E-Mail-Adresse ist bereits vergeben<br>';
      $error = true;
    }
  }

  //Keine Fehler, wir können den Nutzer registrieren
  if(!$error) {
    $passwort_hash = password_hash($passwort, PASSWORD_DEFAULT);

    try {
      $sql = "INSERT INTO users (email, passwort) VALUES ($email, $passwort_hash)";
      $stmt = $dbConnection->prepare("INSERT INTO users (email, passwort) VALUES (:email, :passwortHash)");
      $stmt->bindParam(':email', $email);
      $stmt->bindParam(':passwortHash', $passwort_hash);
      $stmt->execute();
      $result = true;
    } catch (PDOException $e) {
      notifyOnException('Database Insert', $config, $sql, $e);
      $result = false;
    }
    if($result) {
      echo 'Du wurdest erfolgreich registriert. <a href="login.php">Zum Login</a>';
      $showFormular = false;
    } else {
      echo 'Beim Abspeichern ist leider ein Fehler aufgetreten<br>';
    }
  }
}

if($showFormular) {
  ?>

  <form action="?register=1" method="post">
    E-Mail:<br>
    <input type="email" size="40" maxlength="250" name="email"><br><br>

    Dein Passwort:<br>
    <input type="password" size="40"  maxlength="250" name="passwort"><br>

    Passwort wiederholen:<br>
    <input type="password" size="40" maxlength="250" name="passwort2"><br><br>

    <input type="submit" value="Abschicken">
  </form>

  <?php
} //Ende von if($showFormular)
?>
</body>
</html>
