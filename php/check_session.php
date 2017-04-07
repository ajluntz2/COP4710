<?php
    ob_start();
    require_once('utils.php');
    if (!db_validate())
    {
        require_once('config.php');
        header('Location:./login.php');
    }
?>
