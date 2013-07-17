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
}
?>
