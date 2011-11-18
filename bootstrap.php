<?php

namespace Mtkb\Bootstrap;

require_once __DIR__."/config_default.php";
require_once __DIR__."/config.php";
require_once $config_zf_dir."/Zend/Loader/AutoloaderFactory.php";


$loader = \Zend\Loader\AutoloaderFactory::factory(
    array(
        'Zend\Loader\StandardAutoloader' => array(
            'namespaces' => array(
                'Mtkb\\' => $config_mtkb_dir
            )
        )
    )
);
$loaders = \Zend\Loader\AutoloaderFactory::getRegisteredAutoloaders();
var_dump($loaders);
/*new \Zend\Loader\ClassMapAutoloader(
    "./.classmap.php"
);*/
$loader->register();

new \Mtkb\Tracker\Mantis\Mantis;
