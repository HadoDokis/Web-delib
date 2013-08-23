<?php
App::uses( 'DateFrench', 'Utility' );

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

    // -------------------------------------------------------------------------
		/**
		 * Lecture de l'enregistrement
		 *
		 * @return array
		 */
		public function gedoooRead( $id ) {
			return $this->find(
				'first',
				array(
					'conditions' => array( 'Collectivite.id' => $id ),
					'recursive' => -1
				)
			);
        }

		/**
		 * Normalisation d'un enregistrement: ajout des valeurs calculées, ...
		 *
		 * @param array $record
		 * @return array
		 */
		public function gedoooNormalize( array $record ) {
            if( !empty( $record ) ) {
                $record['Collectivite']['date_jour_courant'] = DateFrench::frenchDate(strtotime("now"));
                $record['Collectivite']['date_du_jour'] = date("d/m/Y", strtotime("now"));
            }

            return $record;
        }

		/**
		 * Retourne une correspondance entre les champs CakePHP (même calculés)
		 * et les champs Gedooo.
		 *
		 * @param array $records
		 * @return array
		 */
		public function gedoooPaths() {
			return array(
                'nom_collectivite' => 'Collectivite.nom',
                'adresse_collectivite' => 'Collectivite.adresse',
                'cp_collectivite' => 'Collectivite.CP',
                'ville_collectivite' => 'Collectivite.ville',
                'telephone_collectivite' => 'Collectivite.telephone',
                'date_jour_courant' => 'Collectivite.date_jour_courant',
                'date_du_jour' => 'Collectivite.date_du_jour',
           );
        }

		/**
		 * Retourne une correspondance entre les champs CakePHP (même calculés)
		 * et les types Gedooo.
		 *
		 * @param array $records
		 * @return array
		 */
		public function gedoooTypes() {
			return array_merge(
				$this->types(),
                array(
                    'Collectivite.date_jour_courant' => 'string',
                    'Collectivite.date_du_jour' => 'date',
                )
            );
        }
}
?>
