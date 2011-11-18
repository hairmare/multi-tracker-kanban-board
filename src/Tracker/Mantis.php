<?php

namespace Mtkb\Tracker\Mantis;

use Zend\Dojo\Data,
    SoapClient;

require_once dirname(__FILE__)."/../../lib/zf/library/Zend/Json/Json.php";
require_once dirname(__FILE__)."/../../lib/zf/library/Zend/Dojo/Exception.php";
require_once dirname(__FILE__)."/../../lib/zf/library/Zend/Dojo/Exception/RuntimeException.php";
require_once dirname(__FILE__)."/../../lib/zf/library/Zend/Dojo/Data.php";

class Mantis
{
    private $_isError = false;
    private $_options = false;
    private $_soap = false;
    private $_dataStore = array();

    public function setOptions($options) {
        $this->_options = $options;
    }
    public function setSoapClient(\SoapClient $object)
    {
        $this->_soap = $object;
    }
    public function setDataStore($type, $dataStore)
    {
        $this->_dataStore[$type] = $dataStore;
    }
    public function isError() {
        return $this->_isError;
    }
    public function getProjects() {
        static $projects = false;
        try {
            if (!$projects) {
                $soapReturn = $this->_soap->mc_projects_get_user_accessible(
                    $this->_options["login"],
                    $this->_options["password"]
                );
                $this->_dataStore["projects"]->addItems($soapReturn);
                $projects = $this->_dataStore["projects"];
            }
        } catch (SOAPFault $fault) {
            $projects = $this->_handleSoapFault($fault);
        }
        return $projects;
    }
    public function getStates() {
        static $states = false;
        try {
            $soapReturn = $this->_soap->mc_enum_status(
                    $this->_options["login"],
                    $this->_options["password"]
            );
            $this->_dataStore["states"]->addItems($soapReturn);
            $states = $this->_dataStore["states"];
        } catch (SOAPFault $fault) {
            $states = $this->_handleSoapFault($fault);
        }
        return $states;
    }
    private function _handleSoapFault($fault) {
        $this->_isError = true;

        var_dump(
            $fault->getCode(),
            $fault->getMessage(),
            $this->_soap->__getLastRequest(),
            $this->_soap->__getLastResponse()
        );
    }
}

class Projects extends Data {
    protected $_identifier = "id";
}
class States extends Data {
    protected $_identifier = "id";
}

