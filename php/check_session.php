<?php
    ob_start();
    require_once('utils.php');
    if (!db_validate())
    {
        require_once('config.php');
        echo "<script type='text/javascript'>window.open('./login.php','_parent');</script>";
    }
?>
