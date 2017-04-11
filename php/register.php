<?php
    require_once('config.php');
    ob_start();

    require_once('utils.php');
    if (db_validate()) {
      db_logout();
    }

    $univ = new university_info();
    $univs = $univ->getUniversities(0, -1);

    $err = "<h4 class=\"error\">";
    $end = "</h4>";

    $success = "";
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
        $university = intval($_POST['school']);

        require_once('user.php');
        require_once('university.php');
        $userinfo = new user_info();
        $univinfo = null;

        $gooduniv = false;
        $useuniv = ($university > -1) ? true : false;
        if ($useuniv)
        {
          $univinfo = new university_info();
          $gooduniv = $univinfo->updateOnId($university);
        }
        else
        {
          $university = 'NULL';
        }

        if ($useuniv && !$gooduniv)
        {
          $success = $err.'could not find university in database'.$end;
        }
        else if ($userinfo->addUser($first, $last, $email, $password, $university) !== null)
        {
          $success = $err.'SUCCESS'.$end;

          $login_err = '';
          if (!db_login($userinfo->getDatabase(), $email, $password, $login_err))
          {
            $success = $err.'Could not login in: '.$login_err.$end;
          }
          else
          {
            echo "<script type='text/javascript'>window.open('./index.php','_parent');</script>";
          }
        }
        else
        {
          $success = $err.'an error occured, good luck!'.$end;
        }
      }
    }
?>

<html lang = "en">

   <head>
      <title>COP4710 - Register</title>
   </head>

   <body>

      <h1>New User Register</h1>
      <div class = "container">
         <form class = "form-register" role = "form" action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method = "post">

            <h5 class = "form-register-heading">COP4710</h5>
            <h5 class = "form-register-heading"><?php echo $success; ?></h5>
            <input type="text" class="form-control" name = "first" placeholder="John" required autofocus></br>
            <input type="text" class="form-control" name = "last" placeholder="Snow" required></br>

            <input type="text" class="form-control" name = "email" placeholder="johnsnow@myschool.edu" required></br>

            <select name="school">
              <?php
                echo "<option value='-1'>None</option>";
                foreach ($univs as &$U)
                {
                  echo "<option value='".$U->id."'>".$U->name."</option>";
                }
              ?>
            </select>

            <h5 class = "empty-space"></h5>

            <input type="password" class="form-control" name="password1" placeholder="password" required>
            <input type="password" class="form-control" name="password2" placeholder="confirm password" required>

            <button class = "btn btn-lg btn-primary btn-block" type="submit" name="register">Register</button>
         </form>

         <h5><a href="login.php">Login.</a></h5>


      </div>

   </body>
</html>
