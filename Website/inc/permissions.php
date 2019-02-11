<?php
/*
 * Dateiname: permissions.php
 * Autor: Marlin, Dennis, Jason
 *
 * Version: 1.1
 * letzte Änderung: 11. Februar 2019
 *
 * Inhalt: Rechtesystem, wer was machen darf
 *
 * Verwendete Funktionen:
 *
 * Definierte Funktionen:
 *
 * globale Variablen:
 */
/*
 * 0 = Kunde 3
 * 1 = Geschäftsleitung 41
 * 2 = Admin 35
 * 3 = Mitarbeiter 37
 */
$config['administration']['userEdit'] = array(
  1,
  2,
  3
);
$config['administration']['userEditBank'] = array(3);
$config['administration']['userEditRank'] = array(1);
$config['administration']['userView'] = array(
  1,
  2,
  3
);
$config['administration']['userViewEmail'] = array(1);
$config['administration']['userDelete'] = array(
  1,
  2
);
$config['administration']['userViewBank'] = array(
  1,
  3
);
$config['administration']['userAdd'] = array(2);
$config['administration']['userAuswertung'] = array(1);
$config['administration']['userOverview'] = array(
  1,
  2,
  3
);
$config['administration']['userCsv'] = array(3);
$config['administration']['userBilling'] = array(3);
$config['administration']['userBestaetigung'] = array(3);