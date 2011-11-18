<?php

namespace Mtkb\Www\Tracker\Mantis;

use Mtkb\Tracker\Mantis\Mantis,
    Mtkb\TrackerProjects,
    Mtkb\Tracker\Mantis\States,
    Mtkb\Tracker\Mantis\Tickets,
    Zend\Json\Server\Server,
    Zend\Json\Server\Smd,
    SoapClient;

require_once __DIR__."/../../../bootstrap.php";


// ugly hack to keep global options :)
global $globalMantisOptions;
$globalMantisOptions = $config_tracker_mantis;

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
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Server/Method/Parameter.php";
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Server/Reflection/Prototype.php";
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Server/Reflection/ReflectionReturnValue.php";
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Server/Reflection/Node.php";
require_once dirname(__FILE__)."/../../../lib/zf/library/Zend/Server/Reflection/ReflectionParameter.php";
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
    protected $mantisClassname = '\Mtkb\Tracker\Mantis\Mantis';

    public function projects()
    {
        return $this->_getMantis()->getProjects()->toArray();
    }
    public function states()
    {
        return $this->_getMantis()->getStates()->toArray();
    }
    public function tickets($projectId)
    {
        return $this->_getMantis()->getTickets($projectId)->toArray();
    }
    public function moveTicket($ticketId, $newStatus, $note = null)
    {
        return $this->_getMantis()
                    ->moveTicket(
                        $ticketId,
                        $newStatus,
                        $note
                    )->toArray();
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
        $mantis->setOptions($globalMantisOptions);
        $mantis->setSoapClient(
            new SoapClient(
                $globalMantisOptions['wsdl'],
                $globalMantisOptions
            )
        );
        $mantis->setDataStore("projects", new TrackerProjects);
        $mantis->setDataStore("states", new States);
        $mantis->setDataStore("tickets", new Tickets);
        $this->_mantis = $mantis;
    }
}

$server = new Server();
$server->setClass('Mtkb\Www\Tracker\Mantis\Service');
     
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
