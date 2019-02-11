<?php
/*
 * Dateiname: payment_import.php
 * Autor: Marlin
 *
 * Version:
 * letzte Änderung: 11. Februar 2019
 *
 * Inhalt: Erlaubt das Importieren von Rechnungen aus einer Datei
 *
 * Verwendete Funktionen:
 *   build_database_connection
 *   check_user
 *
 * Definierte Funktionen:
 *
 * globale Variablen:
 */
require_once(__DIR__ . '/inc/config.inc.php');
require_once(__DIR__ . '/inc/functions.inc.php');
$dbConnection = build_database_connection($config);
$rightId = check_user()['right_id'];
if (!in_array($rightId, $config['administration']['canImport']))
{
  die();
}
if (isset($_POST["Import"]))
{
  if ($_FILES["file"]["size"] > 0)
  {
    $row = 1;
    //Datei öffnen, wenn success nimm datei und loop solange datei ist nicht ende
    //Dann SQL insert
    if (($handle = fopen($_FILES["file"]["tmp_name"], "r")) !== FALSE)
    {
      while (($data = fgetcsv($handle, 100, ",")) !== FALSE)
      {
        if ($row === 1)
        {
          $row++;
          continue;
        }
        $num = count($data);
        echo "<p> $num Felder in Zeile $row <br /></p>\n";
        $row++;
        $date = $data[0];
        $amount = $data[1];
        $userId = $data[2];
        echo $date . ' ' . $amount . ' ' . $userId;

        try
        {
          $stmt = $dbConnection->prepare('INSERT INTO abrechnung(user_id, preis, kennung, time) VALUES(:userId, :amount, :kennung, :date)');
          $stmt->bindParam(':userId', $userId);
          $stmt->bindParam(':preis', $preis);
          $stmt->bindParam(':kennung', $kennung);
          $stmt->bindParam(':time', $time);
          $stmt->execute();
        }
        catch (PDOException $e)
        {
          echo 'Error ' . $e . ' auf Zeile ' . $row;
          continue;
        }
      }
      fclose($handle);
    }
  }
}