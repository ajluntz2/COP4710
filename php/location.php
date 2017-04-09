<?php
require_once('db_table.php');

class location_info extends database_table
{
  public $id = -1;
  public $address = '';
  public $latitude = 91.0;
  public $longitude = 181.0;

  function __construct()
  {
    $this->table = 'locations';
  }

  function addLocation($address, $lat, $lon)
  {
    $locationid = 0;
    $query = "SELECT MAX(locationid) FROM locations";
    $results = $this->simpleQuery($query);
    if ($results !== null)
    {
      $locationid = $results['MAX(locationid)']+1;
    }

    $query = "INSERT INTO locations(locationid, address, latitude, longitude) VALUES ('$locationid', '$address', '$lat', '$lon')";
    $this->query($query);

    $locationinfo = new location_info();
    if (!$locationinfo->update())
    {
      return null;
    }
    return $locationinfo;
  }

  function updateOnId($id)
  {
    $this->id = $id;
    return $this->update();
  }

  function update()
  {
    $query = "SELECT * FROM locations WHERE locationid = '$this->id';";
    $row = $this->simpleQuery($query);
    if ($row !== null)
    {
      $this->address = $row['address'];
      $this->latitude = $row['latitude'];
      $this->longitude = $row['longitude'];

      return true;
    }
    return false;
  }
}
?>
