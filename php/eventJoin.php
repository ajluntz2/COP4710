<?php
require_once ('config.php');
require_once ('check_session.php');

require_once ('university.php');
require_once ('rso.php');
require_once ('location.php');
require_once ('user.php');

$curruser = new user_info();
$curruser->updateOnId($_SESSION['userid']);

$event = null;
if ($_SERVER['REQUEST_METHOD'] == "POST")
{
  $attend = $_GET['attend'];

  $event = new event_info();
  $event->updateOnId($_GET['id']);

  if ($attend)
  {
    $update = "
    INSERT INTO attending
      (eventid, userid)
    VALUES
      (".$event->id.", ".$curruser->id.")";

    $event->query($update);
  }
  else
  {
    $delete = "DELETE FROM attending
    WHERE
    eventid = ".$event->id." AND
    userid = ".$curruser->id;

    $event->query($delete);
  }
}
?>


<?php echo "<script>window.open('../php/eventPage.php?id=$event->id', '_parent');</script>"; ?>
