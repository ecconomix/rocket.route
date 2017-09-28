<!DOCTYPE html>
<html>
<head>
    <title>Simple Map</title>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">

    <!--  Fonts  -->
    <link href="https://fonts.googleapis.com/css?family=Encode+Sans+Expanded:700" rel="stylesheet">

    <!--  Styles  -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav>
    <h1 class="main-heading">Rocket Route Test project</h1>
</nav>
<section class="map-section">
<div id="map"></div>
    <form action="index.php" method="post" name="icao-form">
        <input type="text" name="ICAO" id="input-icao" class="main-input">
        <input type="button" value="Submit" id="btnsubmit">
    </form>
</section>

<?php

// User ICAO input data
$icao = $_POST["ICAO"];

// Array of calculated coordinates
$data = [];

$client = new SoapClient("https://apidev.rocketroute.com/notam/v1/service.wsdl");

// Send XML request to API
$respons =  $client->getNotam(
    "<REQWX>" .
        "<USR>oleg.chychkevych@gmail.com</USR>" .
        "<PASSWD>f480b65d410b7070cef0fdb8c1616340</PASSWD>" .
        "<ICAO>" . $icao . "</ICAO>" .
    "</REQWX>"
);

// get response from API
$xml = simplexml_load_string($respons) or die("Error: Cannot create object");


for($i=0; $i < count($xml->NOTAMSET->NOTAM); $i++) {
    $notamLocation = explode("/", $xml->NOTAMSET->NOTAM[$i]->ItemQ);
    $data[$i][0] = 'Name' . $i;
    if ((substr( $notamLocation[7], 4 , 1 ) == 'S')){
        $data[$i][1] = (substr($notamLocation[7], 0, 2) + (substr($notamLocation[7], 2, 2) / 60)) * -1;
    } else $data[$i][1] = (substr($notamLocation[7], 0, 2) + (substr($notamLocation[7], 2, 2) / 60));

    if ((substr( $notamLocation[7], 10 , 1 ) == 'W')) {
        $data[$i][2] = (substr($notamLocation[7], 5, 2) + (substr($notamLocation[7], 7, 2) / 60))  * -1;
    } else $data[$i][2] = (substr($notamLocation[7], 5, 2) + (substr($notamLocation[7], 7, 2) / 60));

};

?>
<script>
    function submitForm() {
        // Get the first form with the name
        // Usually the form name is not repeated
        // but duplicate names are possible in HTML
        // Therefore to work around the issue, enforce the correct index
        var frm = document.getElementsByName('icao-form')[0];
        frm.submit(); // Submit the form
        frm.reset();  // Reset all form data
        return false; // Prevent page refresh
    }
</script>
<script>
    var map;
//    function initMap() {
//        map = new google.maps.Map(document.getElementById('map'), {
//            center: {lat: -34.397, lng: 150.644},
//            zoom: 8
//        });
//    }

    // The following example creates complex markers to indicate beaches near
    // Sydney, NSW, Australia. Note that the anchor is set to (0,32) to correspond
    // to the base of the flagpole.

    function initMap() {

        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 10,
            center: {lat: beaches[0][1], lng: beaches[0][2]}
        });

        setMarkers(map);
    }

    // Get array of Coordinates from API
    var beaches = <?= json_encode($data) ?>;

    function setMarkers(map) {
        var image = {
            url: 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png',
            // This marker is 20 pixels wide by 32 pixels high.
            size: new google.maps.Size(20, 32),
            // The origin for this image is (0, 0).
            origin: new google.maps.Point(0, 0),
            // The anchor for this image is the base of the flagpole at (0, 32).
            anchor: new google.maps.Point(0, 32)
        };
        // Shapes define the clickable region of the icon. The type defines an HTML
        // <area> element 'poly' which traces out a polygon as a series of X,Y points.
        // The final coordinate closes the poly by connecting to the first coordinate.
        var shape = {
            coords: [1, 1, 1, 20, 18, 20, 18, 1],
            type: 'poly'
        };
        for (var i = 0; i < beaches.length; i++) {
            var beach = beaches[i];
            var marker = new google.maps.Marker({
                position: {lat: beach[1], lng: beach[2]},
                map: map,
                icon: image,
                shape: shape,
                title: beach[0],
                zIndex: beach[3]
            });
        }
    }
</script>
<script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBrqKDNm4duVfVsWWtAve1871FxqQUAEVU&callback=initMap"
        async defer>
</script>
</body>
</html>