<?php

App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');
App::uses('CronsComponent', 'Controller/Component');
class CronShell extends Shell {

    function startup() {
        $collection = new ComponentCollection();
        $this->Crons = new CronsComponent($collection);
        $controller = new Controller();
        $this->Crons->startup($controller);
    }

    function main() {
        $this->out($this->Crons->runPending());
    }
}
