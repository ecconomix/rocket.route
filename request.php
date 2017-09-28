<?php

// User ICAO input data
$res = $_POST["ICAO"];

$client = new SoapClient("https://apidev.rocketroute.com/notam/v1/service.wsdl");

// Send XML request to API
$respons =  $client->getNotam(
    "<REQWX>" .
        "<USR>oleg.chychkevych@gmail.com</USR>" .
        "<PASSWD>f480b65d410b7070cef0fdb8c1616340</PASSWD>" .
        "<ICAO>" . $res . "</ICAO>" .
    "</REQWX>"
    );

// get response from API
$xml = simplexml_load_string($respons) or die("Error: Cannot create object");

for($i=0; $i < count($xml->NOTAMSET->NOTAM); $i++) {
    $notamLocation = explode("/", $xml->NOTAMSET->NOTAM[$i]->ItemQ);
    echo $xml->NOTAMSET->NOTAM[$i]->ItemQ . '<br>';
};
?>