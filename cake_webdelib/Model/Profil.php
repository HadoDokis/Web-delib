<?php

class Profil extends AppModel {

    public $name = 'Profil';
    //public $displayField = "name";
    public $validate = array(
        'name' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Entrer le libellé.'
            )
        )
    );
    public $belongsTo = array(
        'ProfilParent' => array(
            'className' => 'Profil',
            'foreignKey' => 'parent_id'
        ),
    );
    public $hasMany = array(
        'ProfilEnfant' => array(
            'className' => 'Profil',
            'foreignKey' => 'parent_id'
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'profil_id'
        )
    );
    public $hasAndBelongsToMany = array('Infosupdef');
    
    public $actsAs = array('AuthManager.AclManager' => array('type' => 'both'));
    
    public function parentNode() {
        if (!$this->id && empty($this->data)) {
        return null;
        }
        $data = $this->data;
        if (empty($this->data)) {
            $data = $this->read();
        }
        if (empty($data['Profil']['profil_id'])) {
            return null;
        }
        
        return array('Profil' => array('id' => $data['Profil']['profil_id']));
    }
    
    public function parentNodeAlias() {
        if (!$this->id && empty($this->data)) {
        return null;
        }
        $data = $this->data;
        if (empty($this->data)) {
            $data = $this->read();
        }
        
        return array('Profil' => array('alias' => $data['Profil']['name']));
    }

}
