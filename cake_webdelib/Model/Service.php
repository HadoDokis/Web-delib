<?php
class Service extends AppModel
{
    public $name = 'Service';
    public $displayField = "name";
    public $validate = array(
        'name' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Entrer le libellé.'
            )
        )
    );
    public $hasAndBelongsToMany = array(
        'User' => array(
            'classname' => 'User',
            'joinTable' => 'users_services',
            'foreignKey' => 'service_id',
            'associationForeignKey' => 'user_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'unique' => true,
            'finderQuery' => '',
            'deleteQuery' => ''),
        'Acteur' => array(
            'classname' => 'Acteur',
            'joinTable' => 'acteurs_services',
            'foreignKey' => 'service_id',
            'associationForeignKey' => 'acteur_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'unique' => true,
            'finderQuery' => '',
            'deleteQuery' => '')
    );
    
    public $actsAs = array(
        'Tree', 
        'AuthManager.AclManager' => array('type' => 'both'));
    
    /* retourne le name du service $id et de ses parents sous la forme parent1/parent12/service_id */
    function doList($id)
    {
        return $this->_doList($id);
    }

    /* fonction récursive de doList */
    function _doList($id)
    {
        $service = $this->find('first', array(
            'conditions' => array('Service.id' => $id),
            'fields' => array('name', 'parent_id'),
            'recursive' => -1));
        if (empty($service))
            return "Impossible de récupérer le service";
        if (!Configure::read('AFFICHE_HIERARCHIE_SERVICE'))
            return $service['Service']['name'];

        if (empty($service['Service']['parent_id']))
            return $service['Service']['name'];
        else
            return $this->_doList($service['Service']['parent_id']) . '/' . $service['Service']['name'];
    }

    /**
     * doListId Retourne toute la liste de services disponibles par rapport à un service donné
     * @param int $id
     * @return array Liste Id de tous les services disponibles
     */
    function doListId($id)
    {
        return $this->_doListId($id);
    }

    /**
     * _doListId Retourne la liste de services disponibles par rapport à un service donné. Fonction privée recursive
     * @param int $id
     * @return array Liste Id de tous les services disponibles
     */
    function _doListId($id)
    {
        $aArray = array();
        $service = $this->find('all', array(
            'conditions' => array('Service.parent_id' => $id),
            'fields' => array('id', 'parent_id'),
            'recursive' => -1));

        if (!empty($service)) {
            $aArray[] = $id;
            foreach ($service as $champs) {
                $aServices = $this->_doListId($champs['Service']['id']);
                if (!empty($aServices))
                    foreach ($aServices as $aService) {
                        $aArray[] = $aService;
                    }
            }
        } else
            $aArray[] = $id;
        return $aArray;
    }
    
    function desactive($id)
    {
        try {
            //Suppression du service
            $this->begin();
            $this->recursive=-1;
            $this->read(null, $id);
            $this->set('actif', false);
            $this->save();
            $this->commit();
            
        } catch (Exception $e) {
            $this->rollback();
            throw new Exception($e);
        }
        
        return true;
    }
    
    /**
     * fonction d'initialisation des variables de fusion pour le service émetteur d'un projet
     * les bibliothèques Gedooo doivent être inclues par avance
     * génère une exception en cas d'erreur
     * @param object_by_ref $oMainPart variable Gedooo de type maintPart du document à fusionner
     * @param object_by_ref $modelOdtInfos objet PhpOdtApi du fichier odt du modèle d'édition
     * @param integer $id id du modèle lié
     */
    function setVariablesFusion(&$aData, &$modelOdtInfos, $id) {
        if ($modelOdtInfos->hasUserFieldDeclared('service_emetteur'))
            $aData['service_emetteur'] = $this->field('name', array('id'=>$id));
        if ($modelOdtInfos->hasUserFieldDeclared('service_avec_hierarchie'))
            $aData['service_avec_hierarchie'] = $this->_doList($id);
    }
    
    public function parentNode() {
        return null;
    }
    
    public function parentNodeAlias() {
        if (!$this->id && empty($this->data)) {
        return null;
        }
        $data = $this->data;
        if (empty($this->data)) {
            $data = $this->read();
        }
        
        return array('Service' => array('alias' => $data['Service']['name']));
    }
}
