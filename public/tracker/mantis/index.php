<?php

namespace Mtkb\Www\Tracker\Mantis;

use Mtkb\Tracker\Mantis,
    Zend\Json\Server\Server;

require_once dirname(__FILE__)."/../../../config_default.php";
require_once dirname(__FILE__)."/../../../config.php";
global $globalMantisOptions;
$globalMantisOptions = $config_tracker_mantis;

require_once dirname(__FILE__)."/../../../src/Tracker/Mantis.php";
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Server/Server.php";
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Server/AbstractServer.php";
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Server/Definition.php";
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Server/Reflection.php";
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Server/Exception.php";
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Server/Reflection/Exception.php";
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Server/Exception/InvalidArgumentException.php";
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Server/Reflection/Exception/InvalidArgumentException.php";
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Server/Reflection/ReflectionClass.php";
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Server/Method/Prototype.php";
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Server/Method/Callback.php";
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Server/Method/Definition.php";
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Server/Reflection/Prototype.php";
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Server/Reflection/ReflectionReturnValue.php";
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Server/Reflection/Node.php";
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Server/Reflection/AbstractFunction.php";
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Server/Reflection/ReflectionMethod.php";
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Json/Server/Smd/Service.php";
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Json/Server/Smd.php";
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Json/Server/Error.php";
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Json/Server/Request.php";
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Json/Server/Request/Http.php";
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Json/Server/Response.php";
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Json/Server/Response/Http.php";
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Json/Server/Server.php";

class Service {
    private $_mantis = false;
    protected $mantisClassname = '\Mtkb\Www\Tracker\Mantis';

    public function projects()
    {
        $mantis = $this->_getMantis();
        return $mantis->getProjects();
    }
    private function _getMantis() 
    {
        if (!$this->_mantis) {
            $this->_initMantis();
        }
        return $this->_mantis;
    }
    private function _initMantis()
    {
        global $globalMantisOptions;
        $mantis = new $this->mantisClassname;;
        $mantis->setOptions($globaMantisOptions);
        $mantis->setDataStore("projects", new Projects);
        $mantis->setDataStore("state", new States);
        $this->_mantis = $mantis;
    }
}

$server = new Server();
$server->setClass('Mtkb\Www\Tracker\Mantis\Service');
     
if ('GET' == $_SERVER['REQUEST_METHOD']) {
    $server->setTarget('/json-rpc.php')
           ->setEnvelope(Zend_Json_Server_Smd::ENV_JSONRPC_2);
    $smd = $server->getServiceMap();
   
    // Set Dojo compatibility:
    $smd->setDojoCompatible(true);
     
    header('Content-Type: application/json');
    echo $smd;
    return;
 }
     
$server->handle();
