<?php

namespace Mtkb\Tracker\Mantis;

use Mtkb\Tracker\DojoRpcInterface,
    Mtkb\Tracker\Projects,
    Mtkb\Tracker\States,
    Mtkb\Tracker\Tickets;

class Service implements DojoRpcInterface
{
    private $_mantis = false;

    protected $mantisClassname = '\Mtkb\Tracker\Mantis\Tracker';

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
        $mantisOptions = \Zend\Registry::get("tracker_config");
        $mantis = new $this->mantisClassname;
        $mantis->setOptions($mantisOptions);
        $mantis->setSoapClient(
            new \SoapClient(
                $mantisOptions['wsdl'],
                $mantisOptions
            )
        );
        $mantis->setDataStore("projects", new Projects);
        $mantis->setDataStore("states", new States);
        $mantis->setDataStore("tickets", new Tickets);
        $this->_mantis = $mantis;
    }
}


