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
if ($_SERVER['REQUEST_METHOD'] == "POST")
{
  $join = $_GET['join'];

  $rso = new rso_info();
  $rso->updateOnId($_GET['id']);

  if ($join)
  {
    $update = "
    INSERT INTO members
      (rsoid, userid)
    VALUES
      (".$rso->id.", ".$curruser->id.")";

    $rso->query($update);
  }
  else
  {
    $delete = "DELETE FROM members
    WHERE
    rsoid = $rso->id AND
    userid = $curruser->id";

    $rso->query($delete);
  }
}
?>


<?php echo "<script>window.open('../php/rsoPage.php?id=$rso->id', '_parent');</script>"; ?>
