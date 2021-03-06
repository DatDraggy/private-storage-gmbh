<?php
/*
 * Dateiname: passwordforget.php
 * Autor: Dennis, Jason
 *
 * Version: 1.1
 * letzte Änderung: 11. Februar 2019
 *
 * Inhalt: Formular zum Passwort zurücksetzen
 *
 * Verwendete Funktionen:
 *  get_site_url
 *
 * Definierte Funktionen:
 *
 * globale Variablen:
 */
session_start();
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");

include("templates/header.inc.php");
?>
  <div class="container small-container-330">
    <h2>Passwort vergessen</h2>


    <?php
    $showForm = true;

    if (isset($_GET['send']))
    {
      if (!isset($_POST['email']) || empty($_POST['email']))
      {
        $error = "<b>Bitte eine E-Mail-Adresse eintragen</b>";
      }
      else
      {
        $statement = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $result = $statement->execute(array('email' => $_POST['email']));
        $user = $statement->fetch();

        if ($user === false)
        {
          $error = "<b>Kein Benutzer gefunden</b>";
        }
        else
        {

          $passwortcode = random_string();
          $statement = $pdo->prepare("UPDATE users SET passwortcode = :passwortcode, passwortcode_time = NOW() WHERE id = :userid");
          $result = $statement->execute(array(
            'passwortcode' => sha1($passwortcode),
            'userid' => $user['id']
          ));

          $empfaenger = $user['email'];
          $betreff = "Neues Passwort für deinen Account bei StorageGmbH"; //Ersetzt hier den Domain-Namen
          $from = "From: Storage GmbH <absender@domain.de>"; //Ersetzt hier euren Name und E-Mail-Adresse
          $url_passwortcode = get_site_url() . 'passwordreset.php?userid=' . $user['id'] . '&code=' . $passwortcode; //Setzt hier eure richtige Domain ein
          $text = 'Hallo ' . $user['vorname'] . ',
für deinen Zugang bei StorageGmbH wurde nach einem neuen Passwort gefragt. Um ein neues Passwort zu vergeben, rufe innerhalb der nächsten 24 Stunden die folgende Website auf:
' . $url_passwortcode . '
 
Sollte dir dein Passwort wieder eingefallen sein oder hast du dies nicht angefordert, so bitte ignoriere diese E-Mail.
 
Viele Grüße,
Das StorageGmbH Team';

          //echo $text;

          mail($empfaenger, $betreff, $text, $from);

          echo "Ein Link um dein Passwort zurückzusetzen wurde an deine E-Mail-Adresse gesendet.";
          $showForm = false;
        }
      }
    }

    if ($showForm):
      ?>
      Gib hier deine E-Mail-Adresse ein, um ein neues Passwort anzufordern.<br><br>

      <?php
      if (isset($error) && !empty($error))
      {
        echo $error;
      }

      ?>
      <form action="?send=1" method="post">
        <label for="inputEmail">E-Mail</label>
        <input class="form-control" placeholder="E-Mail" name="email" type="email" value="<?php echo isset($_POST['email']) ? htmlentities($_POST['email']) : ''; ?>" required>
        <br>
        <input class="btn btn-lg btn-primary btn-block" type="submit" value="Neues Passwort">
      </form>
    <?php
    endif; //Endif von if($showForm)
    ?>

  </div> <!-- /container -->


<?php
include("templates/footer.inc.php")
?>