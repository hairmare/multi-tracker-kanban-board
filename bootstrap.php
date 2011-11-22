<?php

namespace Mtkb\Bootstrap;

require_once __DIR__."/config_default.php";
require_once __DIR__."/config.php";
require_once $config_zf_dir."/Zend/Loader/AutoloaderFactory.php";


\Zend\Loader\AutoloaderFactory::factory(
    array(
        'Zend\Loader\StandardAutoloader' => array(
            'namespaces' => array(
                'Mtkb\\' => $config_mtkb_dir
            )
        ),
        'Zend\Loader\ClassMapAutoloader' => array(
            array(
                'ActiveResource' => $config_ar_dir."/ActiveResource.php"
            )
        )
    )
);
foreach (\Zend\Loader\AutoloaderFactory::getRegisteredAutoloaders() AS $loader) {
    $loader->register();
};

\Zend\Registry::set("tracker_config", $config_tracker);

