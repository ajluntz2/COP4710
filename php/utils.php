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

  function gen_top_nav_tab($name, $class, $link, $active='')
  {
    if ($active == $name)
    {
      return "<a class='active' href='".$link."'>".$name."</a>";
    }
    else
    {
      return "<a class='".$class."' href='".$link."'>".$name."</a>";
    }
  }

  function gen_top_nav($userid, $activate='')
  {
    $end = "</div>";
    $navbar = "
    <div class='topnav' id='Topnav'>";

    $login = gen_top_nav_tab('Login', 'login', '../php/login.php');
    $createUniv = gen_top_nav_tab('New School', '', '../php/registerUniversity.php',$activate);

    $navbar = $navbar.gen_top_nav_tab('Home', '', '../php/dashboard.php',$activate);
    $navbar = $navbar.gen_top_nav_tab('Schools', '', '',$activate);
    $navbar = $navbar.gen_top_nav_tab('RSOs', '', '../php/rsoPage.php',$activate);
    $navbar = $navbar.gen_top_nav_tab('Events', '', '',$activate);

    $navbar = $navbar.gen_top_nav_tab('New RSO', '', '../php/registerRSO.php',$activate);

    $user = new user_info();
    $good_user = $user->updateOnId($userid);

    if ($user->usertype == 'SUPER')
    {
       $navbar = $navbar.$createUniv;
    }

    if (!$good_user)
    {
      $navbar = $navbar.$login;
    }
    else
    {
      $navbar = $navbar.gen_top_nav_tab('Logout', 'logout', '../php/logout.php');
    }
    return $navbar.$end;
  }

  function gen_card($title, $cardlink, $type, $mid, $link)
  {
    $card = "<div class='card' onclick=\"window.open('".$cardlink."', '_parent')\">
    <a class='title'>".$title."</a>
    <a class='type'>".$type."</a>
    <a class='description'>".$mid."</a>";
    $card = $card."<a class='link' href='".$link."'>".$link."</a>
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
    return gen_card($univ->name, '', 'School', $univ->description, $univ->website);
  }

  function gen_rso_card($id)
  {
    $rso = new rso_info();
    if (!$rso->updateOnId($id))
    {
      return '';
    }
    return gen_card($rso->name, '', 'RSO',$rso->description,'');
  }

  function gen_event_card()
  {

  }

  function gen_user_card()
  {

  }

  function gen_univeristy_slider($userid)
  {
    $user = new user_info();
    if (!$user->updateOnId($userid))
    {
      return '';
    }

    $end = "</div>";
    $slider = "
    <div class='slider'> ";
    if( !is_array( $user->universityid ) && !$user->universityid instanceof Traversable )
    {
      $slider = $slider.gen_univeristy_card($user->universityid, $userid);
    }
    else
    {
      foreach ($user->universityid as &$univid)
      {
        $slider = $slider.gen_univeristy_card($univid, $userid);
      }
    }

    return $slider.$end;
  }

  function gen_rso_slider($userid)
  {
    $user = new user_info();
    if (!$user->updateOnId($userid))
    {
      return '';
    }

    $end = "</div>";
    $slider = "
    <div class='slider'> ";
    $rsos = new rso_info();
    $rsos = $rsos->getOnMember($userid);

    if( !is_array( $rsos ) && !$rsos instanceof Traversable )
    {
      $slider = $slider.gen_rso_card($rsos['rsoid']);
    }
    else
    {
      foreach ($rsos as &$rso)
      {
        $slider = $slider.gen_rso_card($rso['rsoid']);
      }
    }

    return $slider.$end;
  }

  function gen_event_slider($userid)
  {
    $user = new user_info();
    if (!$user->updateOnId($userid))
    {
      return '';
    }
    $end = "</div>";
    $slider = "
    <div class='slider'> ";

    return $slider.$end;
  }

  function gen_univeristy_table_list($first=1, $amount=5)
  {
    $tableList = "
    <div class='tableList'>
    <iframe src='../HTML/searchFrame.html'> </iframe>";

    $univ = new university_info();
    $univs = $univ->getUniversities($first, $amount);
    foreach ($univs as &$univ)
    {
      $tableList = $tableList.gen_card($univ->name, $univ->website, 'School', '', $univ->website);
    }

    $tableList = $tableList."
    </div>
    ";

    return $tableList;
  }
?>
