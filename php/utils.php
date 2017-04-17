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

  function gen_card($title, $cardlink, $type, $mid, $link, $approved=true, $date='', $super=false)
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
    return gen_card($event->name, '../php/eventPage.php?id='.$event->id, 'Event',$event->description,'',$event->approved,$event->startdate);
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

  function gen_rso_approve_list($univid)
  {
    $end = "</div>";
    $slider = "
    <div class='slider'> ";
    $rsos = new rso_info();
    $query = "
    SELECT DISTINCT R.*
    FROM
      rsos AS R
    WHERE
      (
        SELECT COUNT(M.rsoid)
        FROM members AS M
        WHERE M.rsoid = R.rsoid
      ) >= 5 AND
      R.approved = 0 AND
      R.universityid = ".$univid;

    $rsos = $rsos->queryRows($query);
    if ($rsos == null)
    {
      return '';
    }

    foreach ($rsos as &$rso)
    {
      $slider .= "
      <div class='card'>
        <a class='title'>".$rso['name']."</a>
        <form method='POST' action='../php/rsoApprove.php?id=".$rso['rsoid']."'>
          <button class='buttonLogin' type='submit' name='Approve'>Approve</button>
        </form>
        <a class='type'>RSO</a>
        <a class='description'>".$rso['description']."</a>
      </div>
      ";
    }

    return $slider.$end;
  }

    function gen_event_approve_list($univid)
    {
      $end = "</div>";
      $slider = "
      <div class='slider'> ";
      $event = new event_info();
      $query = "
      SELECT DISTINCT E.*
      FROM
        events AS E
      WHERE
        E.approved = 0 AND
        E.universityid = ".$univid;

      $events = $event->queryRows($query);
      if ($events == null)
      {
        return '';
      }

      foreach ($events as &$event)
      {
        $slider .= "
        <div class='card'>
          <a class='title'>".$event['name']."</a>
          <form method='POST' action='../php/eventApprove.php?id=".$event['eventid']."'>
            <button class='buttonLogin' type='submit' name='Approve'>Approve</button>
          </form>
          <a class='type'>Event</a>
          <a class='description'>".$event['description']."</a>
        </div>
        ";
      }

      return $slider.$end;
    }

  function gen_event_slider($userid)
  {
    $event = new event_info();
    $end = "</div>";
    $slider = "
    <div class='slider'> ";

    $query = "
    SELECT
      *
    FROM
      events AS E
    JOIN
      attending AS A
    ON
      E.eventid = A.eventid
    WHERE
      A.userid = ".$userid;

    $events = $event->queryRows($query);
    if($events != null)
    {
        foreach ($events as &$event)
        {
          $slider .= gen_event_card($event['eventid']);
        }
    }

    return $slider.$end;
  }

  function gen_event_slider_on_univ($univid)
  {
    $event = new event_info();
    $end = "</div>";
    $slider = "
    <div class='slider'> ";

    $query = "
    SELECT DISTINCT
      E.*
    FROM
      events AS E, universities AS U
    WHERE
      E.approved = 1 AND
      E.universityid = ".$univid;

    $events = $event->queryRows($query);
    if($events != null)
    {
        foreach ($events as &$event)
        {
          $slider .= gen_event_card($event['eventid']);
        }
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

  function gen_prev_next_buttons($page, $after, $count)
  {
    $prev_num = $after-$count;
    $prev_num = ($prev_num < 0) ? 0 : $prev_num;
    $after_num = $after+$count;
    $after_num = ($after_num < 0) ? 0 : $after_num;

    $prev = "?after=".$prev_num."&limit=".$count;
    $next = "?after=".$after_num."&limit=".$count;

    $nextprev = "
    <div class='nav-buttons'>
      <span class='prevnext'>
        <a class='prev' onclick=\"window.open('".$page.$prev."','_parent')\">< prev</a>
        <a class='next' onclick=\"window.open('".$page.$next."','_parent')\">next ></a>
      </span>
    </div>
    ";
    return $nextprev;
  }

  function gen_event_search_list($search, $prevpage, $after=0, $count=25)
  {
    $tableList = "
    <div class='tableList'>";
    $end = "</div>";

    $tableList = $tableList.gen_search_bar($search, $prevpage, $count);

    $event = new event_info();
    $curruser = new user_info();
    $curruser->updateOnId($_SESSION['userid']);

    $query = "
    SELECT DISTINCT E.* FROM events AS E, rsos AS R, members AS M
    WHERE (
        E.eventid > ".$after." AND (
        E.privacy = 'PUBLIC' OR
        (
          E.privacy = 'PRIVATE' AND
          E.universityid = ".$curruser->universityid."
        ) OR
        (
          E.privacy = 'RSO' AND
          R.rsoid = R.rsoid AND
          R.rsoid = M.rsoid
        )
      ) AND
      E.name LIKE '%".$search."%'
    )
    LIMIT ".$count."";
    $events = $event->queryRows($query);
    if ($events !== null)
    {
      foreach ($events as &$event)
      {
        $tableList = $tableList.gen_event_card($event['eventid']);
      }
    }

    return $tableList.gen_prev_next_buttons($prevpage, $after, $count).$end;
  }

  function gen_rso_search_list($search, $prevpage, $after=0, $count=25)
  {
    $tableList = "
    <div class='tableList'>";
    $end = "</div>";

    $tableList = $tableList.gen_search_bar($search, $prevpage, $count);

    $rso = new rso_info();
    $rsos = $rso->searchAfter('name', $search, 'rsoid', $after, $count);
    if ($rsos !== null)
    {
      foreach ($rsos as &$rso)
      {
        $tableList = $tableList.gen_rso_card($rso['rsoid']);
      }
    }

    return $tableList.gen_prev_next_buttons($prevpage, $after, $count).$end;
  }

  function gen_univeristy_search_list($search, $prevpage, $after=0, $count=25)
  {
    $tableList = "
    <div class='tableList'>";
    $end = "</div>";

    $tableList = $tableList.gen_search_bar($search, $prevpage, $count);

    $univ = new university_info();
    $univs = $univ->searchAfter('name', $search, 'universityid', $after, $count);
    if ($univs !== null)
    {
      foreach ($univs as &$univ)
      {
        $tableList = $tableList.gen_univeristy_card($univ['universityid']);
      }
    }

    return $tableList.gen_prev_next_buttons($prevpage, $after, $count).$end;
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
    $query = "
    SELECT * FROM members
    WHERE
      userid = ".$userid."
    ";

    $rsos = $rso->queryRows($query);
    if ($rsos == null)
    {
      return '';
    }

    $tag = '';
    foreach ($rsos as &$members)
    {
      $rso->updateOnId($members['rsoid']);
      $tag .= "<option value='".$rso->id."'>".$rso->name."</option>";
    }
    return $tag;
  }

  function gen_event_options($userid)
  {
    $event = new event_info();
    // I need to change memberid for the relevent variable
    $events = $event->search('memberid', $userid, -1);

    if ($events == null)
    {
      return '';
    }

    $tag = '';
    foreach ($events as &$event)
    {
      $tag .= "<option value='".$event['eventid']."'>".$event['name']."</option>";
    }
    return $tag;
  }
?>
