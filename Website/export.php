<?php
//include database configuration file
require_once(__DIR__ . '/inc/config.inc.php');
$dbConnection = buildDatabaseConnection($config);
//get records from database
$stmt = $dbConnection->prepare("SELECT id, user_id, menge, datum FROM zahlungen ORDER BY id DESC");
$stmt->execute();
$rows = $stmt->fetchAll();

if($stmt->rowCount() > 0){
  $delimiter = ",";
  $filename = "zahlungen_" . date('Y-m-d') . ".csv";

  //create a file pointer
  $f = fopen('php://memory', 'w');

  //set column headers
  $fields = array('ID', 'UserID', 'Menge', 'Datum');
  fputcsv($f, $fields, $delimiter);

  //output each row of the data, format line as csv and write to file pointer
  foreach ($rows as $row) {
    $lineData = array($row['id'], $row['user_id'], $row['menge'], $row['datum']);
    fputcsv($f, $lineData, $delimiter);
  }

  //move back to beginning of file
  fseek($f, 0);

  //set headers to download file rather than displayed
  header('Content-Type: text/csv');
  header('Content-Disposition: attachment; filename="' . $filename . '";');

  //output all remaining data on a file pointer
  fpassthru($f);
}