<?php

class ControllerCommonCron extends \Core\Controller {

    public function index() {
        
        $output = DATE("c") . " Running Cron \n";
        $this->event->trigger('cron.run', $output);
        $logger = new \Core\Log("cron.log");
        $logger->write($output);
        echo $output;
        exit;
        
    }
}