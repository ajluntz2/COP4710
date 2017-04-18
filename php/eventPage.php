<?php
require_once ('config.php');
require_once ('check_session.php');

require_once ('university.php');
require_once ('rso.php');
require_once ('location.php');
require_once ('user.php');

$curruser = new user_info();
$curruser->updateOnId($_SESSION['userid']);

$event = null;
$search = '';
$after = 0;
$limit = 25;
$editing = false;

$attending = false;
$rating = 0.0;
$location = null;

if (isset($_GET['id']))
{
    $event = new event_info();
    if (!$event->updateOnId($_GET['id']))
    {
      $event = null;
    }
    else
    {
      $query = "
      SELECT DISTINCT
        *
      FROM
        attending AS A
      WHERE
        A.eventid = ".$event->id." AND
        A.userid = ".$curruser->id;

      $events = $event->queryRows($query);
      if ($events !== null)
      {
        $attending = true;
      }

      $query = "
      SELECT *
      FROM
        ratings
      WHERE
        ratingid = ".$event->ratingid;
      $r = $event->simpleQuery($query);
      if ($r !== null)
      {
        $sum = $r['one']+$r['two']+$r['three']+$r['four']+$r['five'];

        if ($sum > 0)
        {
          $rating = $r['one']/$sum+
                    2.0*($r['two']/$sum)+
                    3.0*($r['three']/$sum)+
                    4.0*($r['four']/$sum)+
                    5.0*($r['five']/$sum);
        }
      }


      $location = new location_info();
      $location->updateOnId($event->locationid);
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

if (isset($_GET['edit']) && $_GET['edit'] == 1 && $event !== null && $event->adminid == $curruser->id)
{
    $editing = true;
}

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
    if ($event !== null)
    {
      $event->name = $_POST['name'];
      // $event->category = $POST['category'];
      $event->description = $_POST['description'];
      // $event->startdate = $_POST['startdate'];
      // $event->time = $_POST['time'];
      // $event->length = $_POST['length'];
      // $event->days = $_POST['days'];
      // $event->enddate = $_POST['enddate'];
      // $event->frequency = $_POST['frequency'];
      $event->website = $_POST['website'];
      $event->email = $_POST['email'];
      // $event->phone = $_POST['phone'];
      // $event->approved = $_POST['approved'];

      $event->syncFields();
    }
}

?>

<html lang = "en">

   <head>
    <title>COP4710 -
    <?php if ($event !== null) { echo $event->name; }
          else { echo "Events"; } ?>
    </title>
    <link href="../css/style.css" rel="stylesheet">

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
       <?php echo gen_top_nav($curruser->id, 'Events'); ?>

       <?php if ($event !== null) { ?>
         <div class="container">

           <!-- this is the container where the individual page for the University comes up -->

           <div class="container" style="width:50%; margin:0 auto;">
             <h1><?php echo $event->name; ?></h1>

             <?php if ($editing) { ?>
                 <form class = "form-register" role = "form" action = "../php/eventPage.php?id=<?php echo $event->id; ?>" method = "post">

                 <div class="container" style="width:100%; display:block;">
                     <button class = "buttonEdit" type="submit" name="save">Save</button>
                 </div>

                 <div style="display:block;">
                     <div id="column1" style="float:left; margin:0; width:50%;">

                         <h5 style="display: inline;">Name:</h5>
                         <input type="text" class="form-control" name = "name" value="<?php echo $univ->name; ?>" required autofocus ></br>

                         <br>
                         <h5 style="display: inline;">Website:</h5>
                         <input type="text" class="form-control" name = "website" value="<?php echo $univ->website; ?>" required></br>
                         <br>

                         <h5 style="display: inline;">Email:</h5>
                         <input type="text" class="form-control" name = "email" value="<?php echo $univ->email; ?>"></br>
                         <br>

                         <h5 style="display: inline;">Address:</h5>
                         <input type="text" class="form-control" name = "address" value="<?php echo $location->updateOnId($univ->locationid); ?>"></br>
                         <br>

                         <h5 style="display: inline;">Description:</h5>
                         <textarea type="text" class="form-control" name = "description" required><?php echo $univ->description; ?></textarea>
                         <br>

                     </div>

                   <div id="column2" style="float:left; margin:0; width:50%;">
                       <div id="map" style="height: 50%; width:100%;"></div>
                   </div>
               </div>



             </form>
             <?php } else { ?>

             <div class="container" style="width:100%; display:block;">

               <?php if ($event->adminid == $curruser->id){ ?>
               <button class = "buttonEdit" type="submit" name="edit" onclick="window.open('<?php echo "../php/eventPage.php?id=".$event->id."&edit=1"; ?>', '_parent')">Edit</button>
               <?php } ?>

               <?php if ($attending) { ?>

                 <form action='../php/eventJoin.php?attend=0&id=<?php echo $event->id; ?>' method="POST">
                   <button class = "buttonLogin" value="unattend" type="submit" name="unattend" onclick="window.open('<?php echo "../php/eventPage.php?id=".$event->id ?>', '_parent')">Unattend</button>
                 </form>
               <?php } else { ?>

               <form action='../php/eventJoin.php?attend=1&id=<?php echo $event->id; ?>' method="POST">
                 <button class = "buttonLogin" value="attend" type="submit" name="attend">Attend</button>
               </form>

              <?php } ?>

              </div>

             <?php echo "<label><b>Email: </b></label>"; echo $event->email; ?>
             <br>
             <br>

             <!-- columns divs, float left, no margin so there is no space between column, width=1/3 -->
              <div style="display:block;">
                <div id="column1" style="float:left; margin:0; width:50%;">
                 <p><?php echo $event->description; ?></p>
                </div>

                <div id="column2" style="float:left; margin:0; width:50%;">
                  <div id="map" style="height: 50%; width:100%;"></div>
                </div>
              </div>
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

              <form method='POST' action="../php/eventRate.php?id=<?php echo $event->id; ?>">
                <h5>Rating: <?php echo $rating; ?></h5>
                <span class="rating">
                  <input type="radio" name="rating" value="0" <?php if (0-1 < $rating && $rating <= 0) { echo "checked"; } ?> >
                  <label for="rating0">0</label>
                  <input type="radio" name="rating" value="1" <?php if (1-1 < $rating && $rating <= 1) { echo "checked"; } ?> >
                  <label for="rating1">1</label>
                  <input type="radio" name="rating" value="2" <?php if (2-1 < $rating && $rating <= 2) { echo "checked"; } ?> >
                  <label for="rating2">2</label>
                  <input type="radio" name="rating" value="3" <?php if (3-1 < $rating && $rating <= 3) { echo "checked"; } ?> >
                  <label for="rating3">3</label>
                  <input type="radio" name="rating" value="4" <?php if (4-1 < $rating && $rating <= 4) { echo "checked"; } ?> >
                  <label for="rating4">4</label>
                  <input type="radio" name="rating" value="5" <?php if (5-1 < $rating && $rating <= 5) { echo "checked"; } ?> >
                  <label for="rating5">5</label>
                </span>
                <button type="submit">Submit</button>
              </form>

              <div id="fb-root"></div>
              <script>(function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id;
                js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8";
                fjs.parentNode.insertBefore(js, fjs);
              }(document, 'script', 'facebook-jssdk'));</script>
              <div class="fb-comments" data-href="https://localhost/eventPage.php?id=<?php echo $event->id; ?>" data-numposts="5"></div>

         </div>

         <?php } ?>
       <?php } else { ?>
         <?php

          echo gen_event_search_list($search, '../php/eventPage.php', $after, $limit);

         ?>
       <?php } ?>

   </body>
</html>
