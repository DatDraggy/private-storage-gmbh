<?php
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");
require_once("inc/permissions.php");
$dbConnection = buildDatabaseConnection($config);
//Überprüfe, dass der User eingeloggt ist
//Der Aufruf von check_user() muss in alle internen Seiten eingebaut sein
$user = check_user();
$rightId = $user['right_id'];

try {
  $stmt = $dbConnection->prepare('SELECT * FROM zahlungen');
  $stmt->execute();
  $rows = $stmt->fetchAll();
} catch (PDOException $e) {
  echo $e;
}
$csvdata = 'user_id,menge,datum' . "\n";
foreach ($rows as $row) {
  $csvdata .= $row['user_id'] . ',';
  $csvdata .= $row['menge'] . ',';
  $csvdata .= $row['datum'] . "\n";
}

header("content-type: application/csv-tab-delimited-table");
header("content-length: " . strlen($csvdata));
header("content-disposition: attachment; filename=\"abrechnung.csv\"");
echo $csvdata;