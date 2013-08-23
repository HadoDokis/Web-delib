<?php
class Collectivite extends AppModel {
	var $name = 'Collectivite';
	var $cacheSources = 'false';
	
	var $validate = array(
		'nom' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrer le nom de la collectivité'
			)
		),
		'adresse' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrer l\'adresse.'
			)
		),
		'CP' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrer le code postal.'
			)
		),
		'ville' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrer la ville.'
			)
		),
		'telephone' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrer le numéro de téléphone.'
			)
		)
	);

        /**
         * Données Gedooo :
         *  - nom_collectivite/collectivite.nom/text
         *  - adresse_collectivite/collectivite.adresse/text
         *  - cp_collectivite/collectivite.CP/text
         *  - ville_collectivite/collectivite.ville/text
         *  - telephone_collectivite/collectivite.telephone/text
         * 
         * @param &GDO_PartType $oMainPart adresse de l'objet GDO_PartType à remplir
         * @param type $collectivite_id l'identifiant de la collectivité en base
         */
       function makeBalise(&$oMainPart, $collectivite_id) {
            $collectivite = $this->find('first',
                                         array('conditions' => array($this->alias.'.id' => $collectivite_id),
                                               'recursive'  => -1));
        
            $oMainPart->addElement(new GDO_FieldType('nom_collectivite', $collectivite['Collectivite']['nom'], "text"));
            $oMainPart->addElement(new GDO_FieldType('adresse_collectivite', $collectivite['Collectivite']['adresse'], "text"));
            $oMainPart->addElement(new GDO_FieldType('cp_collectivite', $collectivite['Collectivite']['CP'], "text"));
            $oMainPart->addElement(new GDO_FieldType('ville_collectivite', $collectivite['Collectivite']['ville'], "text"));
            $oMainPart->addElement(new GDO_FieldType('telephone_collectivite', $collectivite['Collectivite']['telephone'], "text"));
    }

      
}
?>
