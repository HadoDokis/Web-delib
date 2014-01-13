<?php
class CakeflowTask extends Shell
{
    public $uses = array('Cakeflow.Visa', 'Cakeflow.Etape', 'Cakeflow.Traitement', 'Cakeflow.Circuit', 'Cakeflow.Composition');

    public function execute() {

    }

    /**
     * Fonction pour trouver les étapes correspondantes aux visas
     * afin de remplir le nouvel attribut etape_id de la table visa
     * pour cela on fait la jointure par le nom d'étape
     * Version 3.0.x => 3.1
     */
    public function findVisaEtapeId(){
        $traitements = $this->Traitement->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'circuit_id'),
            'conditions' => array('treated' => false)
        ));
        //Parcours des traitements non terminés
        foreach ($traitements as $traitement){
            $visas = $this->Visa->find('all', array(
                'recursive' => -1,
                'fields' => array('id', 'etape_nom', 'etape_type'),
                'conditions' => array('traitement_id' => $traitement['Traitement']['id'])
            ));
            //Parcours des visas du traitement
            foreach ($visas as $visa){
                $etape = $this->Etape->find('first', array(
                    'recursive' => -1,
                    'fields' => array('id'),
                    'conditions' => array(
                        'nom' => $visa['Visa']['etape_nom'],
                        'type' => $visa['Visa']['etape_type'],
                        'circuit_id' => $traitement['Traitement']['circuit_id'],
                    )
                ));
                //Assigner au visa le champ etape_id correspondant à l'étape liée (autrefois par nom)
                if (!empty($etape)){
                    $this->Visa->id = $visa['Visa']['id'];
                    $this->Visa->saveField('etape_id', $etape['Etape']['id']);
                }
            }
        }
    }

}