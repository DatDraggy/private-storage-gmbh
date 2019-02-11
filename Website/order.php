<?php
/*
 * Dateiname: order.php
 * Autor: Marlin, Dennis
 *
 * Version: 1.6
 * letzte Änderung: 11. Februar 2019
 *
 * Inhalt: Funktionalität zum Zimmer bestellen
 *
 * Verwendete Funktionen:
 *   is_checked_in
 *   build_database_connection
 *   notify_on_exception
 *
 * Definierte Funktionen:
 *
 * globale Variablen:
 *   _SESSION
 */
session_start();
require_once(__DIR__ . '/inc/functions.inc.php');
if (is_checked_in())
{
  $groesse = $_GET['groesse'];
  $dbConnection = build_database_connection($config);
  $userId = $_SESSION['userid'];
  try
  {
    $sql = "SELECT bestaetigt FROM user_bankdaten WHERE id = $userId AND bestaetigt = 1";
    $stmt = $dbConnection->prepare("SELECT bestaetigt FROM user_bankdaten WHERE id = :userId AND bestaetigt = 1");
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();
    $row = $stmt->fetch();
  }
  catch (PDOException $e)
  {
    notifyOnException('Database Select', $config, $sql, $e);
  }

  if ($stmt->rowCount() === 0)
  {
    //Nicht bestätigt
    header('Location: error.php?error=nichtBestaetigt&redirect=bank');
  }
  else
  {
    try
    {
      $sql = "SELECT kennung FROM raeume WHERE kennung NOT IN (SELECT kennung FROM bestellungen WHERE (aktiv = 1 OR bis = 0)) GROUP BY groesse ORDER BY nummer ASC";
      $stmt = $dbConnection->prepare("SELECT kennung FROM raeume WHERE groesse = :groesse AND kennung NOT IN (SELECT kennung FROM bestellungen WHERE (aktiv = 1 OR bis = 0)) ORDER BY nummer ASC LIMIT 1");
      $stmt->bindParam(':groesse', $groesse);
      $stmt->execute();
      $row = $stmt->fetch();
    }
    catch (PDOException $e)
    {
      notifyOnException('Database Select', $config, $sql, $e);
    }
    //ToDo: Wenn 5_1 in Bestellungen und nicht ausgelaufen > Ergebnis 5_2, 5_3, 5_4
    if ($stmt->rowCount() === 0)
    {
      header('Location: error.php?error=keineRaeume');
    }
    else
    {
      $kennung = $row['kennung'];

      try
      {
        $sql = "INSERT INTO bestellungen(user_id, kennung, datum, aktiv) VALUES($userId, $kennung, UNIX_TIMESTAMP(), 0)";
        $stmt = $dbConnection->prepare("INSERT INTO bestellungen(user_id, kennung, datum, aktiv) VALUES(:userId, :kennung, UNIX_TIMESTAMP(), 0)");
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':kennung', $kennung);
        $stmt->execute();
      }
      catch (PDOException $e)
      {
        notifyOnException('Database Select', $config, $sql, $e);
      }
      $code = substr(str_shuffle('123456789'), 0, 4);
      try
      {
        $sql = "UPDATE raeume SET code = '$code' WHERE kennung = '$kennung'";
        $stmt = $dbConnection->prepare('UPDATE raeume SET code = :code WHERE kennung = :kennung');
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':kennung', $kennung);
        $stmt->execute();
      }
      catch (PDOException $e)
      {
        notifyOnException('Database Select', $config, $sql, $e);
      }
      //Bestellung Erfolgreich
      echo 'Ihrem Kundenkonto wurde der Raum zugewiesen';
      header('Location: rooms.php');
    }
  }
}
else
{
  header('Location: login.php');
}