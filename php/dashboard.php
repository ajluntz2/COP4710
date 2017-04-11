<?php
require_once ('config.php');
require_once ('check_session.php');

require_once ('university.php');
require_once ('rso.php');
require_once ('location.php');
require_once ('user.php');

$curruser = new user_info();
$curruser->updateOnId($_SESSION['userid']);
?>

<html lang = "en">

   <head>
    <title>COP4710 - Dashboard</title>
    <link href="../css/style.css" rel="stylesheet">
   </head>

   <body>
     <?php echo gen_top_nav($curruser->id, 'Home'); ?>
     <?php echo gen_univeristy_slider($curruser->id); ?>
     <?php echo gen_rso_slider($curruser->id); ?>
   </body>
</html>
