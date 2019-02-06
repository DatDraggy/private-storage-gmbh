<?php
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");
require_once("inc/permissions.php");

//Überprüft, dass der User eingeloggt ist
//Der Aufruf von check_user() muss in alle internen Seiten eingebaut sein
$userId = $_SESSION['userid'];
if (isset($_GET['userid']) && $_GET['userid'] != $_SESSION['userid']) {
  $userId = $_GET['userid'];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['userid'])) {
    $userId = $_POST['userid'];
  } else {
    die();
  }
}
$user = check_user($userId);
$editor = check_user($_SESSION['userid']);
include("templates/header.inc.php");

if (isset($_POST['save'])) {
  $save = $_POST['save'];
  $editBank = false;
  $editUser = false;
  $canChangeRank = false;
  if ($userId == $_SESSION['userid']) {
    $editBank = true;
    $editUser = true;
    $canChangeRank = true;
  } else {
    if (in_array($editor['right_id'], $config['administration']['userEditBank'])) {
      $editBank = true;
    }
    if (in_array($editor['right_id'], $config['administration']['userEdit'])) {
      $editUser = true;
    }
    if (in_array($editor['right_id'], $config['administration']['userEditRank'])) {
      $canChangeRank = true;
    }
  }
  if ($save == 'personal_data') {
    $firma = trim($_POST['firma']);
    $vorname = trim($_POST['vorname']);
    $nachname = trim($_POST['nachname']);
    $strasse = trim($_POST['strasse']);
    $hausnr = trim($_POST['hausnr']);
    $plz = trim($_POST['plz']);
    $ort = trim($_POST['ort']);
    $rang = trim($_POST['rang']);
    if ($vorname == "" || $nachname == "") {
      $error_msg = "Bitte komplett ausfüllen.";
    } else {
      if ($editUser) {
        $statement = $pdo->prepare("UPDATE users SET vorname = :vorname, nachname = :nachname, updated_at=NOW() WHERE id = :userid");
        $result = $statement->execute(array(
          'vorname' => $vorname,
          'nachname' => $nachname,
          'userid' => $userId
        ));
        $statement = $pdo->prepare("UPDATE adressen SET firma = :firma, strasse = :strasse, hausnr = :hausnr, plz = :plz, ort = :ort WHERE user_id = :user_id");
        $result = $statement->execute(array(
          'firma' => $firma,
          'strasse' => $strasse,
          'hausnr' => $hausnr,
          'plz' => $plz,
          'ort' => $ort,
          'user_id' => $userId
        ));

        $success_msg = "Daten erfolgreich gespeichert.";
      }
      if ($canChangeRank) {

        $statement = $pdo->prepare("SELECT count(id) as countid FROM user_personal WHERE user_id = :userid");
        $statement->execute(array('userid' => $userId));
        $result = $statement->fetch();
        if ($result['countid'] > 0) {
          //Update
          $statement = $pdo->prepare("UPDATE user_personal SET right_id = :rightid WHERE user_id = :userid");
          $result = $statement->execute(array(
            'rightid' => $rang,
            'userid' => $userId
          ));
        } else {
          //Insert
          $statement = $pdo->prepare("INSERT INTO user_personal(user_id, right_id) VALUES(:userid, :rightid)");
          $result = $statement->execute(array(
            'rightid' => $rang,
            'userid' => $userId
          ));
          $success_msg = "Daten erfolgreich gespeichert.";
        }
      } else {
        $success_msg = "Keine Rechte";
      }
    }
  } else if ($save == 'email') {
    $passwort = $_POST['passwort'];
    $email = trim($_POST['email']);
    $email2 = trim($_POST['email2']);

    if ($email != $email2) {
      $error_msg = "Die eingegebenen E-Mail-Adressen stimmten nicht überein.";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $error_msg = "Bitte eine gültige E-Mail-Adresse eingeben.";
    } else if (!password_verify($passwort, $user['passwort'])) {
      $error_msg = "Bitte korrektes Passwort eingeben.";
    } else {
      if ($editUser) {
        $statement = $pdo->prepare("UPDATE users SET email = :email WHERE id = :userid");
        $result = $statement->execute(array(
          'email' => $email,
          'userid' => $userId
        ));

        $success_msg = "E-Mail-Adresse erfolgreich gespeichert.";
      } else {
        $success_msg = "Keine Rechte";
      }
    }

  } else if ($save == 'passwort') {
    $passwortAlt = $_POST['passwortAlt'];
    $passwortNeu = trim($_POST['passwortNeu']);
    $passwortNeu2 = trim($_POST['passwortNeu2']);

    if ($passwortNeu != $passwortNeu2) {
      $error_msg = "Die eingegebenen Passwörter stimmten nicht überein.";
    } else if ($passwortNeu == "") {
      $error_msg = "Das Passwort darf nicht leer sein.";
    } else if (!password_verify($passwortAlt, $user['passwort'])) {
      $error_msg = "Bitte korrektes Passwort eingeben.";
    } else {
      $passwort_hash = password_hash($passwortNeu, PASSWORD_DEFAULT);
      if ($editUser) {
        $statement = $pdo->prepare("UPDATE users SET passwort = :passwort WHERE id = :userid");
        $result = $statement->execute(array(
          'passwort' => $passwort_hash,
          'userid' => $userId
        ));

        $success_msg = "Passwort erfolgreich gespeichert.";
      } else {
        $success_msg = "Keine Rechte";
      }
    }
  } else if ($save == 'bank_data') {
    $iban = trim($_POST['iban']);
    $bic = trim($_POST['bic']);
    if (isset($_POST['isVerified'])) {
      $bestaetigt = 1;
    } else {
      $bestaetigt = 0;
    }

    if (empty($iban) || empty($bic)) {
      $error_msg = "Bitte IBAN und BIC ausfüllen.";
    } else {
      if ($editBank) {
        $statement = $pdo->prepare("UPDATE user_bankdaten SET iban = :iban, bic = :bic, bestaetigt = :bestaetigt WHERE id = :userid ");
        $statement->bindParam(':iban', $iban);
        $statement->bindParam(':bic', $bic);
        $statement->bindParam(':bestaetigt', $bestaetigt);
        $statement->bindParam(':userid', $userId);
        $result = $statement->execute();
        $success_msg = "Daten erfolgreich gespeichert.";
      } else {
        $success_msg = "Keine Rechte";
      }
    }
  }
}

$user = check_user($userId);
$viewUser = false;
$viewUserBank = false;
$userViewEmail = false;
if ($userId == $_SESSION['userid']) {
  $viewUser = true;
  $viewUserBank = true;
  $userViewEmail = true;

}
if (in_array($editor['right_id'], $config['administration']['userView'])) {
  $viewUser = true;
}
if (in_array($editor['right_id'], $config['administration']['userViewBank'])) {
  $viewUserBank = true;
}
if (in_array($editor['right_id'], $config['administration']['userViewEmail'])) {
  $userViewEmail = true;
}

?>

<div class="container main-container">

    <h1>Accountdaten</h1>

  <?php
  if (isset($success_msg) && !empty($success_msg)):
    ?>
      <div class="alert alert-success">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <?php echo $success_msg; ?>
      </div>
  <?php
  endif;
  ?>

  <?php
  if (isset($error_msg) && !empty($error_msg)):
    ?>
      <div class="alert alert-danger">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <?php echo $error_msg; ?>
      </div>
  <?php
  endif;
  ?>

  <?php
  if (isset($delete_msg) && !empty($delete_msg)):
    ?>
      <div class="alert alert-delete">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <?php echo $error_msg; ?>
      </div>
  <?php
  endif;
  ?>

    <div>

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#data" aria-controls="home" role="tab" data-toggle="tab">Persönliche
                    Daten</a></li>
            <li role="presentation"><a href="#bank" aria-controls="profile" role="tab"
                                       data-toggle="tab">Bankverbindung</a>
            </li>
            <li role="presentation"><a href="#email" aria-controls="profile" role="tab" data-toggle="tab">E-Mail</a>
            </li>
            <li role="presentation"><a href="#passwort" aria-controls="messages" role="tab"
                                       data-toggle="tab">Passwort</a>
            </li>
        </ul>

        <!-- Persönliche Daten-->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="data">
                <br>
                <form action="" method="post" class="form-horizontal">
                    <input type="hidden" name="save" value="personal_data">
                    <div class="form-group">
                        <label for="inputId" class="col-sm-2 control-label">User ID</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="inputId" name="userid" type="number" readonly
                                   value="<?php echo htmlentities($userId); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputFirma" class="col-sm-2 control-label">Firma</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="inputFirma" name="firma" type="text"
                                   value="<?php if ($viewUser) {
                                     echo htmlentities($user['firma']);
                                   } ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputVorname" class="col-sm-2 control-label">Vorname</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="inputVorname" name="vorname" type="text"
                                   value="<?php if ($viewUser) {
                                     echo htmlentities($user['vorname']);
                                   } ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputNachname" class="col-sm-2 control-label">Nachname</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="inputNachname" name="nachname" type="text"
                                   value="<?php if ($viewUser) {
                                     echo htmlentities($user['nachname']);
                                   } ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputStrasse" class="col-sm-2 control-label">Straße</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="inputStrasse" name="strasse" type="text"
                                   value="<?php if ($viewUser) {
                                     echo htmlentities($user['strasse']);
                                   } ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputHausnummer" class="col-sm-2 control-label">Hausnummer</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="inputHausnummer" name="hausnr" type="text"
                                   value="<?php if ($viewUser) {
                                     echo htmlentities($user['hausnr']);
                                   } ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputPlz" class="col-sm-2 control-label">Postleitzahl</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="inputPlz" name="plz" type="text"
                                   value="<?php if ($viewUser) {
                                     echo htmlentities($user['plz']);
                                   } ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputOrt" class="col-sm-2 control-label">Ort</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="inputOrt" name="ort" type="text"
                                   value="<?php if ($viewUser) {
                                     echo htmlentities($user['ort']);
                                   } ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputRang" class="col-sm-2 control-label">Rang</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="inputRang" name="rang" type="number"
                                   value="<?php if ($viewUser) {
                                     echo htmlentities($user['right_id']);
                                   } ?>" <?php if (!in_array($editor['right_id'], $config['administration']['userEditRank'])) {
                              echo 'disabled';
                            } ?> required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary">Speichern</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Änderung der Bankverbindung -->

            <div role="tabpanel" class="tab-pane" id="bank">
                <br>
                <form action="" method="post" class="form-horizontal">
                    <input type="hidden" name="save" value="bank_data">
                    <div class="form-group">
                        <label for="inputId" class="col-sm-2 control-label">User ID</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="inputId" name="userid" type="number" readonly
                                   value="<?php echo htmlentities($user['id']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputIban" class="col-sm-2 control-label">IBAN</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="inputIban" name="iban" type="text"
                                   value="<?php if ($viewUserBank) {
                                     echo htmlentities($user['iban']);
                                   } ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputBic" class="col-sm-2 control-label">BIC</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="inputBic" name="bic" type="text"
                                   value="<?php if ($viewUserBank) {
                                     echo htmlentities($user['bic']);
                                   } ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="isVerified" class="col-sm-2 control-label">Verifiziert</label>
                        <div class="col-sm-10">
                            <input <?php if (!in_array($editor['right_id'], $config['administration']['userEditBank'])) {
                              echo 'disabled';
                            } ?> class="form-control" id="isVerified" name="isVerified"
                                 type="checkbox" <?php if ($user['bestaetigt'] == 1) {
                              echo 'checked';
                            } ?>>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary">Speichern</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Änderung der E-Mail-Adresse -->
            <div role="tabpanel" class="tab-pane" id="email">
                <br>
                <p>Zum Änderen deiner E-Mail-Adresse gib bitte dein aktuelles Passwort sowie die neue E-Mail-Adresse
                    ein.</p>
                <form action="" method="post" class="form-horizontal">
                    <input type="hidden" name="save" value="email">
                    <div class="form-group">
                        <label for="inputId" class="col-sm-2 control-label">User ID</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="inputId" name="userid" type="number" readonly
                                   value="<?php echo htmlentities($user['id']); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputPasswort" class="col-sm-2 control-label">Passwort</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="inputPasswort" name="passwort" type="password" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputEmail" class="col-sm-2 control-label">E-Mail</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="inputEmail" name="email" type="email"
                                   value="<?php if ($userViewEmail) {
                                     echo htmlentities($user['email']);
                                   } ?>" required>
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="inputEmail2" class="col-sm-2 control-label">E-Mail (wiederholen)</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="inputEmail2" name="email2" type="email" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary">Speichern</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Änderung des Passworts -->
            <div role="tabpanel" class="tab-pane" id="passwort">
                <br>
                <p>Zum Änderen deines Passworts gib bitte dein aktuelles Passwort sowie das neue Passwort ein.</p>
                <form action="" method="post" class="form-horizontal">
                    <input type="hidden" name="save" value="passwort">
                    <div class="form-group">
                        <label for="inputPasswort" class="col-sm-2 control-label">Altes Passwort</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="inputPasswort" name="passwortAlt" type="password" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputPasswortNeu" class="col-sm-2 control-label">Neues Passwort</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="inputPasswortNeu" name="passwortNeu" type="password"
                                   required>
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="inputPasswortNeu2" class="col-sm-2 control-label">Neues Passwort
                            (wiederholen)</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="inputPasswortNeu2" name="passwortNeu2" type="password"
                                   required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary">Speichern</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


    </div>
  <?php
  include("templates/footer.inc2.php");
  ?>
