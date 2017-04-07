<?php
  require_once('config.php');

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

      $_SESSION['valid'] = true;
      $_SESSION['timeout'] = time();
      $_SESSION['userid'] = $row['userid'];
      $_SESSION['email'] = $email;
      $_SESSION['usertype'] = $row['usertype'];
      $_SESSION['universityid'] = $row['universityid'];
      $_SESSION['first'] = $row['first'];
      $_SESSION['last'] = $row['last'];

      return true;
    }
    else
    {
      $err = 'invalid email or password';
      return false;
    }
  }

  function db_add_user($db, $first, $last, $email, $password, $university, &$err)
  {
      if (!isset($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
      {
        $err = 'bad email';
        return false;
      }

      $db = db_ensure($db);

      $universityid = 'NULL';
      if (isset($university) && !empty(trim($university)))
      {
        if (verify_university_on_db($db, $university, $universityid, $errMsg))
        {
          $err = $university.' does not exist in database';
          return false;
        }
      }

      if (!verify_email_not_on_db($db, $email, $err))
      {
        return false;
      }

      $userid = 0;
      $max_userid_query = "SELECT MAX(userid) FROM users";
      $results = $db->query($max_userid_query);
      if (mysqli_num_rows($results) > 0)
      {
        $row = mysqli_fetch_array($results);
        $userid = $row['MAX(userid)']+1;
      }

      //INSERT INTO 'users' ('userid', 'password', 'usertype', 'universityid', 'rsoid', 'first', 'last', 'email') VALUES ('', '', '', NULL, NULL, '', '', '')

      $hashedPass = md5($password);
      $insert_query = "INSERT INTO users(userid, password, usertype, universityid, rsoid, first, last, email) VALUES ('$userid', '$hashedPass', 'USER', '$universityid', NULL, '$first', '$last', '$email')";
      $db->query($insert_query);

      if (verify_email_on_db  ($db, $email, $err))
      {
        return true;
      }
      else
      {
        $err = 'user failed to insert into database';
        return false;
      }
      return false;
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
      unset($_SESSION['timeout']     );
      unset($_SESSION['userid']      );
      unset($_SESSION['email']       );
      unset($_SESSION['password']    );
      unset($_SESSION['usertype']    );
      unset($_SESSION['universityid']);
      unset($_SESSION['first']       );
      unset($_SESSION['last']        );
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
?>
