<?php
require_once(__DIR__ . '/inc/config.inc.php');
$dbConnection = buildDatabaseConnection($config);

if (isset($_POST["Import"])) {
  if ($_FILES["file"]["size"] > 0) {
    $row = 1;
    if (empty($_FILES["file"]["tmp_name"])) {
      $_FILES["file"]["tmp_name"] = 'test.csv';
    }
    //Datei öffnen, wenn success nimm datei und loop solange datei ist nicht ende
    //Dann SQL insert
    if (($handle = fopen($_FILES["file"]["tmp_name"], "r")) !== FALSE) {
      while (($data = fgetcsv($handle, 100, ",")) !== FALSE) {
        $num = count($data);
        echo "<p> $num Felder in Zeile $row: <br /></p>\n";
        $row++;
        $date = $data[0];
        $amount = $data[1];
        $userId = $data[2];

        try {
          $stmt = $dbConnection->prepare('INSERT INTO zahlungen(user_id, menge, datum) VALUES(:userId, :amount, :date)');
          $stmt->bindParam(':userId', $userId);
          $stmt->bindParam(':amount', $amount);
          $stmt->bindParam(':date', $date);
          $stmt->execute();
        } catch (PDOException $e) {
          echo 'Error ' . $e . ' auf Zeile ' . $row;
          continue;
        }
      }
      fclose($handle);
    }
  }
}