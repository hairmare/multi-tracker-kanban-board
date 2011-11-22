<?php

namespace Mtkb\Tracker;

use Zend\Json\Server\Server,
    Zend\Json\Server\Smd;

require_once __DIR__."/../../bootstrap.php";

// load config
$trackerConfig = \Zend\Registry::get("tracker_config");

// create service classname from config
$trackerServiceClass = '\Mtkb\Tracker\\'
                     . ucfirst($trackerConfig['type'])
                     . '\Service';

// run service
$server = new Server();
$server->setClass($trackerServiceClass);
     
if ('GET' == $_SERVER['REQUEST_METHOD']) {
    $server->setTarget($_SERVER['REQUEST_URI'])
           ->setEnvelope(Smd::ENV_JSONRPC_2);
    $smd = $server->getServiceMap();
   
    // Set Dojo compatibility:
    $smd->setDojoCompatible(true);
     
    header('Content-Type: application/json');
    echo $smd;
    return;
 }
     
$server->handle();
