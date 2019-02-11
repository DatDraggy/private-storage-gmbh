<?php
/*
 * Dateiname: internal_save.php
 * Autor: Marlin, Dennis, Jason
 *
 * Version: 1.1
 * letzte Änderung: 11. Februar 2019
 *
 * Inhalt: Funktionssammlung
 *
 * Verwendete Funktionen:
 *
 * Definierte Funktionen:
 *   check_user
 *   allowed_to_view_user
 *   error
 *   get_site_url
 *   random_string
 *   is_checked_in
 *
 * globale Variablen:
 */
require_once(__DIR__ . '/config.inc.php');
require_once(__DIR__ . '/shared.inc.php');

include_once("password.inc.php");
$pdo = build_database_connection($config);

/*
 * Funktion: check_user
 * Beschreibung: Prüft ob User Valide und existent und gibt Datenbankdaten zurück
 *
 * Verwendete Funktionen:
 *
 * Parameter:
 *   userId
 *
 * Rückgabewert:
 *    array (Datenbankdaten)
 */
function check_user($userId = '')
{
  global $pdo;
  if (empty($userId))
  {
    $userId = $_SESSION['userid'];
  }

  if (!isset($_SESSION['userid']) && isset($_COOKIE['identifier']) && isset($_COOKIE['securitytoken']))
  {
    $identifier = $_COOKIE['identifier'];
    $securitytoken = $_COOKIE['securitytoken'];

    $statement = $pdo->prepare("SELECT * FROM securitytokens WHERE identifier = ?");
    $result = $statement->execute(array($identifier));
    $securitytoken_row = $statement->fetch();

    if (sha1($securitytoken) !== $securitytoken_row['securitytoken'])
    {
      //Vermutlich wurde der Security Token gestohlen
      //Hier ggf. eine Warnung o.ä. anzeigen

    }
    else
    { //Token war korrekt
      //Setze neuen Token
      $neuer_securitytoken = random_string();
      $insert = $pdo->prepare("UPDATE securitytokens SET securitytoken = :securitytoken WHERE identifier = :identifier");
      $insert->execute(array(
        'securitytoken' => sha1($neuer_securitytoken),
        'identifier' => $identifier
      ));
      setcookie("identifier", $identifier, time() + (3600 * 24 * 365)); //1 Jahr Gültigkeit
      setcookie("securitytoken", $neuer_securitytoken, time() + (3600 * 24 * 365)); //1 Jahr Gültigkeit

      //Logge den Benutzer ein
      $_SESSION['userid'] = $securitytoken_row['user_id'];
    }
  }


  if (!isset($_SESSION['userid']))
  {
    die('Bitte zuerst <a href="login.php">einloggen</a>');
  }


  $statement = $pdo->prepare("SELECT users.id, adressen.firma, users.vorname, users.nachname, adressen.strasse, adressen.hausnr, adressen.plz, adressen.ort, user_bankdaten.iban, user_bankdaten.bic, user_bankdaten.bestaetigt, users.email, user_personal.right_id
    FROM users INNER JOIN user_bankdaten ON users.id = user_bankdaten.id INNER JOIN adressen ON users.id = adressen.user_id LEFT OUTER JOIN user_personal ON user_personal.user_id = users.id WHERE users.id = :id");
  $result = $statement->execute(array(':id' => $userId));
  $user = $statement->fetch();
  return $user;
}

/*
 * Funktion: allowed_to_view_user
 * Beschreibung: Prüft ob User erlaubnis zum editieren von user ist
 *
 * Verwendete Funktionen:
 *
 * Parameter:
 *   userId
 *
 * Rückgabewert:
 *   boolean (True, wenn erlaubt)
 */
function allowed_to_view_user($userId)
{
  global $config;
  $dbConnection = build_database_connection($config);

  $stmt = $dbConnection->prepare("SELECT right_id FROM user_personal WHERE user_id = :userId");
  $stmt->bindParam(':userId', $userId);
  $stmt->execute();
  $row = $stmt->fetch();
  if (in_array($row['right_id'], $config['administration']['userEdit']))
  {
    return true;
  }
  else
  {
    return false;
  }
}

/*
 * Funktion: is_checked_in
 * Beschreibung: Gibt boolean zurück ob user eingeloggt
 *
 * Verwendete Funktionen:
 *
 * Parameter:
 *
 * Rückgabewert:
 *  boolean (Eingeloggt oder nicht)
 */
function is_checked_in()
{
  return isset($_SESSION['userid']);
}

/*
 * Funktion: random_string
 * Beschreibung: Gibt zufällig generierten string zurück
 *
 * Verwendete Funktionen:
 *
 * Parameter:
 *
 * Rückgabewert:
 *  string (Zufällige Zeichen)
 */
function random_string()
{
  if (function_exists('openssl_random_pseudo_bytes'))
  {
    $bytes = openssl_random_pseudo_bytes(16);
    $str = bin2hex($bytes);
  }
  else if (function_exists('mcrypt_create_iv'))
  {
    $bytes = mcrypt_create_iv(16, MCRYPT_DEV_URANDOM);
    $str = bin2hex($bytes);
  }
  else
  {
    //Replace your_secret_string with a string of your choice (>12 characters)
    $str = md5(uniqid('your_secret_string', true));
  }
  return $str;
}

/*
 * Funktion: get_site_url
 * Beschreibung: Derzeitige URL Zurückgeben
 *
 * Verwendete Funktionen:
 *
 * Parameter:
 *
 * Rückgabewert:
 *  string (Gibt URL zurückt)
 */
function get_site_url()
{
  $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
  return $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/';
}

/*
 * Funktion: error
 * Beschreibung: Zeigt user error nachricht
 *
 * Verwendete Funktionen:
 *
 * Parameter:
 *   String (Error Nachricht)
 *
 * Rückgabewert:
 */
function error($error_msg)
{
  include("templates/header.inc.php");
  include("templates/error.inc.php");
  include("templates/footer.inc.php");
  exit();
}
