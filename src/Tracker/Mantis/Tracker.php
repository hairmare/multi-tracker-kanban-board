<?php
/**
 * Multi Tracker Kanban Board
 *
 * 2011, 2012 - Lucas S. Bickel <hairmare@purplehaze.ch>
 * Alle Rechte vorbehalten
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


namespace Mtkb\Tracker\Mantis;

use Mtkb\Tracker\Projects,
    Zend\Dojo\Data,
    SoapClient,
    stdClass;

class Tracker {
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
                $soapReturn = $this->_mapPruneSoapReturn("projects", $soapReturn);

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
    public function getTickets($projectId)
    {
        static $tickets = false;
        try {
            $soapReturn = $this->_soap->mc_project_get_issues(
                    $this->_options["login"],
                    $this->_options["password"],
                    $projectId
            );
            $soapReturn = $this->_mapPruneSoapReturn("tickets", $soapReturn);
            $this->_dataStore["tickets"]->addItems($soapReturn);
            $tickets = $this->_dataStore["tickets"];
        } catch (SOAPFault $fault) {
            $tickets = $this->_handleSoapFault($fault);
        }
        return $tickets;
    }
    public function moveTicket($ticketId, $newStatus, $note)
    {
        try {
            $states = $this->getStates();

            // load issue
            $issueData = $this->_soap->mc_issue_get(
                $this->_options["login"],
                $this->_options["password"],
                $ticketId
            );
            // update issue
            $issueData->status = $states[$newStatus];

            // add note
            if ($note) {
                $issueNote = new stdClass;
                
                $issueNote->text = $note;
                $soapReturn = $this->_soap->mc_issue_note_add(
                    $this->_options["login"],
                    $this->_options["password"],
                    $ticketId,
                    $issueNote
                );
            }

            // save issue
            $soapReturn = $this->_soap->mc_issue_update(
                $this->_options["login"],
                $this->_options["password"],
                $ticketId,
                $issueData
            );

            // this is for displaying the ticket after saving
            $soapReturn = $this->_soap->mc_issue_get(
                $this->_options["login"],
                $this->_options["password"],
                $ticketId
            );
            $soapReturn = $this->_mapPruneSoapReturn("tickets", array($soapReturn));
            $this->_dataStore["tickets"]->addItems($soapReturn);
            $ticket = $this->_dataStore["tickets"];
        } catch (SOAPFault $fault) {
            $ticket = $this->_handleSoapFault($fault);
        }
        return $ticket;
    }
    private function _handleSoapFault($fault)
    {
        $this->_isError = true;
        // insecurely exposing the whole fault :D
        return $fault;
    }
    private function _mapPruneSoapReturn($mode, $soapReturn)
    {
        // what parts of mantis will we expose
        $allowedNodes = array(
            "id",
            "name",
        );
        if ($mode == "tickets") {
            $allowedNodes[] = "status";
            $allowedNodes[] = "description";
        }

        $nodeMap = array();
        if ($mode == "tickets") {
            $nodeMap["summary"] = "name";
        }

        // map/prune items
        foreach ($soapReturn AS $key => $val) {

            foreach (array_keys(get_object_vars($val)) AS $var) {
                
                // map
                if (in_array($var, array_keys($nodeMap))) {
                    $newvar = $nodeMap[$var];
                    $val->$newvar = $val->$var;
                }
                // prune
                if (!in_array($var, $allowedNodes)) {
                    unset($val->$var);
                }
            }
            $soapReturn[$key] = $val;
        }
        return $soapReturn;
    }
}

class Error {
}

