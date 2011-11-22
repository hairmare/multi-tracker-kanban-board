<?php

## Mantis BT Config
# Mantis is simple to configure as it has complete API support for all needed features.
$username = "user";
$password = "";
$location = "http://example.com/mantisbt/api/soap/mantisconnect.php";
$wsdlfile = $location."?wsdl";

$config_tracker_mantis = array(
    "type"     => "mantis",
    "wsdl"     => $wsdlfile,
    "location" => $location,
    "login"    => $username,
    "password" => $password,
    "trace"    => true
);

## Redmine Config
# Redmine needs alot of help, since only core APIs are exposed. Remember that you will
# need to reconfigure mtkb if you make changes in Redmine.
$username = "user";
$password = "";
$location = "http://example.com/redmine";
$wsdlfile = $location."?wsdl";

# You will probably need to tailor these. Redmine has no clean API for grabbing these and
# refuse to solve this in any other substandard way for the sake of this project. Have a
# look at how Mantis does the API thing. Personally I would just can Redmine just because
# it has a really poor API. The german example below doesn't make this whole thing any
# nicer, but you'll have to live with it since that is what i need right now.
$statuses = array(
    array (
        "id"   => "1",
        "name" => "Neu"
    ),
    array(
        "id"   => "2",
        "name" => "In Bearbeitung"
    ),
    array(
        "id"   => "3",
        "name" => "Gelöst"
    ),
    array(
        "id"   => "4",
        "name" => "Feedback"
    ),
    array(
        "id"   => "5",
        "name" => "Erledigt"
    ),
    array(
        "id"   => "6",
        "name" => "Abgewiesen"
    )
);

$config_tracker_redmine = array(
    "type"     => "redmine",
    "location" => $location,
    "statuses" => $statuses
);

# choose one here
$config_tracker = $config_tracker_mantis;
$config_tracker = $config_tracker_redmine;

$config_zf_dir   = __DIR__ . "/lib/zf/library/";
$config_ar_dir   = __DIR__ . "/lib/phpactiveresource/";
$config_mtkb_dir = __DIR__ . "/src/";

