<?php
    require_once('utils.php');
    db_logout();

    require_once("config.php");
    echo "<script type='text/javascript'>window.open('../index.php','_parent');</script>";
?>
