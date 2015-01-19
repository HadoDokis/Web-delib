<?php

class UserService extends AppModel {

    var $name = 'UserService';

    var $belongsTo = array(
        'User' =>
        array('className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'counterCache' => ''
        ),
        'Service' =>
        array('className' => 'Service',
            'foreignKey' => 'service_id',
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'counterCache' => ''
        )
    );

    /**
     * Fusionne 2 service entre eux
     * @param int $fusionId Service à fusionner
     * @param int $id Service cible
     * @return bool 
     */
    function fusion($fusionId, $id) {
        
        if (!$this->Service->exists($fusionId) OR !$this->Service->exists($id)) {
            throw new Exception(__('Invalide ids pour fusionner le service'));
        }
        
        if ($fusionId == $id) {
            throw new Exception(__('Impossible de fusionner le même service'));
        }

        $service=$this->Service->find('first', array(
            'conditions' => array('parent_id' => $fusionId, 'actif' => true), 
            'recursive' => -1));

        if (!empty($service)) {
            throw new Exception(__('Impossible de fusionner ce service : il possède au moins un service'));
        }
            
        try {
            
            //Liste des utilisateurs à fusionner dans le service cible
            $users = $this->find('list', array(
                'fields' => array('UserService.user_id'),
                'conditions' => array('service_id' => $fusionId, 'service_id <>' => $id),
                'recursive' => -1,
                'order'=>'id ASC'));

            $this->begin();
            foreach ($users as $key=>$user) {
                $this->delete($key);
                $userservice=$this->findByUserIdAndServiceId($user, $id);
                if(empty($userservice))
                {
                   $this->create();
                   $this->save(array('user_id'=>$user, 'service_id'=>$id));
                }
            }
            $this->commit();
            
            //Suppression du service
            $this->Service->desactive($fusionId);
            
        } catch (Exception $e) {
            $this->rollback();
            throw new Exception($e);
        }
    }

}
