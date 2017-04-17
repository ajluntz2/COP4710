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
if ($_SERVER['REQUEST_METHOD'] == "POST" && $event->updateOnId($_GET['id']))
{
  $zero   = isset($_POST['rating']) && $_POST['rating'] == 0 ? 1 : 0;
  $one    = isset($_POST['rating']) && $_POST['rating'] == 1 ? 1 : 0;
  $two    = isset($_POST['rating']) && $_POST['rating'] == 2 ? 1 : 0;
  $three  = isset($_POST['rating']) && $_POST['rating'] == 3 ? 1 : 0;
  $four   = isset($_POST['rating']) && $_POST['rating'] == 4 ? 1 : 0;
  $five   = isset($_POST['rating']) && $_POST['rating'] == 5 ? 1 : 0;

  $query = "
  UPDATE ratings
  SET
    zero = zero + ".$zero.",
    one = one + ".$one.",
    two = two + ".$two.",
    three = three + ".$three.",
    four = four + ".$four.",
    five = five + ".$five."
  WHERE
    ratingid = ".$event->ratingid;
  $event->query($query);
}
?>


<?php echo "<script>window.open('../php/eventPage.php?id=$event->id', '_parent');</script>"; ?>
