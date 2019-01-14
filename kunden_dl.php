<?php
session_start();
require_once(__DIR__ . '/inc/functions.inc.php');
$userId=is_checked_in();
if (!empty($userId)) {
  $dbConnection = buildDatabaseConnection($config);


  header('Content-Type: text/csv');
  header('Content-Disposition: attachment; filename="nutzer' . $userId . '.csv"');
  $data = array(
    'id,vorname,nachname,iban,bic,summe'
  );

  //SELECT users.id,vorname,nachname,iban,bic,sum((SELECT preis FROM bestellungen INNER JOIN raeume ON raeume.kennung = bestellungen.kennung INNER JOIN preise ON raeume.groesse = preise.groesse WHERE bestellungen.user_id = users.id)) as summe FROM users INNER JOIN user_bankdaten ON users.id = user_bankdaten.id
  $sql = 'SELECT users.id,vorname,nachname,iban,bic,sum((SELECT preis FROM bestellungen INNER JOIN raeume ON raeume.kennung = bestellungen.kennung INNER JOIN preise ON raeume.groesse = preise.groesse WHERE bestellungen.user_id = users.id)) as summe FROM users INNER JOIN user_bankdaten ON users.id = user_bankdaten.id';

  try{
    $stmt = $dbConnection->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll();
  }catch (PDOException $e){

  }
  foreach ($rows as $row){
    $data[] = $row['id'].','.$row['vorname'].','.$row['nachname'].','.$row['iban'].','.$row['bic'].','.$row['summe'];
  }

  $fp = fopen('php://output', 'wb');

  foreach ( $data as $line ) {
    $val = explode(",", $line);
    fputcsv($fp, $val);
  }
  fclose($fp);
}