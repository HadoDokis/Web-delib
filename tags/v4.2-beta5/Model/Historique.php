<?php
class Historique extends AppModel {

    var $name = 'Historique';

    var $belongsTo = array(
                'Deliberation'=>array(
                        'className'    => 'Deliberation',
                        'conditions'   => '',
                        'order'        => '',
                        'dependent'    => false,
                        'foreignKey'   => 'delib_id'),
                'User'=>array(
                        'className'    => 'User',
                        'conditions'   => '',
                        'order'        => '',
                        'dependent'    => false,
                        'foreignKey'   => 'user_id')
			);

 
    function enregistre($delib_id, $user_id, $commentaire) {
        $this->create();
        $deliberation = $this->Deliberation->find('first', array('conditions' => array('Deliberation.id' => $delib_id),
                                                                 'fields'     => array('id', 'circuit_id'),
                                                                 'recursive'  => -1));
	$user = $this->User->find('first', array('conditions' => array('User.id' => $user_id),
                                                 'recursive'  => -1,
                                                  'fields'    => array('User.nom', 'User.prenom')));
        $histo['Historique']['user_id']     = $user_id;
        $histo['Historique']['circuit_id']  = $deliberation['Deliberation']['circuit_id'];
        $histo['Historique']['delib_id']    = $delib_id;
        $histo['Historique']['commentaire'] = "[".$user['User']['prenom'].' '.$user['User']['nom']."] $commentaire";
        return $this->save($histo); 
    }

    /**
     * fonction d'initialisation des variables de fusion pour l'historique d'un projet/délibération
     * les bibliothèques Gedooo doivent être inclues par avance
     * génère une exception en cas d'erreur
     * @param object_by_ref $oMainPart variable Gedooo de type maintPart du document à fusionner
     * @param object_by_ref $modelOdtInfos objet PhpOdtApi du fichier odt du modèle d'édition
     * @param integer $deliberationId id du projet/délibération
     */
    function setVariablesFusion(&$oMainPart, &$modelOdtInfos, $deliberationId) {
        //lecture de l'historique
        $historiques = $this->find('all', array(
            'recursive' => -1,
            'fields' => array('commentaire', 'created'),
            'conditions' => array('delib_id' => $deliberationId)));
        if (empty($historiques)) return;

        $oStyleIteration = new GDO_IterationType("Historique");
        foreach($historiques as $historique) {
            $oDevPart = new GDO_PartType();
            $oDevPart->addElement(new GDO_FieldType("log", $historique['Historique']['created'].' : '.$historique['Historique']['commentaire'], "text"));
            $oStyleIteration->addPart($oDevPart);
        }
        $oMainPart->addElement($oStyleIteration);
    }
}
?>
