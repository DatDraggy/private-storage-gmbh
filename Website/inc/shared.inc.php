<?php
/*
 * Dateiname: internal_save.php
 * Autor: Marlin, Dennis, Jason
 *
 * Version: 1.1
 * letzte Änderung: 11. Februar 2019
 *
 * Inhalt: DB Funktionen
 *
 * Verwendete Funktionen:
 *
 * Definierte Funktionen:
 *   build_database_connection
 *   notify_on_exception
 *
 * globale Variablen:
 */

/*
 * Funktion: build_database_connection
 * Beschreibung: Verbindet mit Datenbank und erzeugt PDO Objekt
 *
 * Verwendete Funktionen:
 *   notify_on_exception
 *
 * Parameter:
 *   config
 *
 * Rückgabewert:
 *   PDO Objekt
 */
function build_database_connection($config)
{
  //Connect to DB only here to save response time on other commands
  try
  {
    $dbConnection = new PDO('mysql:dbname=' . $config['db_name'] . ';host=' . $config['db_host'] . ';port=' . $config['db_port'] . ';charset=utf8mb4', $config['db_user'], $config['db_password'], array(PDO::ATTR_TIMEOUT => 25));
    $dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
  catch (PDOException $e)
  {
    notify_on_exception('Database Connection', $config, '', $e);
  }
  return $dbConnection;
}

/*
 * Funktion: notify_on_exception
 * Beschreibung: Benachrichtigt an email bei ausführung
 *
 * Verwendete Funktionen:
 *
 * Parameter:
 *   Betreff
 *   Config
 *   SQL Query
 *   Exception
 *
 * Rückgabewert:
 */
function notify_on_exception($subject, $config, $sql = '', $e = '')
{
  $to = $config['db_mail'];
  $txt = __FILE__ . ' ' . $sql . ' Error: ' . $e;
  $headers = 'From: ' . $config['mail'];
  mail($to, $subject, $txt, $headers);
  die();
}