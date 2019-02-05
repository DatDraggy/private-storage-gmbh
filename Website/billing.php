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
include("templates/header.inc.php");
?>

<div class="container main-container">

  <h1>Herzlich Willkommen!</h1>

  Hallo <?php echo htmlentities($user['vorname']); ?>,<br>
  Herzlich Willkommen im CSV-Import/Export Bereich!<br><br>

 



  
  <?php
  if(in_array($user['right_id'], $config['administration']['userBilling'])) {
    ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            Kundenabrechnung
            <a href="exportData.php" class="del_btn">CSV Export</a>
			<a href="payment_import.php" class="edit_btn">CSV Import</a>
        </div>
	<div class="panel-body">
        <table class="table table-bordered">
		<thead>
        <tr>
          <th>Position</th>
          <th>UserID</th>
          <th>Menge</th>
          <th>Datum</th>
        </tr>
		</thead>
		<tbody>
       <?php

                    
                    //get records from database
                    $query = $pdo->query("SELECT * FROM zahlungen ORDER BY id DESC");
                    if($query->num_rows > 0){ 
                        while($row = $query->fetch_assoc()){ ?> 
          <tr>
            <td><?php echo $count++; ?></td>
            <td><?php echo $row['user_id']; ?></td>
            <td><?php echo $row['menge']; ?></td>
            <td><?php echo $row['datum']; ?></td>
          </tr>
  <?php }  }else{ ?>
                    <tr><td colspan="5">Keine Buchungen zur Abrechnung vorhanden.....</td></tr>
  <?php } } ?>

        </tbody>
      </table>
    </div>
  </div>
<!-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
<!-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
    <div id="wrap">
	
        <div class="container">
            <div class="row">
 
                <form class="form-horizontal" action="import.php" method="post" name="upload_excel" enctype="multipart/form-data">
                    <fieldset>
 
                        <!-- Form Name -->
                        <legend>Form Name</legend>
 
                        <!-- File Button -->
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="filebutton">Select File</label>
                            <div class="col-md-4">
                                <input type="file" name="file" id="file" class="input-large">
                            </div>
                        </div>
 
                        <!-- Button -->
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="singlebutton">Import data</label>
                            <div class="col-md-4">
                                <button type="submit" id="submit" name="Import" class="btn btn-primary button-loading" data-loading-text="Loading...">CSV-Import</button>
                            </div>
                        </div>
 
                    </fieldset>
                </form>
  <div>
            <form class="form-horizontal" action="export.php" method="post" name="upload_excel"   
                      enctype="multipart/form-data">
                  <div class="form-group">
                            <div class="col-md-4 col-md-offset-4">
                                <input type="submit" name="Export" class="btn btn-success" value="CSV Export"/>
                            </div>
                   </div>                    
            </form>           
 </div>
            </div>
            <?php
               get_all_records();
            ?>
        </div>
    </div>        
		
<?php
include("templates/footer.inc2.php")
?>
