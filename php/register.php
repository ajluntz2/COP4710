<?php
    require_once('config.php');
    ob_start();

    require_once('utils.php');
    if (db_validate()) {
      db_logout();
    }

    $err = "<h4 class=\"error\">";
    $end = "</h4>";

    $success = $errMsg = "";
    $first = $last = $email = $password = $university = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
      $pass1 = $_POST["password1"];
      $pass2 = $_POST["password2"];

      # Password
      if (empty($pass1) || empty($pass2))
      {
        $success = $err."Password and password confirmation is required".$end;
      }
      else if (trim($pass1) !== trim($pass2))
      {
        $success = $err."Passwords must match".$end;
      }
      else
      {
        $first = $_POST['first'];
        $last = $_POST['last'];
        $email = $_POST['email'];
        $password = trim($pass1);
        $university = $_POST['university'];

        require_once('db_connect.php');
        $db = new database_connect();

        if (db_add_user($db, $first, $last, $email, $password, $university, $errMsg))
        {
          $success = $err.'SUCCESS'.$end;

          if (!db_login($db, $email, $password, $err))
          {
            $success = $err.'Could not login in: '.$errMsg.$end;
          }
          else
          {
            header('Location:./index.php');
          }
        }
        else
        {
          $success = $err.$errMsg.$end;
        }
      }
    }
?>

<html lang = "en">

   <head>
      <title>COP4710 - Register</title>
   </head>

   <body>

      <h2>New User Register</h2>
      <div class = "container">
         <form class = "form-register" role = "form" action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method = "post">

            <h4 class = "form-register-heading">COP4710</h4>
            <h4 class = "form-register-heading"><?php echo $success; ?></h4>
            <input type="text" class="form-control" name = "first" placeholder="John" required autofocus></br>
            <input type="text" class="form-control" name = "last" placeholder="Snow" required></br>

            <input type="text" class="form-control" name = "email" placeholder="johnsnow@myschool.edu" required></br>
            <input type="text" class="form-control" name = "university" placeholder="University of Central Florida"></br>

            <input type="password" class="form-control" name="password1" placeholder="password" required>
            <input type="password" class="form-control" name="password2" placeholder="confirm password" required>

            <button class = "btn btn-lg btn-primary btn-block" type="submit" name="register">Register</button>
         </form>

         <a href="login.php">Login.</a>


      </div>

   </body>
</html>
