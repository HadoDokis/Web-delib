<?php
/**
 * Tache de mise à jour vers webdelib 4.2
 *
 * Recopie l'attribut president_id des séances délibérantes vers l'attribut des délibérations
 */
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('Seance', 'Model');
App::uses('Deliberation', 'Model');

class CopyPresidentIdTask extends Shell {

    public function execute() {
        $this->Seance = new Seance;
        $this->Deliberation = new Deliberation;
        $this->Seance->Behaviors->load('Containable');
        $seances = $this->Seance->find('all', array(
            'fields' => array('Seance.id', 'Seance.president_id'),
            'conditions' => array(
                "not" => array("Seance.president_id" => null)
            ),
            'contain' => array(
                'Deliberation' => array(
                    'conditions' => array(
                        'Deliberation.etat >' => 2,
                        "Deliberation.president_id is null"
                    ),
                    'fields' => array('Deliberation.id', 'Deliberation.president_id')
                )
            )
        ));
        foreach ($seances as $seance) {
            if (!empty($seance['Seance']['president_id'])){
                foreach ($seance['Deliberation'] as $deliberation) {
                    if (empty($deliberation['president_id'])){
                        $this->Deliberation->id = $deliberation['id'];
                        $this->Deliberation->saveField('president_id', $seance['Seance']['president_id']);
                    }
                }
            }
        }
    }
}