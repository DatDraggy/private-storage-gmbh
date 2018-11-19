<?php
if(!is_checked_in()) {
  require_once(__DIR__ . '/inc/functions.inc.php');
  $groesse = $_GET['groesse'];
  $dbConnection = buildDatabaseConnection($config);
  $userId = $_SESSION['userid'];
  try {
    $sql = "SELECT bestaetigt FROM user_bankdaten WHERE id = $userId AND bestaetigt = 1";
    $stmt = $dbConnection->prepare("SELECT bestaetigt FROM user_bankdaten WHERE id = :userId AND bestaetigt = 1");
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();
    $row = $stmt->fetch();
  } catch (PDOException $e) {
    notifyOnException('Database Select', $config, $sql, $e);
  }

  if ($stmt->rowCount() === 0) {
    //Nicht bestätigt
    header('error.php?error=nichtBest');
  }


  try {
    $sql = "SELECT kennung FROM raeume WHERE kennung NOT IN (SELECT kennung FROM bestellungen WHERE aktiv = 1) GROUP BY groesse ORDER BY nummer ASC";
    $stmt = $dbConnection->prepare("SELECT kennung FROM raeume WHERE groesse = :groesse AND kennung NOT IN (SELECT kennung FROM bestellungen WHERE aktiv = 1) ORDER BY nummer ASC LIMIT 1");
    $stmt->bindParam(':groesse', $groesse);
    $stmt->execute();
    $row = $stmt->fetch();
  } catch (PDOException $e) {
    notifyOnException('Database Select', $config, $sql, $e);
  }
  //ToDo: Wenn 5_1 in Bestellungen und nicht ausgelaufen > Ergebnis 5_2, 5_3, 5_4
  if ($stmt->rowCount() === 0) {
    header('error.php?error=keineRaeume');
  } else {
    $kennung = $row['kennung'];

    try {
      $sql = "INSERT INTO bestellungen(user_id, kennung, datum, aktiv) VALUES($userId, $kennung, UNIX_TIMESTAMP(), 1)";
      $stmt = $dbConnection->prepare("INSERT INTO bestellungen(user_id, kennung, datum, aktiv) VALUES(:userId, :kennung, UNIX_TIMESTAMP(), 1)");
      $stmt->bindParam(':userId', $userId);
      $stmt->bindParam(':kennung', $kennung);
      $stmt->execute();
    } catch (PDOException $e) {
      notifyOnException('Database Select', $config, $sql, $e);
    }
    //Bestellung Erfolgreich
  }
}
else{
  header('login.php');
}