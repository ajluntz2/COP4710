<?php
    ob_start();
    require_once ('config.php');
    require_once('utils.php');

    if (db_validate())
    {
      header('Location:./logout.php');
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
      header('Location:./index.php');
    }
    else
    {
      $success = $err.$errMsg.$end;
      $db->close();
    }
  }
?>


<html lang = "en">

   <head>
      <link href="css/style.css" rel="stylesheet">
      <title>COP4710 - Login</title>
   </head>

   <body>

      <h1>Login</h1>
      <div class = "container">
         <form class = "form-signin" role = "form"
            action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method = "post">

            <h5 class = "form-signin-heading">COP4710</h5>
            <h5 class = "form-signin-heading"><?php echo $success; ?></h5>
            <input type="text" class="form-control" name = "email" placeholder="johnsnow@myschool.edu" required autofocus></br>
            <input type="password" class="form-control" name="password" placeholder="password" required>
            <button class = "btn btn-lg btn-primary btn-block" type="submit" name="login">Login</button>
         </form>

         <h5><a href="register.php">Register an account.</a></h5>


      </div>

   </body>
</html>
