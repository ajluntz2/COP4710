<?php
require_once ('config.php');
require_once ('check_session.php');

require_once ('university.php');
require_once ('location.php');
require_once ('user.php');

$curruser = new user_info();
$curruser->updateOnId($_SESSION['userid']);

$university = new university_info();
if ($university->updateOnId($curruser->universityid))
{
  $unv_location = new location_info($university->locationid);
}
else
{
    $university = null;
    $unv_location = null;
}
?>

<html lang = "en">

   <head>
      <title>COP4710 - Dashboard</title>
   </head>

   <body>
         <h2>Welcome, <?php echo $curruser->name(); ?>!</h2>

         <h3>
           <?php
           if ($university !== null)
           {
             echo $university->name;
           }
           ?>
         </h3>
   </body>
   <footer>
     <a href="logout.php">Logout</a>
   </footer>
</html>
