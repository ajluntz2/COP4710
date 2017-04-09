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
   </head>

   <body>
     <h2>Welcome, <?php echo $curruser->name(); ?>!</h2>
     <?php
     if ($university !== null)
     {
       echo "<h3>".$university->name."</h3>";
     }
     if ($rsos !== null)
     {
       foreach ($rsos as &$rso)
       {
         echo "<h4>";
         if (!$rso['approved'])
         {
           echo "(Awaiting approval) ";
         }
         echo $rso['name'];
         if ($rso['adminid'] == $curruser->id)
         {
           echo " [admin]";
         }
         echo "</h4>";
       }
     }
     ?>
   </body>
   <footer>
     <a href="logout.php">Logout</a>
   </footer>
</html>
