<?php

namespace Mtkb\Tracker;

interface DojoRpcInterface 
{
    public function projects();
    public function states();
    public function tickets($projectId);
    public function moveTicket($ticketId, $newStatus, $note = null);
}
