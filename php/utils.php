<?php
  require_once('config.php');
  require_once('university.php');

  function db_ensure($db)
  {
      if (!$db)
      {
        $db = new database_connect();
      }
      if (!$db->isConnected())
      {
        $db->connect();
      }

      return $db;
  }

  function verify_email_on_db($db, $email, &$err)
  {
    if (!isset($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
    {
      $err = 'bad email';
      return false;
    }

    $db = db_ensure($db);

    $query = "SELECT COUNT(*) FROM users WHERE email = '$email'";
    $results = $db->query($query);
    if (mysqli_num_rows($results) > 0)
    {
        $row = mysqli_fetch_array($results);
        if ($row['COUNT(*)'] > 0)
        {
          return true;
        }
    }
    $err = 'could not find email in database';
    return false;
  }

  function verify_email_not_on_db($db, $email, &$err)
  {
    if (!isset($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
    {
      $err = 'bad email';
      return false;
    }

    $db = db_ensure($db);

    $query = "SELECT COUNT(*) FROM users WHERE email = '$email'";
    $results = $db->query($query);
    if (mysqli_num_rows($results) > 0)
    {
        $row = mysqli_fetch_array($results);
        if ($row['COUNT(*)'] > 0)
        {
          $err = 'foud find email in database';
          return false;
        }
    }
    return true;
  }

  function verify_university_on_db($db, $name, &$id, &$err)
  {
    if (!isset($name))
    {
      $err = 'bad university';
      return false;
    }

    $db = ensure_db($db);

    $query = "SELECT universityid FROM universities WHERE name = '$name'";
    $results = $db->query($query);
    if (mysqli_num_rows($results) > 0)
    {
        $row = mysqli_fetch_array($results);
        $id = $row['universityid'];
        return true;
    }
    $err = 'could not find university in database';
    return false;
  }

  function db_login($db, $email, $password, &$err)
  {
    $err = '';
    if (!isset($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
    {
      $err = 'bad email';
    }
    if (!isset($password))
    {
      $err = 'password is required';
    }
    if (!empty($err))
    {
      return false;
    }
    $db = db_ensure($db);

    $password = md5(trim($password));
    $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";

    $check_user = $db->query($query);
    if (mysqli_num_rows($check_user) > 0)
    {
      $row = mysqli_fetch_array($check_user);

      require_once('user.php');
      $_SESSION['valid'] = true;
      $_SESSION['timeout'] = time();
      $_SESSION['userid'] = $row['userid'];

      return true;
    }
    else
    {
      $err = 'invalid email or password';
      return false;
    }
  }

  function db_logout()
  {
    $valid = db_validate();
    if(session_status() == PHP_SESSION_ACTIVE)
    {
      session_destroy();
    }

    if ($valid)
    {
      $_SESSION['userid'] = null;
    }
    $_SESSION['valid'] = false;
  }

  function db_validate()
  {
    if (session_status() == PHP_SESSION_NONE)
    {
      session_start();
    }
    return (isset($_SESSION["valid"]) && $_SESSION['valid']);
  }

  function gen_top_nav_tab($name, $class)
  {
    return "<a class=".$class.">".$name."</a>";
  }

  function gen_top_nav($userid)
  {
    $end = "</div>";
    $navbar = "<div class='topnav' id='Topnav'>";
    $login = gen_top_nav_tab('Login', 'login');

    $user = new user_info();
    if (!$user->updateOnId($userid))
    {
      return $navbar.$login.$end;
    }
  }

  function gen_card($title, $titlelink, $type, $mid, $link)
  {
    $card = "<div class='card'>
    <a class='title'>".$title."</a>
    <a class='type'>".$type."</a>
    <a class='description'>".$mid."</a>";
    $card = $card."<a class='link' href=".$link.">".$link."</a>
    </div>";
    return $card;
  }

  function gen_univeristy_card($id, $userid)
  {
    $univ = new university_info();
    if (!$univ->updateOnId($id))
    {
      return '';
    }
    $user = new user_info();
    return gen_card($univ->name, $univ->website, 'School', '', $univ->website);
  }

  function gen_rso_card($id)
  {
    $rso = new rso_info();
    if (!$rso->updateOnId($id))
    {
      return '';
    }
    return gen_card($rso->name, '', 'RSO','','');
  }

  function gen_event_card()
  {

  }

  function gen_user_card()
  {

  }
?>
