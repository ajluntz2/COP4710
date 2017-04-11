<?php
require_once ('config.php');
require_once ('check_session.php');

require_once ('university.php');
require_once ('rso.php');
require_once ('location.php');
require_once ('user.php');

$curruser = new user_info();
$curruser->updateOnId($_SESSION['userid']);

$university = new university_info();
if (!$university->updateOnId($curruser->universityid))
{
  $university = null;
}
$rsos = new rso_info();
$rsos = $rsos->getOnMember($curruser->id);
?>

<html lang = "en">

   <head>
    <title>COP4710 - Dashboard</title>
    <link href="../css/style.css" rel="stylesheet">
   </head>

   <body>
     <?php echo gen_top_nav($curruser->id); ?>

     <h2>Welcome, <?php echo $curruser->name(); ?>!</h2>
     <?php
     if ($university !== null)
     {
       echo gen_univeristy_card($university->id, $curruser->id);
     }
     if ($rsos !== null)
     {
       foreach ($rsos as &$rso)
       {
         echo gen_rso_card($rso['rsoid']);
       }
     }
     ?>
   </body>
   <footer>
     <a href="logout.php">Logout</a>
   </footer>
</html>
