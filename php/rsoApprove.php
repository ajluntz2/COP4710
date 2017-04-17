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
$rso = new rso_info();
$univ = new university_info();
if ($rso->updateOnId($_GET['id']) && $univ->updateOnId($rso->universityid) && $univ->super == $curruser->id)
{
  $rso->approved = 1;
  $rso->syncFields();
}
?>


<?php echo "<script>window.open('../php/universityPage.php?id=$univ->id', '_parent');</script>"; ?>
