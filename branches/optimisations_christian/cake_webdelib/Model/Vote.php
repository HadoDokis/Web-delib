<?php
	class Vote extends AppModel {

    /**
     * Définition de constantes nommées pour le champ resultat.
     */
    const voteContre = 2;
    const votePour = 3;
    const abstention = 4;
    const sansParticipation = 5;

		var $name = 'Vote';
		var $belongsTo = array(
			'Acteur' => array('className' => 'Acteur',
				'foreignKey' => 'acteur_id',
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'counterCache' => ''
			)
		);
	}
?>
