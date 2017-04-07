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
      <title>COP4710 - Login</title>
   </head>

   <body>

      <h2>Enter Username and Password</h2>
      <div class = "container">
         <form class = "form-signin" role = "form"
            action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method = "post">

            <h4 class = "form-signin-heading">COP4710</h4>
            <h4 class = "form-signin-heading"><?php echo $success; ?></h4>
            <input type="text" class="form-control" name = "email" placeholder="johnsnow@myschool.edu" required autofocus></br>
            <input type="password" class="form-control" name="password" placeholder="password" required>
            <button class = "btn btn-lg btn-primary btn-block" type="submit" name="login">Login</button>
         </form>

         <a href="register.php">Register an account.</a>


      </div>

   </body>
</html>
