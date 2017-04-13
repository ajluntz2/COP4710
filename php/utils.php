<?php
  require_once('config.php');
  require_once('university.php');
  require_once('event.php');

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
    $navbar = $navbar.gen_top_nav_tab('Schools', '', '../php/universityPage.php',$activate);
    $navbar = $navbar.gen_top_nav_tab('RSOs', '', '../php/rsoPage.php',$activate);
    $navbar = $navbar.gen_top_nav_tab('Events', '', '../php/eventPage.php',$activate);
    $navbar = $navbar.gen_top_nav_tab('Create Event', '', '../php/registerEvent.php',$activate);

    $user = new user_info();
    $good_user = $user->updateOnId($userid);

    $univ = new university_info();
    if ($univ->updateOnId($user->universityid))
    {
      $navbar = $navbar.gen_top_nav_tab('New RSO', '', '../php/registerRSO.php',$activate);
    }

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

  function gen_card($title, $cardlink, $type, $mid, $link, $approved=true, $date='')
  {
    $card = "<div class='card' onclick=\"window.open('".$cardlink."', '_parent')\">";

    if (!$approved)
    {
      $card .= "
      <div class='tooltip'>
      <a class='approve'>!</a>
      <span class='tooltiptext'>Not approved!</span>
      </div>";
    }

    $card .= "
    <a class='title'>".$title."</a>
    <a class='type'>".$type."</a>
    <a class='description'>".$mid."</a>";

    if ($date !== '')
    {
      $card .= "<a class='date'>".$date."</a>";
    }
    if ($link !== '')
    {
      $card .= "<a class='link' href='".$link."'>".$link."</a>";
    }

    $card .= "</div>";
    return $card;
  }

  function gen_univeristy_card($id, $userid=-1)
  {
    $univ = new university_info();
    if (!$univ->updateOnId($id))
    {
      return '';
    }
    return gen_card($univ->name, '../php/universityPage.php?id='.$univ->id, 'School', $univ->description, $univ->website);
  }

  function gen_rso_card($id)
  {
    $rso = new rso_info();
    if (!$rso->updateOnId($id))
    {
      return '';
    }
    return gen_card($rso->name, '../php/rsoPage.php?id='.$rso->id, 'RSO',$rso->description,'', $rso->approved);
  }

  function gen_event_card($id)
  {
    $event = new event_info();
    if (!$event->updateOnId($id))
    {
      return '';
    }
    return gen_card($event->name, '../php/eventPage.php?id='.$event->id, 'RSO',$event->description,'',$event->approved,$event->startdate);
  }

  function gen_user_card($id)
  {
    $user = new user_info();
    if (!$user->updateOnId($id))
    {
      return '';
    }
    // return gen_card($event->name, '../php/evetPage.php?id='.$event->id, 'RSO',$event->description,'');
    return '';
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
    $event = new event_info();
    $end = "</div>";
    $slider = "
    <div class='slider'> ";

    $events = $event->search('attending', $userid);
    foreach ($events as &$event)
    {
      $slider .= gen_event_card($event['eventid']);
    }

    return $slider.$end;
  }

  function gen_search_bar($search, $prevpage, $num=25)
  {
    $bar = "<form method='get' action=".$prevpage.">";
    $bar = $bar."<div class='box'>";
    $bar = $bar."<div class='searchBar'>";
    if ($search=='')
    {
      $bar = $bar."<input id='search' type='search' name='search' placeholder='Search...' />";
    }
    else
    {
      $bar = $bar."<input id='search' type='search' name='search' placeholder='Search...' value=".$search." />";
    }
    $bar = $bar."<button class='icon' type='submit'>GO</button>";
    $bar = $bar."</div>";

    $bar = $bar."<select name='limit'>";

    if ($num % 5 !== 0)
    {
      $bar = $bar."<option id='option".$num."' value='".$num."' selected='selected' >".$num."</option>";
    }
    for ($i = 1; $i <= 5; ++$i)
    {
      $cur = 5*$i;
      $bar = $bar."<option id='option".$cur."' value='".$cur."' ".($cur==$num?"selected='selected'":"")." >".$cur."</option>";
    }

    $bar = $bar."</select>";
    $bar = $bar."</div>";
    $bar = $bar."</form>";
    return $bar;
  }

  function gen_event_search_list($search, $prevpage, $after=0, $count=25)
  {
    $tableList = "
    <div class='tableList'>";
    $end = "</div>";

    $tableList = $tableList.gen_search_bar($search, $prevpage, $count);

    $event = new event_info();
    $events = $event->searchAfter('name', $search, 'eventid', $after, $count);
    if ($events == null)
    {
      return $tableList.$end;
    }
    foreach ($events as &$event)
    {
      $tableList = $tableList.gen_event_card($event['eventid']);
    }

    return $tableList.$end;
  }

  function gen_rso_search_list($search, $prevpage, $after=0, $count=25)
  {
    $tableList = "
    <div class='tableList'>";
    $end = "</div>";

    $tableList = $tableList.gen_search_bar($search, $prevpage, $count);

    $rso = new rso_info();
    $rsos = $rso->searchAfter('name', $search, 'rsoid', $after, $count);
    if ($rsos == null)
    {
      return $tableList.$end;
    }
    foreach ($rsos as &$rso)
    {
      $tableList = $tableList.gen_rso_card($rso['rsoid']);
    }

    return $tableList.$end;
  }

  function gen_univeristy_search_list($search, $prevpage, $after=0, $count=25)
  {
    $tableList = "
    <div class='tableList'>";
    $end = "</div>";

    $tableList = $tableList.gen_search_bar($search, $prevpage, $count);

    $univ = new university_info();
    $univs = $univ->searchAfter('name', $search, 'universityid', $after, $count);
    if ($univs == null)
    {
      return $tableList.$end;
    }
    foreach ($univs as &$univ)
    {
      $tableList = $tableList.gen_univeristy_card($univ['universityid']);
    }

    return $tableList.$end;
  }

  function gen_univeristy_options($userid)
  {
    $user = new user_info();
    if (!$user->updateOnId($userid))
    {
      return '';
    }

    $tag = '';
    if( !is_array( $user->universityid ) && !$user->universityid instanceof Traversable )
    {
      $univ = new university_info();
      if ($univ->updateOnId($user->universityid))
      {
        $tag = $tag."<option value='".$univ->id."'>".$univ->name."</option>";
      }
    }
    else
    {
      $univ = new university_info();
      foreach ($user->universityid as &$univid)
      {
        $univ->updateOnId($univ->id);
        if ($univ->updateOnId($user->universityid))
        {
          $tag = $tag."<option value='".$univ->id."'>".$univ->name."</option>";
        }
      }
    }

    return $tag;
  }

  function gen_rso_options($userid)
  {
    $rso = new rso_info();
    $rsos = $rso->search('memberid', $userid, -1);

    if ($rsos == null)
    {
      return '';
    }

    $tag = '';
    foreach ($rsos as &$rso)
    {
      $tag .= "<option value='".$rso['rsoid']."'>".$rso['name']."</option>";
    }
    return $tag;
  }
?>
