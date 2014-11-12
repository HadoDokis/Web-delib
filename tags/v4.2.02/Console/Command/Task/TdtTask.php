<?php
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

App::uses('Tdt', 'Lib');
App::uses('S2lowComponent', 'Controller/Component');

class TdtTask extends Shell {
    
    public $uses = array('Deliberation','TdtMessage');
    
    public function execute() {
    }
    
    public function classification() {
        $collection = new ComponentCollection();
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
    
    
    
   /**
     * Récuperation les documents Tdt
     * @return bool
     */
    public function recupDataMessageTdt() {
 
        try {
            $collection = new ComponentCollection();
            $this->S2low = new S2lowComponent($collection);
        
            $Tdt = new Tdt;
            $messages=$this->TdtMessage->find('all', array(
                'fields' => array('id','tdt_id','delib_id'),
                'contains'=> 'Deliberation',
                'conditions' => array('tdt_data NOT' => NULL),
                'recursive' => -1,
            ));
            foreach ($messages as $message) {
                $this->out('Récupération pour le message Tdt => '.$message['TdtMessage']['id']);
                $this->TdtMessage->id = $message['TdtMessage']['id'];
                $this->TdtMessage->saveField('tdt_data', $Tdt->getDocument($message['TdtMessage']['tdt_id']));
                $this->out('Ok');
            }
            return true;
        } catch (Exception $e) {
            throw new Exception('Echec lors de la récupération du message Tdt => '.$message['TdtMessage']['id']);
        }
        return false;
    }
}