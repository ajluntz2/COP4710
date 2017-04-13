<?php
require_once ('config.php');
require_once ('check_session.php');

require_once ('university.php');
require_once ('rso.php');
require_once ('location.php');
require_once ('user.php');

$curruser = new user_info();
$curruser->updateOnId($_SESSION['userid']);

$univ = null;
$location = null;
$search = '';
$after = 0;
$limit = 25;
if ($_SERVER['REQUEST_METHOD'] == "GET")
{
  if (isset($_GET['id']))
  {
    $univ = new university_info();
    if (!$univ->updateOnId($_GET['id']))
    {
      $univ = null;
    }
    else
    {
      $location = new location_info();
      if (!$location->updateOnId($univ->locationid))
      {
        $location = null;
      }
    }
  }
  if (isset($_GET['search']))
  {
    $search = $_GET['search'];
  }
  if (isset($_GET['after']))
  {
    $after = $_GET['after'];
  }
  if (isset($_GET['limit']))
  {
    $limit = $_GET['limit'];
  }
}
?>

<html lang = "en">

   <head>
    <title>COP4710 -
    <?php if ($univ !== null) { echo $univ->name; }
          else { echo "Schools"; } ?>
    </title>

    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Places Searchbox</title>
    <link href="../css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">

    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 50%;
        width: 70%;
        margin: 0 auto;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      #description {
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
      }

      #infowindow-content .title {
        font-weight: bold;
      }

      #infowindow-content {
        display: none;
      }

      #map #infowindow-content {
        display: inline;
      }

      .pac-card {
        margin: 10px 10px 0 0;
        border-radius: 2px 0 0 2px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        outline: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        background-color: #fff;
        font-family: Roboto;
      }

      #pac-container {
        padding-bottom: 12px;
        margin-right: 12px;
      }

      .pac-controls {
        display: inline-block;
        padding: 5px 11px;
      }

      .pac-controls label {
        font-family: Roboto;
        font-size: 13px;
        font-weight: 300;
      }

      #pac-input {
        background-color: #fff;
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
        margin-left: 12px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 400px;
      }

      #pac-input:focus {
        border-color: #4d90fe;
      }

      #title {
        color: #fff;
        background-color: #4d90fe;
        font-size: 25px;
        font-weight: 500;
        padding: 6px 12px;
      }
      #target {
        width: 345px;
      }

      .container {
      	display: block;
      	margin: 0 auto;
      }
    </style>

   </head>

   <body>
     <script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
       <?php echo gen_top_nav($curruser->id, 'Schools'); ?>

       <?php if ($univ !== null) { ?>
         <!-- this is the container where the individual page for the University comes up -->

         <div class="container" style="width:50%; margin:0 auto;">
           <h1><?php echo $univ->name; ?></h1>

           <div class="container" style="background-color:#f1f1f1; width:100%;">
             <button class = "edit" type="submit" name="edit">Edit</button>
             <br>
             <button class = "buttonLogin" type="submit" name="join">Join</button>
           </div>

           <br>
           <?php echo "<label><b>Website: </b></label>" ; echo $univ->website; ?>
           <br>

           <?php echo "<label><b>Email: </b></label>"; echo $univ->email; ?>
           <br>
           <br>

 <!-- columns divs, float left, no margin so there is no space between column, width=1/3 -->
    <div id="column1" style="float:left; margin:0; width:50%;">
     <p><?php echo $univ->description; ?></p>
    </div>

    <div id="column2" style="float:left; margin:0; width:50%;">
      <div id="map" style="height: 50%; width:100%;"></div>
    </div>


         </div>
       <?php } else { ?>
         <?php
           echo gen_univeristy_search_list($search, '../php/universityPage.php', $after, $limit);
         ?>
       <?php } ?>



       <script>
         // This example adds a search box to a map, using the Google Place Autocomplete
         // feature. People can enter geographical searches. The search box will return a
         // pick list containing a mix of places and predicted search terms.

         // This example requires the Places library. Include the libraries=places
         // parameter when you first load the API. For example:
         // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

         function initAutocomplete() {
           var latitude = <?php echo $location->latitude; ?>;
           var longitude = <?php echo $location->longitude; ?>;
           var map = new google.maps.Map(document.getElementById('map'), {
             center: {lat:latitude, lng:longitude},
             zoom: 15,
             mapTypeId: 'roadmap'
           });

           marker = new google.maps.Marker({
            map: map,
            draggable: true,
            animation: google.maps.Animation.DROP,
            position: new google.maps.LatLng(latitude, longitude)
          });
          marker.addListener('click', toggleBounce);
         }
       </script>
       <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDSVinGJtXybbaYrT5XLRQhSwY9x3Tbxfo&libraries=places&callback=initAutocomplete"
            async defer></script>

   </body>
</html>
