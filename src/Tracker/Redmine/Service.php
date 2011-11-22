<?php

namespace Mtkb\Tracker\Redmine;

use Mtkb\Tracker\DojoRpcInterface,
    Mtkb\Tracker\Projects,
    Mtkb\Tracker\States,
    Mtkb\Tracker\Tickets;


class Service implements DojoRpcInterface
{
    private $_redmine = false;
    protected $redmineClassname = '\Mtkb\Tracker\Redmine\Tracker';

    public function projects()
    {
        return $this->_getRedmine()->getProjects()->toArray();
    }
    public function states()
    {
        return $this->_getRedmine()->getStates()->toArray();
    }
    public function tickets($projectId)
    {
        return $this->_getRedmine()->getTickets($projectId)->toArray();
    }
    public function moveTicket($ticketId, $newStatus, $note = null)
    {
        return $this->_getRedmine()
                    ->moveTicket(
                        $ticketId,
                        $newStatus,
                        $note
                    )->toArray();
    }

    private function _getRedmine()
    {
        if (!$this->_redmine) {
            $this->_initRedmine();
        }
        return $this->_redmine;
    }
    private function _initRedmine()
    {
        $redmineOptions = \Zend\Registry::get("tracker_config");

        $redmine = new $this->redmineClassname;
        $redmine->setOptions($redmineOptions);

        // project api
        $project = new Project;
        $project->site = $redmineOptions['location'];
        $project->request_format = 'xml';
        $redmine->setArProject($project);
        $redmine->setDataStore("projects", new Projects);

        // issue_status are already setup through a dirty config hack
        $redmine->setDataStore("states", new States);

        // issue api
        $issue = new Issue;
        $issue->site = $redmineOptions['location'];
        $issue->request_format = 'xml';
        $redmine->setArTicket($issue);
        $redmine->setDataStore("tickets", new Tickets);

        // done :)
        $this->_redmine = $redmine;
    }
}
