<?php
require_once('db_table.php');

class university_info extends database_table
{
  public $id = -1;
  public $name = '';
  public $website = '';
  public $email = '';
  public $locationid = null;
  public $super = null;

  function __construct()
  {
    $this->table = 'universities';
  }

  public function getUniversities($after, $limit)
  {
    $query = '';
    if ($limit < 0)
    {
      $query = "SELECT * FROM universities ORDER BY universityid";
    }
    else
    {
      $query = "SELECT * FROM (SELECT * FROM universities WHERE universityid > '$after' ORDER BY universityid LIMIT $limit) a ORDER BY universityid";
    }
    $row_data = $this->queryRows($query);
    foreach ($row_data as &$row)
    {
        $univ = new university_info();
        $univ->updateFields($row);

        $rows[] = $univ;
    }
    return $rows;
  }

  function addUniversity($super, $location,
                         $name, $website, $email)
  {
    if (!$super->update() || !$location->update())
    {
      return false;
    }

    if ($super->usertype !== 'SUPER')
    {
      return false;
    }
    $universityid = 0;
    $query = "SELECT MAX(universityid) FROM universities";
    $results = $super->simpleQuery($query);
    if ($results !== null)
    {
      $universityid = $results['MAX(universityid)']+1;
    }

    $query = "INSERT INTO universities(universityid, name, website, email, locationid, super)
              VALUES ('$universityid', '$name', '$website', '$email', '$location->id', '$super->id')";
    $this->query($query);

    $univ = new university_info();
    if (!$univ->updateOnId($universityid))
    {
      return null;
    }
    return $univ;
  }

  function updateOnName($name)
  {
    if (!isset($name))
    {
      return false;
    }

    $query = "SELECT * FROM university WHERE name = '$name'";
    $results = $this->simpleQuery($query);
    if ($results == null)
    {
      return false;
    }
    $this->updateFields($results);
    return true;
  }

  function updateOnId($id)
  {
    $this->id = $id;
    return $this->update();
  }

  function update()
  {
    $query = "SELECT * FROM universities WHERE universityid = $this->id;";
    $results = $this->simpleQuery($query);
    if ($results == null)
    {
      return false;
    }
    $this->updateFields($results);
    return true;
  }

  private function updateFields($row)
  {
    $this->id = $row['universityid'];
    $this->name = $row['name'];
    $this->website = $row['website'];
    $this->email = $row['email'];
    $this->locationid = $row['locationid'];
    $this->super = $row['super'];
  }
}
?>
