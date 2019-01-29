<?php
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");
require_once("inc/permissions.php");

//Überprüfe, dass der User eingeloggt ist
//Der Aufruf von check_user() muss in alle internen Seiten eingebaut sein
$userId = $_SESSION['userid'];
$targetUserId = $_GET['userid'];
$user = check_user($userId);
$statement = $pdo->prepare("SELECT right_id FROM user_personal WHERE user_id = :userId");
$statement->bindParam(':userId', $userId);
$result = $statement->execute();
$row = $statement->fetch();
if ($statement->rowCount() === 1) {
  $rightId = $row['right_id'];
} else {
  die();//Keine RightID gefunden, nicht autorisiert
}

if (in_array($rightId, $config['administration']['userDelete'])) {
  //User delete, Belegte Räume befreien
  $statement = $pdo->prepare("DELETE FROM users WHERE id = :userId");
  $statement->bindParam(':userId', $targetUserId);
  $statement->execute();
  echo 'deleted';
}

include("templates/header.inc.php");
