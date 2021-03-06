<?php
/*
 * Dateiname: export_data.php
 * Autor: Dennis
 *
 * Version: 1
 * letzte Änderung: 11. Februar 2019
 *
 * Inhalt: Header für jede Datei
 *
 * Verwendete Funktionen:
 *
 * Definierte Funktionen:
 *
 * globale Variablen:
 */
require_once("inc/permissions.php");
?>
<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Login Storage GmbH</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">

    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet">

  </head>
  <body>

    <nav class="navbar navbar-inverse navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Menu</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php"><i class="glyphicon glyphicon-leaf logo"></i>Storage GmbH</a>
        </div>
        <?php if (!is_checked_in()): ?>
          <div id="navbar" class="navbar-collapse collapse">
            <form class="navbar-form navbar-right" action="login.php" method="post">
              <table class="login" role="presentation">
                <tbody>
                <tr>
                  <td>
                    <div class="input-group">
                      <div class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></div>
                      <input class="form-control" placeholder="E-Mail" name="email" type="email" required>
                    </div>
                  </td>
                  <td>
                    <input class="form-control" placeholder="Passwort" name="passwort" type="password" value="" required>
                  </td>
                  <td>
                    <button type="submit" class="btn btn-success">Login</button>
                  </td>
                </tr>
                <tr>
                  <td>
                    <label style="margin-bottom: 0px; font-weight: normal;"><input type="checkbox" name="angemeldet_bleiben" value="remember-me" title="Angemeldet bleiben" checked="checked" style="margin: 0; vertical-align: middle;"/>
                      <small>Angemeldet bleiben</small>
                    </label></td>
                  <td>
                    <small><a href="passwortvergessen.php">Passwort vergessen</a></small>
                  </td>
                  <td></td>
                </tr>
                </tbody>
              </table>


            </form>
          </div>
        <?php else: ?>
          <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
              <li><a href="rooms.php">Ihre gemieteten Räume</a></li>
              <?php
              if (in_array($user['right_id'], $config['administration']['userOverview']))
              {
                ?>
                <li><a href="internal.php">Kundenaccount-Bereich</a></li>
                <?php
              }
              ?>

              <?php
              if (in_array($user['right_id'], $config['administration']['userBestaetigung']))
              {
                ?>
                <li><a href="confirmation.php">Raumbestellungen</a></li>
                <?php
              }
              ?>

              <?php
              if (in_array($user['right_id'], $config['administration']['userCsv']))
              {
                ?>
                <li><a href="billing.php">CSV-Import/Export</a></li>
                <?php
              }
              ?>

              <?php
              if (in_array($user['right_id'], $config['administration']['userAuswertung']))
              {
                ?>
                <li><a href="statistics.php">Auswertungsbereich</a></li>
                <?php
              }
              ?>
              <li><a href="settings.php">Persönlicher Bereich</a></li>
              <li><a href="logout.php">Logout</a></li>
            </ul>
          </div>
        <?php endif; ?>
      </div>
    </nav>