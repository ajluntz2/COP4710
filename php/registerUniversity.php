<?php
ob_start();
require_once ('config.php');
require_once ('check_session.php');
require_once('utils.php');
require_once('university.php');
?>

<?php
$curruser = new user_info();
$curruser->updateOnId($_SESSION['userid']);
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
  require_once('db_connect.php');
  $univ = new university_info();

  $name = $_POST['name'];
  $website = $_POST['website'];
  $email = $_POST['email'];
  $locationid = $_POST['locationid'];

  $univ = $univ->addUniversity($curruser->id, $locationid,
                               $name, $website, $email);

  if ($univ !== null)
  {
    echo "<script type='text/javascript'>window.open('./dashboard.php','_parent');</script>";
  }
}
?>

<html>
<head>
  <title>COP4710 - School Create</title>
  <link href="../css/style.css" rel="stylesheet">
</head>

<body>
  <?php echo gen_top_nav($curruser->id, 'New School'); ?>
  <iframe
    style="
    border: none;
    margin: 0 auto;
    display: block;
    height: 90%;
    width: 100%;"

    frameBorder="0"

    src="../HTML/universityRegisterTest.html"></iframe>
</body>
</html>
