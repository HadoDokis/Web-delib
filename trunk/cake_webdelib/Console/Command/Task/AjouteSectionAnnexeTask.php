<?php
/**
 * Tache de mise à jour vers webdelib 4.2
 *
 * Ajoute une section annexe en fin de document
 * pour chaque document de la table models (Webdelib < 4.2)
 * qui possèdent l'attribut joindre_annexe
 */
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('Model', 'Model');
App::uses('phpOdtApi', 'ModelOdtValidator.Lib');
class AjouteSectionAnnexeTask extends Shell {
    
    public function execute() {
        $this->Model = new Model;

        $models = $this->Model->find('all', array(
            'recursive' => -1,
            'conditions' => array('joindre_annexe' => true),
        ));

        //Cocher Annexes.joindre_fusion si aucun modèle n'a joindre annexe
        if (!empty($models)){
            $this->Annex->updateAll( array('joindre_fusion' => true), array() );
        }

        foreach ($models as $model){
            $lib = new phpOdtApi();
            $lib->loadFromOdtBin($model['Model']['content'], 'w', true);
            if (!$lib->hasUserFieldsInSection('fichier', 'Annexes')){
                $this->out('<info>Modification du model '.$model['Model']['id'].' ('. $model['Model']['name'] .")</info>\n", 0);
                $lib->appendUserField('fichier', 'string', 'Annexes');
                $this->Model->id = $model['Model']['id'];
                $this->Model->saveField('content', $lib->save(true));
                $this->Model->saveField('joindre_annexe', 0);
            }
        }
    }
}