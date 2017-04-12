<?php
require_once ('config.php');
require_once ('check_session.php');

require_once ('university.php');
require_once ('rso.php');
require_once ('location.php');
require_once ('user.php');

$curruser = new user_info();
$curruser->updateOnId($_SESSION['userid']);

$univ = null;
$search = '';
if ($_SERVER['REQUEST_METHOD'] == "GET")
{
  if (isset($_GET['id']))
  {
    $univ = new university_info();
    if (!$univ->updateOnId($_GET['id']))
    {
      $univ = null;
    }
  }
  if (isset($_GET['search']))
  {
    $search = $_GET['search'];
  }
}
?>

<html lang = "en">

   <head>
    <title>COP4710 - Schools</title>
    <link href="../css/style.css" rel="stylesheet">
   </head>

   <body>
       <?php echo gen_top_nav($curruser->id, 'Schools'); ?>

       <?php if ($univ !== null) { ?>
         <div class="container">
           <?php echo gen_univeristy_card($univ->id); ?>
         </div>
       <?php } else { ?>
         <?php
           echo gen_univeristy_search_list($search, '../php/universityPage.php');
         ?>
       <?php } ?>

   </body>
</html>
