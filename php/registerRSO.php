<?php
ob_start();
require_once ('config.php');
require_once ('check_session.php');
require_once('utils.php');
require_once('rso.php');
?>

<?php

$curruser = new user_info();
$curruser->updateOnId($_SESSION['userid']);

$success = '';
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
  $rso = new rso_info();

  $univid = $_POST['school'];
  $name = $_POST['name'];
  $description = $_POST['description'];

  $rso = $rso->addRSO($curruser->id, $univid,
                     $name, $description, $success);
  if ($rso !== null)
  {
    echo "<script type='text/javascript'>window.open('../php/rsoPage.php?id=".$rso->id."','_parent');</script>";
  }
  else
  {
   echo "<script type='text/javascript'>window.open('../php/registerRSO.php','_parent');</script>";
  }
}
?>

<html>
<head>
  <title>COP4710 - RSO Create</title>
  <link href="../css/style.css" rel="stylesheet">
</head>

<body>
  <?php echo gen_top_nav($curruser->id, 'New RSO'); ?>
  <h1><?php echo $success; ?></h1>
  <iframe
    style="
    border: none;
    margin: 0 auto;
    display: block;
    height: 90%;
    width: 100%;"

    src="../php/RSORegisterFrame.php"></iframe>
</body>
</html>
