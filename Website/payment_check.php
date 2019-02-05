<?php
//If CLI
$dbConnection = buildDatabaseConnection($config);

$row = 1;
if (($handle = fopen("test.csv", "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 100, ",")) !== FALSE) {
    $num = count($data);
    echo "<p> $num Felder in Zeile $row: <br /></p>\n";
    $row++;
    $userId = $data[0];
    $amount = $data[1];
    $datum = $data[2];
  }
  fclose($handle);
}

try {
  $sql = '';
  $stmt = $dbConnection->prepare($sql);
  $stmt->execute();
  $rows = $stmt->fetchAll();
} catch (PDOException $e) {

}

foreach ($rows as $row) {
  $userId = $row['user_id'];

}
?>