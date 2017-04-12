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
    <title>COP4710 - Schools</title>
    <link href="../css/style.css" rel="stylesheet">
   </head>

   <body>
       <?php echo gen_top_nav($curruser->id, 'RSOs'); ?>

       <?php if ($rso !== null) { ?>
         <div class="container">
           <?php echo gen_rso_card($rso->id); ?>
         </div>
       <?php } else { ?>
         <?php

          echo gen_rso_search_list($search, '../php/rsoPage.php', $after, $limit);

         ?>
       <?php } ?>

   </body>
</html>
