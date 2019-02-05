<?php
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");
require_once("inc/permissions.php");

//Überprüfe, dass der User eingeloggt ist
//Der Aufruf von check_user() muss in alle internen Seiten eingebaut sein
$user = check_user();
$userId = $_SESSION['userid'];
$statement = $pdo->prepare("SELECT right_id FROM user_personal WHERE user_id = :userId");
$statement->bindParam(':userId', $userId);
$result = $statement->execute();
$row = $statement->fetch();
if ($statement->rowCount() === 1) {
  $rightId = $row['right_id'];
}
?>

<?php

//$csvdata="abrechnung.csv";
header("content-type: application/csv-tab-delimited-table");
header("content-length: " . strlen($csvdata));
header("content-disposition: attachment; filename=\"abrechnung.csv\"");
echo $csvdata;


$result = mysql_query("SELECT * FROM zahlungen", $connection);
echo mysql_error();
$csvdata = "TESTEINTRAG";
while ($row = mysql_fetch_array($result)) {
  $csvdata = $csvdata . $row["user_id"] . ";";
  $csvdata = $csvdata . $row["menge"] . ";";
  $csvdata = $csvdata . $row["datum"] . ";";
}
echo $csvdata;
?> 
