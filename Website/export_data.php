<?php
/*
 * Dateiname: export_data.php
 * Autor: Dennis, Marlin
 *
 * Version: 1.1
 * letzte Änderung: 11. Februar 2019
 *
 * Inhalt: Exportiert Zahlungen ins CSV Format
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
require_once("inc/permissions.php");
$dbConnection = build_database_connection($config);
//Überprüfe, dass der User eingeloggt ist
//Der Aufruf von check_user() muss in alle internen Seiten eingebaut sein
$user = check_user();
$rightId = $user['right_id'];
if(!in_array($rightId, $config['administration']['userBilling'])){die();}
try
{
  $stmt = $dbConnection->prepare('SELECT * FROM abrechnung WHERE bezahlt = 0');
  $stmt->execute();
  $rows = $stmt->fetchAll();
}
catch (PDOException $e)
{
  echo $e;
}
$csvdata = 'datum,menge,user_id,kennung' . "\n";
foreach ($rows as $row)
{
  $csvdata .= date('Y-m-d', $row['time']) . ',';
  $csvdata .= $row['preis'] . ',';
  $csvdata .= $row['user_id'] . ',';
  $csvdata .= $row['kennung'] . "\n";
}

header("content-type: application/csv-tab-delimited-table");
header("content-length: " . strlen($csvdata));
header("content-disposition: attachment; filename=\"abrechnung.csv\"");
echo $csvdata;