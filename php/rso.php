<?php
require_once('db_connect.php');

class rso_info
{
  public $db = null;

  public $id = -1;
  public $adminid = -1;
  public $universityid = -1;
  public $approved = -1;
  public $memberid = -1;

  function __construct($rsoid)
  {
    $this->id = $rsoid;
    $this->db = db_ensure($this->db);

    $this->update();
  }

  function __destruct()
  {
    $this->db = null;
  }

  function update()
  {
    $this->db = db_ensure($this->db);

    $query = "SELECT * FROM rsos WHERE rsoid = '$this->id';";
    $results = $this->db->query($query);
    if (mysqli_num_rows($results) > 0)
    {
      $row = mysqli_fetch_array($results);
      $this->updateFields($row);
      return true;
    }
    return false;
  }

  private function updateFields($row)
  {
        $this->adminid = $row['adminid'];
        $this->universityid = $row['universityid'];
        $this->approved = $row['approved'];
        $this->memberid = $row['memberid'];
  }
}
?>
