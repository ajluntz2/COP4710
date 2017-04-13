<?php
require_once('db_table.php');

class event_info extends database_table
{
  public $id = -1;
  public $name = '';
  public $adminid = -1;
  public $locationid = -1;
  public $rsoid = -1;
  public $universityid = -1;
  public $ratingid = -1;
  public $catigory = '';
  public $description = '';
  public $startdate = '';
  public $time = '';
  public $length = '';
  public $days = '';
  public $enddate = '';
  public $frequency = '';

  public $email = '';
  public $phone = -1;

  public $approved = false;

  function __construct()
  {
    $this->table = 'events';
  }

  function addEvent($userid, $universityid, $locationid,
                    $rsoid, $ratingid, $category,
                    $description, $startdate, $enddate,
                    $days, $time, $length,
                    $frequency,
                    $email, $phone)
  {
    $insert_query = "
    INSERT INTO events(  name   ,    adminid  ,    locationid  ,    rsoid  ,    universityid  ,    ratingid  ,
                         category  ,    description  ,    startdate  ,    time  ,    length  ,    days  ,
                         enddate  ,    frequency   ,    email  ,    phone)
    VALUES            (".$name.", ".$adminid.", ".$locationid.", ".$rsoid.", ".$universityid.", ".$ratingid.",
                       ".$category.", ".$description.", ".$startdate.", ".$time.", ".$length."), ".$days.",
                       ".$enddate.", ".$frequency.", ".$email.", ".$phone.")
    ";

    $this->query($insert_query);

    $event = new event_info();
    if (!$event->updateOnName($name))
    {
      return null;
    }
    return $event;
  }

  function updateOnName($name)
  {
    $query = "SELECT * FROM events WHERE name = ".$name;
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

  function update()
  {
    $query = "SELECT * FROM events WHERE eventid = ".$this->id;
    $row = $this->simpleQuery($query);
    if ($row !== null)
    {
      $this->updateFields($row);
      return true;
    }
    return false;
  }

  private function updateFields($row)
  {
    $this->id = $row['eventid'];
    $this->name = $row['name'];
    $this->adminid = $row['adminid'];
    $this->locationid = $row['locationid'];
    $this->rsoid = $row['rsoid'];
    $this->universityid = $row['universityid'];
    $this->ratingid = $row['ratingid'];
    $this->catigory = $row['catigory'];
    $this->description = $row['description'];
    $this->startdate = $row['startdate'];
    $this->time = $row['time'];
    $this->length = $row['length'];
    $this->days = $row['days'];
    $this->enddate = $row['enddate'];
    $this->frequency = $row['frequency'];
    $this->email = $row['email'];
    $this->phone = $row['phone'];
    $this->approved = $row['approved'];
  }
}
?>
