<?php
require_once ('config.php');
require_once ('check_session.php');
require_once ('db_connect.php');
?>


<html lang = "en">

   <head>
      <title>COP4710 - Dashboard</title>
   </head>

   <body>
         <h2>Welcome, <?php echo $_SESSION['first'].' '.$_SESSION['last'] ?>!</h2>
         <a href="logout.php">Logout</a>
   </body>
</html>
