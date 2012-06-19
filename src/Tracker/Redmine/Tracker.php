<?php

namespace Mtkb\Tracker\Redmine;

class Tracker {
    private $_options = false;
    private $_dataStore = array();
    private $_arProject = false;
    private $_arTicket = false;
    private $_statuses = false;

    public function setOptions($options)
    {
        $this->_options = $options;
        $this->_statuses = $options['statuses'];
    }
    public function setArProject($activeResource)
    {
        $this->_arProject = $activeResource;
    }
    public function setArTicket($activeResource)
    {
        $this->_arTicket = $activeResource;
    }
    public function setDataStore($type, $dataStore)
    {
        $this->_dataStore[$type] = $dataStore;
    }
    public function getProjects()
    {
        try {
            $rpcReturn = $this->_arProject->find('all');

            $projects = array();
            foreach ($rpcReturn AS $value) {
                $data = $this->_mapPruneConvert("projects", $value->_data);
                $projects[] = $data;
            }

            $this->_dataStore["projects"]->addItems($projects);
            $projects = $this->_dataStore["projects"];

        } catch (Exception $e) {
            var_dump($e);
        }
        return $projects;
    }
    public function getStates()
    {
        $this->_dataStore["states"]->addItems($this->_statuses);
        return $this->_dataStore["states"];
    }
    public function getTickets($projectId)
    {
        try {
            // Oh how i like using @ to ignore broken libs :)
            $project = @$this->_arProject->get($projectId);
            $rpcReturn = $project->get("issues");

            $issues = @$rpcReturn->_data['issue'];
            if (!$issues) {
                // zarro bugs, go make some
                throw new \Exception('zarro buggs');
            }
            $tickets = array();
            foreach ($rpcReturn->_data['issue'] AS $value) {
                $ticket = new \stdClass;
                $ticket->id = (int)$value->id;
                $ticket->name = (string)$value->subject;
                $ticket->description = (string)$value->description;
                $ticket->status = new \stdClass;
                $ticket->status->id = (int)$value->status['id'];
                $ticket->status->name = (string)$value->status['name'];
                $tickets[] = $ticket;
            }

            $this->_dataStore["tickets"]->addItems($tickets);
            $tickets = $this->_dataStore["tickets"];

        } catch (Exception $e) {

            var_dump($e);
        }
        return $tickets;
    }
    public function moveTicket($ticketId, $newStatus, $note = null)
    {
        try {
            // load isse
            $this->_arTicket->find($ticketId);

            // update status (whew, i tried like over 9000 possibilities thanks to redmines sooper apidox)
            $this->_arTicket->set('status_id', $newStatus);

            // add note
            if ($note !== null) {
                $this->_arTicket->set('notes', $note);
            }

            // save teh shizzle
            $this->_arTicket->save();

            // reload to finish up
            $value = $this->_arTicket->find($ticketId);

            // re-add to store in new position after saving
            $ticket = new \stdClass;
            $ticket->id = (int)$value->id;
            $ticket->name = (string)$value->subject;
            $ticket->description = (string)$value->description;
            $ticket->status = new \stdClass;
            $ticket->status->id = (int)$value->status['id'];
            $ticket->status->newStatus = $newStatus;
            $ticket->status->name = (string)$value->status['name'];

            $this->_dataStore["tickets"]->addItem($ticket);

        } catch (Exception $e) {
            var_dump($e);
        }
        return $this->_dataStore["tickets"];
    }

    private function _mapPruneConvert($mode, $data)
    {
        // what parts of mantis will we expose
        $allowedNodes = array(
            "id",
            "name",
        );
        if ($mode == "projects") {
            $allowedNodes[] = "status";
        }
        if ($mode == "tickets") {
            $allowedNodes[] = "status";
            $allowedNodes[] = "description";
            $allowedNodes[] = "subject";
        }

        $nodeMap = array();
        if ($mode == "tickets") {
            $nodeMap["subject"] = "name";
        }

        $postPrune = array();
        if ($mode == "tickets") {
            $postPrune[] = "subject";
        }


        // map/prune items
        foreach ($data AS $key => $val) {

            // map
            if (in_array($key, array_keys($nodeMap))) {
                $newvar = $nodeMap[$key];
                $data[$newvar] = $val;
            }
            // prune
            if (!in_array($key, $allowedNodes)) {
                unset($data[$key]);
            }
        }
        $return = new \stdClass;
        foreach ($data AS $key => $val) {
            if ($key == 'id') {
                $val = (int)$val;
            }
            if (is_a($val, "\SimpleXMLElement")) {
                $id   = (array)$val['id'];
                $name = (array)$val['name'];
                if (!empty($name)) {
                    if (is_array($name)) {
                        $val = new \stdClass;
                        $val->id = $id[0];
                        $val->name = $name[0];
                    } else {
                        $val = $name;
                    }
                } else {
                    $val = (string)$val;
                }
            }
            if (!in_array($key, $postPrune)) {
                $return->$key = $val;
            }
        }
        return $return;
    }
}

class Project extends \ActiveResource
{
    /**
     * hack for phpactiveresource not handling namespaces correctly
     */
    var $element_name = 'project';
    var $request_format = 'xml'; 
}
class Issue extends \ActiveResource
{
    /**
     * hack for phpactiveresource not handling namespaces correctly
     */
    var $element_name = 'issue';
    var $request_format = 'xml'; 
}
