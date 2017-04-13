<?php
require_once ('config.php');
require_once ('check_session.php');

require_once ('university.php');
require_once ('rso.php');
require_once ('location.php');
require_once ('user.php');

$curruser = new user_info();
$curruser->updateOnId($_SESSION['userid']);

$rso = null;
$search = '';
$after = 0;
$limit = 25;
if ($_SERVER['REQUEST_METHOD'] == "GET")
{
  if (isset($_GET['id']))
  {
    $rso = new rso_info();
    if (!$rso->updateOnId($_GET['id']))
    {
      $rso = null;
    }
  }
  if (isset($_GET['search']))
  {
    $search = $_GET['search'];
  }
  if (isset($_GET['after']))
  {
    $after = $_GET['after'];
  }
  if (isset($_GET['limit']))
  {
    $limit = $_GET['limit'];
  }
}
?>

<html lang = "en">

   <head>
    <title>COP4710 -
    <?php if ($rso !== null) { echo $rso->name; }
          else { echo "RSOs"; } ?>
    </title>
    <link href="../css/style.css" rel="stylesheet">
   </head>

   <body>
       <?php echo gen_top_nav($curruser->id, 'RSOs'); ?>

       <?php if ($rso !== null) { ?>
         <div class="container" style="width:50%; margin:0 auto;">

           <h1><?php echo $rso->name; ?></h1>

           <div class="container" style="width:100%; display:block;">
             <button class = "buttonEdit" type="submit" name="edit">Edit</button>

             <button class = "buttonLogin" type="submit" name="join">Join</button>
           </div>

           <div style="float:left; margin:0; max-width:50%;">
             <p><?php echo $rso->description; ?></p>
         </div>
           <br>
           <br>

         </div>
       <?php } else { ?>
         <?php

          echo gen_rso_search_list($search, '../php/rsoPage.php', $after, $limit);

         ?>
       <?php } ?>

   </body>
</html>
