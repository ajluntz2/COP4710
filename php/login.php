<?php
ob_start();
require_once ('config.php');
require_once('utils.php');

if (db_validate())
{
  echo "<script type='text/javascript'>window.open('./logout.php','_parent');</script>";
}
?>

<?php
$err = "<h4 class=\"error\">";
$end = "</h4>";
$success = $errMsg = "";
$email = $password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
  require_once('db_connect.php');

  $email = $_POST['email'];
  $password = $_POST['password'];

  $db = new database_connect();
  $good = db_login($db, $email, $password, $errMsg);

  if ($good)
  {
    $success = $err."SUCCESS!".$end;
    echo "<script type='text/javascript'>window.open('./index.php','_parent');</script>";
  }
  else
  {
    $success = $err.$errMsg.$end;
    $db->close();
  }
}
?>

<html lang="en">

  <head>
    <title>COP4710 - Login</title>
    <link href="../css/style.css" rel="stylesheet">
  </head>

  <body>
    <!-- <iframe
      style="
      border: none;"
      src="../HTML/titleFrame.html"></iframe> -->
    <iframe
      style="
      border: none;
      margin: 0 auto;
      display: block;
      max-height: 500px;
      min-height: 380px;"

      src="../HTML/loginFrame.html"></iframe>
  </body>
</html>
