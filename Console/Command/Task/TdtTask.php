<?php
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');


class TdtTask extends Shell {
    
    public function execute() {
    }
    
    public function classification() {
        $collection = new ComponentCollection();
        App::uses('S2lowComponent', 'Controller/Component');
        $this->S2low = new S2lowComponent($collection);
        
        return $this->S2low->getClassification();
    }
}
?>