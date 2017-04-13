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
$after = 0;
$limit = 25;
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
       <?php echo gen_top_nav($curruser->id, 'Schools'); ?>

       <?php if ($univ !== null) { ?>
         <!-- this is the container where the individual page for the University comes up -->

         <div class="container">
           <h1><?php echo $univ->name; ?></h1>
           <?php echo "<label><b>Website: </b></label>" ; echo $univ->website; ?>

           <br>

           <?php echo "<label><b>Email: </b></label>"; echo $univ->email; ?>
           <p><?php echo $univ->description; ?></p>

           <div class="container" style="background-color:#f1f1f1">
             <button class = "buttonLogin" type="submit" name="register">Register</button>
           </div>

         </div>
       <?php } else { ?>
         <?php
           echo gen_univeristy_search_list($search, '../php/universityPage.php', $after, $limit);
         ?>
       <?php } ?>

   </body>
</html>
