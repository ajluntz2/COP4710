<?php
require_once('db_table.php');

class rating_info extends database_table
{
  public $id = -1;
  public $one = -1;
  public $two = -1;
  public $three = -1;
  public $four = -1;
  public $five = -1;

  function __construct()
  {
    $this->table = 'events';
  }

  function addRating()
  {
    $insert_query = "
    INSERT INTO ratings(one, two, three, four, five)
    VALUES             (0,0,0,0,0)
    ";

    $this->query($insert_query);
  }

  function updateOnId($id)
  {
    $this->id = $id;
    return $this->update();
  }

  function update()
  {
    $query = "SELECT * FROM ratings WHERE ratingid = ".$this->id;
    $row = $this->simpleQuery($query);
    if ($row !== null)
    {
      $this->updateFields($row);
      return true;
    }
    return false;
  }

  function syncFields()
  {
    $update_query = "
    UPDATE events
    SET
      one = '".$this->one."',
      two = '".$this->two."',
      three = '".$this->three."',
      four = '".$this->four."',
      five = '".$this->five."',
    WHERE
      events.eventid = ".$this->id;

    return $this->query($update_query);
  }

  private function updateFields($row)
  {
    $this->id = $row['eventid'];
    $this->one = $row['one'];
    $this->two = $row['two'];
    $this->three = $row['three'];
    $this->four = $row['four'];
    $this->five = $row['five'];
  }
}
?>
