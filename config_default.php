<?php

$username = "user";
$password = "";
$location = "http://example.com/mantisbt/api/soap/mantisconnect.php";
$wsdlfile = $location."?wsdl";

$config_tracker_mantis = array(
    "location" => $location,
    "login"    => $username,
    "password" => $password,
    "trace"    => true
);

