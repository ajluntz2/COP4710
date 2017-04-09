<?php
require_once('db_table.php');

class rso_info extends database_table
{
  public $id = -1;
  public $name = '';
  public $adminid = -1;
  public $universityid = -1;
  public $approved = -1;
  public $memberid = -1;

  function __construct()
  {
    $this->table = 'rsos';
  }

  function __destruct()
  {
  }

  function updateOnId($id)
  {
    $this->id = $id;
    return $this->update();
  }

  function updateOnName($name)
  {
    $query = "SELECT * FROM rsos WHERE name = '$name'";
    $row = $this->simpleQuery($query);
    if ($row !== null)
    {
      $this->updateFields($row);
      return true;
    }
    return false;
  }

  function addRSO($adminid, $univid,
                  $name)
  {
    $admin = new user_info();
    if (!$admin->updateOnId($adminid))
    {
      return null;
    }
    $univ = null;
    if ($univid > -1)
    {
      $univ = new university_info();
      if (!$univ->updateOnId($univid))
      {
        return null;
      }
    }

    $rsoid = 0;
    $maxid = $this->simpleQuery("SELECT MAX(rsoid) FROM rsos");
    if ($maxid !== null)
    {
      $rsoid = $maxid['MAX(rsoid)'] + 1;
    }

    $insert_query;
    if ($univ == null)
    {
      $insert_query = "
      INSERT INTO rsos(rsoid, name)
      VALUES (".$rsoid.", '".$name."')";
    }
    else
    {
      $insert_query = "
      INSERT INTO rsos(rsoid, name, universityid)
      VALUES (".$rsoid.", '".$name."', ".$univid.")";
    }

    $this->query($insert_query);
    $regrso = new rso_info();
    if (!$regrso->updateOnId($rsoid))
    {
      return $regrso;
    }
    return null;
  }

  function getOnMember($userid)
  {
    $query = "SELECT * FROM rsos WHERE memberid = ".$userid;
    return $this->queryRows($query);
  }

  function update()
  {
    $query = "SELECT * FROM rsos WHERE rsoid = ".$this->id;
    $results = $this->simpleQuery($query);
    if ($results !== null)
    {
      $this->updateFields($results);
      return true;
    }
    return false;
  }

  private function updateFields($row)
  {
    $this->adminid = $row['adminid'];
    $this->name = $row['name'];
    $this->universityid = $row['universityid'];
    $this->approved = $row['approved'];
    $this->memberid = $row['memberid'];
  }
}
?>
