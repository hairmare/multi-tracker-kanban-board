<?php

$config_zf_dir=__DIR__."/lib/zf/library/";
$config_mtkb_dir=__DIR__."/src/";

$username = "user";
$password = "";
$location = "http://example.com/mantisbt/api/soap/mantisconnect.php";
$wsdlfile = $location."?wsdl";

$config_tracker_mantis = array(
    "wsdl"     => $wsdlfile,
    "location" => $location,
    "login"    => $username,
    "password" => $password,
    "trace"    => true
);

