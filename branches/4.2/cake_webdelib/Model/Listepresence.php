<?php
class Listepresence extends AppModel {

	var $name = 'Listepresence';
	var	$cacheQueries = false;
	var $belongsTo = array(
			'Deliberation' =>
				array('className' => 'Deliberation',
						'foreignKey' => 'delib_id',
						'conditions' => '',
						'fields' => '',
						'order' => '',
						'counterCache' => ''
				),
			'Acteur' =>
				array('className' => 'Acteur',
						'foreignKey' => 'acteur_id',
						'conditions' => '',
						'fields' => '',
						'order' => '',
						'counterCache' => ''
				),
                        'Suppleant' =>
				array('className' => 'Acteur',
						'foreignKey' => 'suppleant_id',
						'conditions' => '',
						'fields' => '',
						'order' => '',
						'counterCache' => ''
				),
			'Mandataire' =>
				array('className' => 'Acteur',
						'foreignKey' => 'mandataire',
						'conditions' => '',
						'fields' => '',
						'order' => '',
						'counterCache' => ''
				)

	);


        // ---------------------------------------------------------------------

        /**
         * Lecture des enregistrements liés à une délibération.
         *
         * @param integer $deliberation_id
         * @todo: deep pour les votes ?
         * @return array
         */
		public function gedoooReadAll( $deliberation_id ) {
            $joinParamsVote = array( 'conditions' => array( 'Vote.delib_id' => $deliberation_id ), 'type' => 'LEFT OUTER' );

			$results = $this->find(
				'all',
				array(
					'fields' => array_merge(
						$this->fields(),
						$this->Acteur->fields(),
						$this->Mandataire->fields(),
						$this->Suppleant->fields(),
                        // Types d'acteurs
                        alias_querydata( $this->Acteur->Typeacteur->fields(), array( 'Typeacteur' => 'TypeacteurActeur' ) ),
                        alias_querydata( $this->Mandataire->Typeacteur->fields(), array( 'Typeacteur' => 'TypeacteurMandataire' ) ),
                        alias_querydata( $this->Suppleant->Typeacteur->fields(), array( 'Typeacteur' => 'TypeacteurSuppleant' ) ),
                        // Votes // FIXME: votes de la délibération
						alias_querydata( $this->Acteur->Vote->fields(), array( 'Vote' => 'VoteActeur' ) ),
						alias_querydata( $this->Mandataire->Vote->fields(), array( 'Vote' => 'VoteMandataire' ) ),
						alias_querydata( $this->Suppleant->Vote->fields(), array( 'Vote' => 'VoteSuppleant' ) )
					),
					'conditions' => array(
						'Listepresence.delib_id' => $deliberation_id,
//						'VoteActeur.resultat' => array( 2, 3, 4, 5 ) // TODO: à vérifier
					),
					'joins' => array(
						$this->join( 'Acteur' ),
						$this->join( 'Mandataire' ),
						$this->join( 'Suppleant' ),
                        // Types d'acteurs
                        alias_querydata( $this->Acteur->join( 'Typeacteur' ), array( 'Typeacteur' => 'TypeacteurActeur' ) ),
                        alias_querydata( $this->Mandataire->join( 'Typeacteur' ), array( 'Typeacteur' => 'TypeacteurMandataire' ) ),
                        alias_querydata( $this->Suppleant->join( 'Typeacteur' ), array( 'Typeacteur' => 'TypeacteurSuppleant' ) ),
                        // Votes // FIXME: votes de la délibération
						alias_querydata( $this->Acteur->join( 'Vote', $joinParamsVote ), array( 'Vote' => 'VoteActeur' ) ),
						alias_querydata( $this->Mandataire->join( 'Vote', $joinParamsVote ), array( 'Vote' => 'VoteMandataire' ) ),
						alias_querydata( $this->Suppleant->join( 'Vote', $joinParamsVote ), array( 'Vote' => 'VoteSuppleant' ) ),
					),
					'recursive' => -1,
					'order' => array( 'VoteActeur.resultat ASC', 'Acteur.position ASC' )
				)
			);

			return $results;
		}
}
?>
