<?php
require_once('db_connect.php');

class database_table
{
  public static $db = null;
  public $table = '';

  static function init()
  {
    self::$db = new database_connect();
  }

  public function getDatabase()
  {
    self::$db->connect();
    return self::$db;
  }

  public function query($query)
  {
    return $this->getDatabase()->query($query);
  }

  public function simpleQuery($query)
  {
    $results = $this->getDatabase()->query($query);
    if ($results && mysqli_num_rows($results) > 0)
    {
      return mysqli_fetch_array($results);
    }
    return null;
  }

  public function queryRows($query)
  {
    $results = $this->getDatabase()->query($query);
    if ($results && mysqli_num_rows($results) > 0)
    {
      while ($row_data = mysqli_fetch_array($results))
      {
        $rows[] = $row_data;
      }
      return $rows;
    }
    return null;
  }

  public function getRows($col, $val)
  {
    $query = "SELECT * WHERE $col = '$val' FROM $table";
    $results = $this->getDatabase()->query($query);
    if ($results && mysqli_num_rows($results) > 0)
    {
      while ($row_data = mysqli_fetch_array($results))
      {
        $rows[] = $row_data;
      }
      return $rows;
    }
    return null;
  }

  public function search($row, $search_val)
  {
    $query = "SELECT * FROM ".$this->table." WHERE (".$row." LIKE '%".$search_val."%')";
    return $this->queryRows($query);
  }
}
database_table::init();
?>
