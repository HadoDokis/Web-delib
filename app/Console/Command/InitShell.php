<?php

class InitShell extends AppShell {

    public $uses = array('Service', 'User', 'Profil', 'Aco', 'Aro');

    public function main() {
        Configure::write('debug', 1);
       
        App::uses('AppShell', 'Console/Command');
        App::uses('ComponentCollection', 'Controller');
        App::uses('MenuComponent', 'Controller/Component');
        $collection = new ComponentCollection();
        $this->Menu =new MenuComponent($collection);

	$droits = $this->Menu->listeAliasMenuControlleur();
        foreach ($droits as $droit) {
            $this->Aco->create();
            $parent = $this->Aco->find('first', array('conditions' => array('Aco.alias' => $droit['alias']),
                                                      'recursive'  => -1));
            if (!empty($parent))
                $data['Aco']['parent_id'] = $parent['Aco']['id'];
            $data['Aco']['alias'] = $droit['alias'];
            $this->Aco->save();
        }
        
        $this->Service->create();
        $data['Service']['parent_id'] = 0;
        $data['Service']['order'] = 'A';
        $data['Service']['libelle'] = "Initialisation";
        $data['Service']['circuit_defaut_id'] = 0;
        $data['Service']['actif'] = true;
        $this->Service->save($data);
        $data['Service']['id'] =   $this->Service->getLastInsertId();

        $this->Profil->create();
        $data['Profil']['parent_id'] = 0;
        $data['Profil']['libelle'] = "Adminis";
        $data['Profil']['actif'] = true;
        $this->Profil->save($data);
        $data['Profil']['id'] = $this->Profil->getLastInsertId();

        $this->Aro->create();
        $data['Aro']['foreign_key'] = $data['Profil']['id'] ;
        $data['Aro']['Model']       = 'Profil';
        $data['Aro']['alias']       = $data['Profil']['libelle'];
        $data['Aro']['parent_id']   = 0;
        $this->Aro->save();

        $this->User->create();       
        $data['User']['profil_id']  =   $data['Profil']['id'];
        $data['User']['note']       = "Utilisateur créé par le script d'initialisation"; 
        $data['User']['statut']      = 0; 
        $data['User']['login']      = 'tortue'; 
        $data['User']['password']   = 'admin'; 
        $data['User']['password2']  = 'admin'; 
        $data['User']['nom'] = 'Administrateur'; 
        $data['User']['prenom'] = 'Webdelib'; 
        $data['User']['accept_notif'] = false;
        $data['User']['mail_refus'] = false;
        $data['User']['mail_traitement'] = false;
        $data['User']['mail_insertion'] = false;
        $data['Service']['Service'] =  $data['Service'];

        $this->User->save($data);
        $data['User']['id'] =  $this->User->getLastInsertId();
            
        $this->Dbdroits->MajCruDroits(
                       array('model'=>'User', 'foreign_key'=> $data['User']['id'], 'alias'=>$this->data['User']['login']),
                       array('model'=>'Profil','foreign_key'=>$this->data['User']['profil_id']),
                       1 );
  
    }

}


?>
