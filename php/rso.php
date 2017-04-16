<?php
require_once('db_table.php');

class rso_info extends database_table
{
  public $id = -1;
  public $name = '';
  public $adminid = -1;
  public $universityid = -1;
  public $approved = -1;
  public $description = null;

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
                  $name, $description='')
  {
    $admin = new user_info();
    $univ = new university_info();
    if (!$admin->updateOnId($adminid) || !$univ->updateOnId($univid))
    {
      return null;
    }
    $insert_query = "
    INSERT INTO rsos(name, universityid, description, adminid)
    VALUES ('".$name."', ".$univid.", '".$description."', ".$adminid.")";

    $this->query($insert_query);
    $regrso = new rso_info();
    if (!$regrso->updateOnName($name))
    {
      $update = "
        INSERT INTO members
          (rsoid, userid)
        VALUES
          (".$regrso->id.", ".$adminid.")";
      $regrso->query($update);

      return $regrso;
    }
    return null;
  }

  function getOnMember($userid)
  {
    $query = "SELECT R
    FROM rsos AS R
    JOIN members AS M
    ON R.rsoid = M.rsoid
    WHERE M.userid = $userid";
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
    $this->description = $row['description'];
  }
}
?>
