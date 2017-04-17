<?php
ob_start();
require_once ('config.php');
require_once ('check_session.php');
require_once('utils.php');
require_once('rso.php');
require_once('university.php');
require_once('event.php');

require_once('rating.php');
?>

<?php

$curruser = new user_info();
$curruser->updateOnId($_SESSION['userid']);

$success = '';
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
  // add rating
  // add location

  $locationid = 1; // TODO: fix

  $eventStartTime_Hour = $_POST['eventStartTime_Hour'];
  $eventStartTime_Minute = $_POST['eventStartTime_Minute'];

  $name = $_POST['eventName'];
  $category = $_POST['category'];
  $startDate = $_POST['eventStartDate'];
  $startTime = $eventStartTime_Hour.":".$eventStartTime_Minute.":00";
  $endDate = $_POST['eventEndDate'];

  $frequency = 0; // TODO: please add frequency option

  $email = $_POST['eventContactEmail'];
  $phone = $_POST['eventContactPhone'];

  $days = '';
  if (isset($_POST['monChecked'])) { $days .= "'mon,'"; }
  if (isset($_POST['tueChecked'])) { $days .= "'tues,'"; }
  if (isset($_POST['wedChecked'])) { $days .= "'wed,'"; }
  if (isset($_POST['thuChecked'])) { $days .= "'thur,'"; }
  if (isset($_POST['friChecked'])) { $days .= "'fri,'"; }
  if (isset($_POST['satChecked'])) { $days .= "'sat,'"; }
  if (isset($_POST['sunChecked'])) { $days .= "'sun,'"; }

  $rating = new rating_info();
  $rating->addRating();
  $rid = $rating->simpleQuery('SELECT MAX(ratingid) FROM ratings')['MAX(ratingid)'];

  $event = new event_info();
  $event = $event->addEvent(
    $curruser->id, $curruser->universityid, $locationid,
    $_POST['rso'], $rid, $name, $category,
    $_POST['description'], $startDate, $endDate,
    $days, $startTime, -1,
    $frequency,
    $email, $phone
  );

  if ($event !== null)
  {
    echo "<script>window.open('../php/eventPage.php?id=".$event->id."', '_parent');</script>";
  }
}
?>

<html>
<head>
  <title>COP4710 - Event Create</title>
  <link href="../css/style.css" rel="stylesheet">

  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/resources/demos/style.css">

  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
  $( function() {
  $( "#startDatePicker" ).datepicker();
  } );

  $( function() {
  $( "#endDatePicker" ).datepicker();
  } );
  </script>

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
  <?php echo gen_top_nav($curruser->id, 'Create Event'); ?>
  <h1><?php echo $success; ?></h1>

<!-- Create event shit  -->
<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>

<script>
  $(function() {
       $( "#calendar" ).datepicker();
  });
</script>

  <div >
    <h1>New Event</h1>
          <div class = "container">
           <form class = "form-register" role = "form" action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method = "post">

              <h5 class = "form-register-heading"><?php echo $success; ?></h5>

              <h5 style="display: inline;">Name:</h5>
              <input type="text" class="form-control" name = "eventName" placeholder="We Like To Party!" required autofocus></br>

              <br>

              <h5 style="display: inline;">Category:</h5>

              <select name="category">
                <option value="Auto, Boat, & Air">Auto, Boat, & Air</option>
                <option value="Business & Professional">Business & Professional</option>
                <option value="Charity and Causes">Charity and Causes</option>
                <option value="Community & Culture">Community & Culture</option>
                <option value="Family & Education">Family & Education</option>
                <option value="Fashion & Beauty">Fashion & Beauty</option>
                <option value="Film, Media & Entertainment">Film, Media & Entertainment</option>
                <option value="Food & Drink">Food & Drink</option>
                <option value="Government & Politics">Government & Politics</option>
                <option value="Health & Wellness">Health & Wellness</option>
                <option value="Hobbies & Special Interest">Hobbies & Special Interest</option>
                <option value="Home & Lifestyle">Home & Lifestyle</option>
                <option value="Music">Music</option>
                <option value="Other">Other</option>
                <option value="Performing & Visual Arts">Performing & Visual Arts</option>
                <option value="Religion & Spirituality">Religion & Spirituality</option>
                <option value="Science & Technology">Science & Technology</option>
                <option value="Seasonal & Holiday">Seasonal & Holiday</option>
                <option value="Sports & Fitness">Sports & Fitness</option>
                <option value="Travel & Outdoor">Travel & Outdoor</option>
			  </select>

             <h5 style="display: inline;">Event Visibility:</h5>
              <select name="privacy">
                <option value="Public">Public</option>
                <option value="Private">Private</option>
                <option value="RSO">RSO</option>
             </select>

              <h5 style="display: inline;">RSO:</h5>
              <select name="rso">
                <option value='-1'>None</option>
                <?php echo gen_rso_options($_SESSION['userid']); ?>
              </select>

              <br>
              <br>

              <h5 style="display: inline;">Start Date:</h5>
              <input name="eventStartDate" type="text" id="startDatePicker">

              <h5 style="display: inline;">Start Time:</h5>
              <input type="number" class="form-control" name = "eventStartTime_Hour" value="12" min="0" max="24" required>
              <input type="number" class="form-control" name = "eventStartTime_Minute" value="00" min="0" max="59" required>

              <br><br>

              <h5 style="display: inline;">End Date:</h5>
              <input name="eventEndDate" type="text" id="endDatePicker">

              <div class="weekDays-selector">
                <input name="monChecked" type="checkbox" id="weekday-mon" class="weekday" />
                <label for="weekday-mon">M</label>
                <input name="tueChecked" type="checkbox" id="weekday-tue" class="weekday" />
                <label for="weekday-tue">T</label>
                <input name="wedChecked" type="checkbox" id="weekday-wed" class="weekday" />
                <label for="weekday-wed">W</label>
                <input name="thuChecked" type="checkbox" id="weekday-thu" class="weekday" />
                <label for="weekday-thu">T</label>
                <input name="friChecked" type="checkbox" id="weekday-fri" class="weekday" />
                <label for="weekday-fri">F</label>
                <input name="satChecked" type="checkbox" id="weekday-sat" class="weekday" />
                <label for="weekday-sat">S</label>
                <input name="sunChecked" type="checkbox" id="weekday-sun" class="weekday" />
                <label for="weekday-sun">S</label>
  			      </div>

              <br>

              <h5 style="display: inline;">Contact Phone:</h5>
              <input type="text" class="form-control" name = "eventContactPhone" placeholder="4071234567" required><br><br>

              <h5 style="display: inline;">Contact Email:</h5>
              <input type="text" class="form-control" name = "eventContactEmail" placeholder="admin@yourevent.com" required><br>

              <h5 style="display: inline;">Address:</h5>
              <input type="text" class="form-control" id='address' name = "address" placeholder="Click on map below for the address" required></br>
              <h5 class = "empty-space"></h5>

              <h5 style="display: block; padding:0px; padding-top: 0px;">Description:</h5>
              <textarea type="text" class="form-control" name = "description"required><p>&nbsp;</p></textarea>

  			      <br>

              <button class = "btn btn-lg btn-primary btn-block" type="submit" name="register">Register</button>
              <h5 class = "empty-space"></h5>


           </form>
           </div>




           <input id="pac-input" class="controls" type="text" placeholder="Search Box">
           <div id="map"></div>
           <script>
             // This example adds a search box to a map, using the Google Place Autocomplete
             // feature. People can enter geographical searches. The search box will return a
             // pick list containing a mix of places and predicted search terms.

             // This example requires the Places library. Include the libraries=places
             // parameter when you first load the API. For example:
             // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

             function initAutocomplete() {
               var map = new google.maps.Map(document.getElementById('map'), {
                 center: {lat: 28.6024, lng: -81.2001},
                 zoom: 15,
                 mapTypeId: 'roadmap'
               });
               var geocoder = new google.maps.Geocoder();
               var infowindow = new google.maps.InfoWindow();

               // Create the search box and link it to the UI element.
               var input = document.getElementById('pac-input');
               var searchBox = new google.maps.places.SearchBox(input);
               map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

               // Bias the SearchBox results towards current map's viewport.
               map.addListener('bounds_changed', function() {
                 searchBox.setBounds(map.getBounds());
               });

               var markers = [];

               searchBox.addListener('places_changed', function() {
                 var places = searchBox.getPlaces();

                 if (places.length == 0) {
                   return;
                 }

                 // Clear out the old markers.
                 markers.forEach(function(marker) {
                   marker.setMap(null);
                 });
                 markers = [];

                 // For each place, get the icon, name and location.
                 var bounds = new google.maps.LatLngBounds();
                 places.forEach(function(place) {
                   if (!place.geometry) {
                     console.log("Returned place contains no geometry");
                     return;
                   }
                   var icon = {
                     url: place.icon,
                     size: new google.maps.Size(71, 71),
                     origin: new google.maps.Point(0, 0),
                     anchor: new google.maps.Point(17, 34),
                     scaledSize: new google.maps.Size(25, 25)
                   };

                   // Create a marker for each place.
                   markers.push(new google.maps.Marker({
                     map: map,
                     icon: icon,
                     title: place.name,
                     position: place.geometry.location
                   }));

                   if (place.geometry.viewport) {
                     // Only geocodes have viewport.
                     bounds.union(place.geometry.viewport);
                   } else {
                     bounds.extend(place.geometry.location);
                   }
                 });
                 map.fitBounds(bounds);
               });
               // Listen for the event fired when the user selects a prediction and retrieve
               // more details for that place.
               google.maps.event.addListener(map, 'click', function(event) {
               geocoder.geocode({
                   'latLng': event.latLng
                   }, function(results, status) {
                       if (status == google.maps.GeocoderStatus.OK) {
                           if (results[0]) {
                           document.getElementById('address').value = results[0].formatted_address;
                           }
                       }
                   });
               });
             }

             function addMarker(pos) {

       			// Clear out the old markers.
       			markers.forEach(function (marker) {
       				marker.setMap(null);
       			});
       			markers = [];

       			// Add Marker
       			var marker = new google.maps.Marker({
       				draggable: true,
       				position: pos,
       				map: map
       			});

       			google.maps.event.addListener(marker, 'dragend', function (event) {
       				marker_pos = this.getPosition();
       			});

       			markers.push( marker );
       			return marker;
       		}

               function getMarker()
               {
       			return marker;
       		}

           </script>
           <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDSVinGJtXybbaYrT5XLRQhSwY9x3Tbxfo&libraries=places&callback=initAutocomplete"
                async defer></script>
    </body>

  </div>


</body>
</html>
