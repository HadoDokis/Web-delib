<?php
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');


class TdtTask extends Shell {
    
    public $uses = array('Deliberation','TdtMessage');
    
    public function execute() {
    }
    
    public function classification() {
        $collection = new ComponentCollection();
        App::uses('S2lowComponent', 'Controller/Component');
        $this->S2low = new S2lowComponent($collection);
        
        return $this->S2low->getClassification();
    }
    
    public function migrationMessageTdt4201() {
            
        try
        {
            $messages = $this->TdtMessage->find('all', array(
                    'fields' => array('TdtMessage.id','TdtMessage.tdt_id', 'tdt_type', 'delib_id'),
                    'recusive'=>-1,
                    'order'=>'TdtMessage.tdt_id DESC')
            );
            
            if(!empty($messages))
            foreach($messages as $message)
            {
                $this->TdtMessage->begin();
                $message_parent = $this->TdtMessage->find('first', array(
                    'fields' => array('TdtMessage.id'),
                    'conditions' => array(  'TdtMessage.delib_id' => $message['TdtMessage']['delib_id'],
                                            'TdtMessage.tdt_type' => $message['TdtMessage']['tdt_type'],
                                            'TdtMessage.id !=' => $message['TdtMessage']['id'],
                                            'TdtMessage.parent_id is null'),
                    'order'=>'TdtMessage.tdt_id ASC',
                    'recusive'=>-1)
                );
                
                if(!empty($message_parent)){
                    $this->TdtMessage->id=$message['TdtMessage']['id'];
                    $this->TdtMessage->saveField('parent_id', $message_parent['TdtMessage']['id']);
                    $this->TdtMessage->commit();
                }

            }
            return true;
        }
        catch (ErrorException $e)
        {
            $this->TdtMessage->rollback();
            $this->out($e->getMessage());
        }
        
        return false;
    }
}
?>