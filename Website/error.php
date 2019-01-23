<?php
require_once(__DIR__ . '/inc/config.inc.php');
require_once(__DIR__ . '/inc/errors.inc.php');
if (isset($_GET['error'])) {
  $error = $_GET['error'];
} else {
  die();
}
if (isset($errors[$error])) {
  echo $errors[$error];
}
if (isset($_GET['redirect'])) {
  switch ($_GET['redirect']) {
    case 'bank':
      echo '<meta http-equiv="refresh" content="3; URL=settings.php#bank">';
      break;
  }
}