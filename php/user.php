<?php
require_once('db_table.php');

class user_info extends database_table
{
  public $id = -1;
  private $password = '';
  public $usertype = '';
  public $universityid = -1;
  public $rsoid = -1;
  public $first = '';
  public $last = '';
  public $email = '';

  function __construct()
  {
    $this->table = 'users';
  }

  public static function userByEmail($email)
  {
    $uinfo = new user_info();
    $uinfo->updateOnEmail($email);
    return $uinfo;
  }

  public static function userById($id)
  {
    $uinfo = new user_info();
    $uinfo->updateOnId($id);
    return $uinfo;
  }

  function addUser($first, $last, $email, $password, $universityid)
  {
    $reguser = new user_info();
    if ($reguser->updateOnEmail($email))
    {
      return null;
    }

    $hashedPass = md5($password);
    $insert_query = "INSERT INTO users(password, usertype, universityid, rsoid, first, last, email)
                     VALUES ('$hashedPass', 'USER', ".$universityid.", NULL, '$first', '$last', '$email')";

    $this->query($insert_query);

    if (!$reguser->updateOnEmail($email))
    {
      return null;
    }
    return $reguser;
  }

  function updateOnEmail($email)
  {
    if (!isset($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
    {
      return false;
    }

    $query = "SELECT * FROM users WHERE email = '$email'";
    $row = $this->simpleQuery($query);
    if ($row !== null)
    {
      $this->updateFields($row);
      return true;
    }
    return false;
  }

  function updateOnId($id)
  {
    $this->id = $id;
    return $this->update();
  }

  function name()
  {
    return $this->first.' '.$this->last;
  }

  function update()
  {
    $query = "SELECT * FROM users WHERE userid = ".$this->id;
    $row = $this->simpleQuery($query);
    if ($row !== null)
    {
      $this->updateFields($row);
      return true;
    }
    return false;
  }

  function syncFields($pass)
  {
    if (!$this->check($pass))
    {
      $this->update();
      return false;
    }

    $update_query = "UPDATE users SET first = '$this->first', last = '$this->last', universityid = ".$this->universityid.", rsoid = ".$this->rsoid.", email = '$this->email' WHERE 'users'.'userid' = ".$this->id;
    return $this->query($update_query);
  }

  function changePassword($current, $new)
  {
    if (!$this->check($current))
    {
      $this->update();
      return false;
    }

    $new = md5($new);
    $update_query = "UPDATE users SET password = '$new' WHERE 'users'.'userid' = ".$this->id;
    $ret = $this->query($update_query);
    $this->update();
    return $ret;
  }

  function check($pass)
  {
    if (!isset($pass) || empty($pass))
    {
      return false;
    }

    return ($this->password == md5($pass));
  }

  private function updateFields($row)
  {
    $this->id = $row['userid'];
    $this->password = $row['password'];
    $this->usertype = $row['usertype'];
    $this->universityid = $row['universityid'];
    $this->rsoid = $row['rsoid'];
    $this->first = $row['first'];
    $this->last = $row['last'];
    $this->email = $row['email'];
  }
}
?>
