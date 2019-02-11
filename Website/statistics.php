<?php
/*
 * Dateiname: statistics.php
 * Autor: Marlin
 *
 * Version: 1
 * letzte Änderung: 11. Februar 2019
 *
 * Inhalt: Statistiken mit Diagramm
 *
 * Verwendete Funktionen:
 *   build_database_connection
 *   check_user
 *
 * Definierte Funktionen:
 *
 * globale Variablen:
 */
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");
require_once("inc/permissions.php");
require_once('templates/header.inc.php');

$dbConnection = build_database_connection($config);
$user = check_user();
$rightId = $user['right_id'];

try
{
  $stmt = $dbConnection->prepare('SELECT groesse, count(groesse) as count FROM bestellungen INNER JOIN raeume ON raeume.kennung = bestellungen.kennung WHERE aktiv = 1 GROUP BY groesse');
  $stmt->execute();
  $rows = $stmt->fetchAll();
}
catch (PDOException $e)
{
  echo $e;
}
$data = array();
$label = array();
foreach ($rows as $row)
{
  $data[] = $row['count'];
  $label[] = $row['groesse'];
}


?>
<div class="container center-screen">
  <script src="js/chart.min.js"></script>
  <script>
    window.onload = function () {
      var ctxRoomUsage = document.getElementById("roomUsage");
      var labels = <?=json_encode($label)?>;
      var data = <?=json_encode($data)?>;
      var myChartRoomUsage = new Chart(ctxRoomUsage, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Räume',
            data: data,
            borderWidth: 1,
            backgroundColor: []
          }]
        },
        options: {
          legend: {
            display: false
          },
          scales: {
            xAxes: [{
              scaleLabel: {
                display: true,
                labelString: 'Raum'
              }
            }],
            yAxes: [{
              ticks: {
                beginAtZero: true,
                maxTicksLimit: Math.min(11, 1 + Math.max.apply(null, data)),
                max: Math.max(...data) + 1
              },
              scaleLabel: {
                display: true,
                labelString: 'In Benutzung'
              }
            }]
          }
        }
      });

      const colors = ['rgba(254,218,33, 1)', 'rgba(254,49,110, 1)', 'rgba(46,52,202, 1)', 'rgba(26,251,196, 1)'];
      do {
        colors.some(function (color) {
          if (myChartRoomUsage.data.datasets[0].data.length > myChartRoomUsage.data.datasets[0].backgroundColor.length) {
            myChartRoomUsage.data.datasets[0].backgroundColor.push(color);
          } else {
            return true;
          }
        });
      } while (myChartRoomUsage.data.datasets[0].data.length > myChartRoomUsage.data.datasets[0].backgroundColor.length);
      myChartRoomUsage.update();
    }
  </script>

  <div class="chart-container" style="position: relative; height:400px; width:800px">
    <canvas id="roomUsage"></canvas>
  </div>

  <?php
  include("templates/footer.inc2.php")
  ?>
