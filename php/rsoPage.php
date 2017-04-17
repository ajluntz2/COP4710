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
$editing = false;

$ismember = false;

if (isset($_GET['id']))
{
    $rso = new rso_info();

    if (!$rso->updateOnId($_GET['id']))
    {
        $rso = null;
    }
    else
    {
      $query = "
      SELECT DISTINCT
        *
      FROM
        members AS M
      WHERE
        M.rsoid = ".$rso->id." AND
        M.userid = ".$curruser->id;

      $rsos = $rso->queryRows($query);
      if ($rsos !== null)
      {
        $ismember = true;
      }
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

if (isset($_GET['edit']) && $_GET['edit'] == 1 && $rso !== null && $rso->adminid == $curruser->id)
{
    $editing = true;
}

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
    if ($rso !== null)
    {
      $rso->name = $_POST['name'];
      $rso->description = $_POST['description'];

      $rso->syncFields();
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

             <?php if ($editing) { ?>

                 <form class = "form-register" role = "form" action = "../php/rsoPage.php?id=<?php echo $rso->id; ?>" method = "post">

                   <div class="container" style="width:100%; display:block;">
                       <button class = "buttonEdit" type="submit" name="save">Save</button>
                   </div>

                   <div style="display:block;">
                       <div id="column1" style="float:left; margin:0; width:50%;">

                           <h5 style="display: inline;">Name:</h5>
                           <input type="text" class="form-control" name = "name" value="<?php echo $rso->name; ?>" required autofocus ></br>

                           <h5 style="display: inline;">Description:</h5>
                           <textarea type="text" class="form-control" name = "description" required><?php echo $rso->description; ?></textarea>
                           <br>

                       </div>
                 </div>

                 </form>

             <?php } else { ?>

                 <div class="container" style="width:100%; display:block;">
                   <?php if ($rso->adminid == $curruser->id){ ?>
                   <button class = "buttonEdit" type="submit" name="edit" onclick="window.open('<?php echo "../php/rsoPage.php?id=".$rso->id."&edit=1"; ?>', '_parent')">Edit</button>
                   <?php } ?>

                    <?php if ($ismember) { ?>
                       <form action='../php/rsoJoin.php?join=0&id=<?php echo $rso->id; ?>' method="POST">
                         <button class = "buttonLogin" value="attend" type="submit" name="join">Unjoin</button>
                       </form>
                    <?php } else { ?>
                    <?php if ($rso->universityid == $curruser->universityid){ ?>
                      <form action='../php/rsoJoin.php?join=1&id=<?php echo $rso->id; ?>' method="POST">
                       <button class = "buttonLogin" type="submit" name="join">Join</button>
                     </form>
                    <?php } ?>
                    <?php } ?>
                 </div>

                  <div style="float:left; margin:0; max-width:50%;">
                      <p><?php echo $rso->description; ?></p>
                  </div>

             <?php } ?>

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
