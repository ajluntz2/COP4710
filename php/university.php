<?php
require_once('db_table.php');
require_once('user.php');
require_once('location.php');

class university_info extends database_table
{
  public $id = -1;
  public $name = '';
  public $website = '';
  public $email = '';
  public $locationid = null;
  public $super = null;
  public $description = null;

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
      $query = "SELECT * FROM (SELECT * FROM universities WHERE universityid > ".$after." ORDER BY universityid LIMIT ".$limit.") a ORDER BY universityid";
    }
    $row_data = $this->queryRows($query);
    if( !is_array( $row_data ) && !$row_data instanceof Traversable )
    {
      $univ = new university_info();
      $univ->updateFields($row_data);

      $rows[] = $univ;
    }
    else
    {
      foreach ($row_data as &$row)
      {
          $univ = new university_info();
          $univ->updateFields($row);

          $rows[] = $univ;
      }
    }
    return $rows;
  }

  function addUniversity($superid, $locationid,
                         $name, $website, $email, $description='')
  {
    $des = mysqli_real_escape_string($this->getDatabase()->_con,$description);

    $query = "INSERT INTO universities(universityid, name, website, email, locationid, super, description)
              VALUES (NULL, '".$name."', '".$website."', '".$email."', ".$locationid.", ".$superid.", '".$des."')";
    $this->query($query);

    $univ = new university_info();
    if (!$univ->updateOnName($name))
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
    $query = "SELECT * FROM universities WHERE universityid = ".$this->id;
    $results = $this->simpleQuery($query);
    if ($results == null)
    {
      return false;
    }
    $this->updateFields($results);
    return true;
  }

  function syncFields()
  {
    $des = mysqli_real_escape_string($this->getDatabase()->_con,$this->description);

    if ($this->email !== '')
    {
      $update_query = "UPDATE universities SET name = '".$this->name."', website = '".$this->website."', email = '".$this->email."', description = \"".$des."\" WHERE universities.universityid = ".$this->id;
    }
    else
    {
      $update_query = "UPDATE universities SET name = '".$this->name."', website = '".$this->website."', description = \"".$des."\" WHERE universities.universityid = ".$this->id;
    }
    return $this->query($update_query);
    // return $update_query;
  }

  private function updateFields($row)
  {
    $this->id = $row['universityid'];
    $this->name = $row['name'];
    $this->website = $row['website'];
    $this->email = $row['email'];
    $this->locationid = $row['locationid'];
    $this->super = $row['super'];
    $this->description = $row['description'];
  }
}
?>
