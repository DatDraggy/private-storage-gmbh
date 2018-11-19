<?php
require_once (__DIR__.'/inc/config.inc.php');
if(in_array($_GET['error'], $error)){
    echo $error[$_GET['error']];
}