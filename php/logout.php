<?php
    require_once('utils.php');
    db_logout();

    require_once("config.php");
    header('Location:./index.php');
?>
