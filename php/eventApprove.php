<?php
require_once ('config.php');
require_once ('check_session.php');

require_once ('university.php');
require_once ('rso.php');
require_once ('location.php');
require_once ('user.php');

$curruser = new user_info();
$curruser->updateOnId($_SESSION['userid']);

$event = new event_info();
$univ = new university_info();
if ($event->updateOnId($_GET['id']) && $univ->updateOnId($event->universityid) && $univ->super == $curruser->id)
{
  $event->approved = 1;
  $event->syncFields();
}
?>


<?php echo "<script>window.open('../php/universityPage.php?id=$univ->id', '_parent');</script>"; ?>
