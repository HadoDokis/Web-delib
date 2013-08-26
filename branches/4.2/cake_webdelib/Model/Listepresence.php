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

		/**
		 * Liste des noms d'itérations pour les votes, ainsi que les suffixes à
		 * utiliser.
		 *
		 * ?? Projets.{n}.ActeursSansParticipation.{n}.nombre_acteur_sans_participation
		 *
		 * @var array
		 */
		public $gedoooIterations = array(
			// 1. Présences
			'ActeursPresents' => 'present', // nom_acteur_present
			'ActeursAbsents' => 'absent', // nom_acteur_absent, nom_acteur_mandate
			// 2. Mandaté ?
			'ActeursMandates' => 'mandate', // nom_acteur_mandataire, nom_acteur_mandate
			// 3. Votes
			'ActeursContre' => 'contre', // nom_acteur_contre, nom_acteur_mandate
			'ActeursPour' => 'pour', // nom_acteur_pour, nom_acteur_mandate
			'ActeursAbstention' => 'abstention', // nom_acteur_abstention, nom_acteur_mandate
			'ActeursSansParticipation' => 'sans_participation', // nom_acteur_sans_participation, nom_acteur_mandate
		);

		/**
		 * Normalisation des enregistrement: ajout des valeurs calculées, ...
		 *
		 * @param array $records
		 * @return array
		 */
		public function gedoooNormalizeList( array $records ) {
			$votes = array_fill_keys( $this->gedoooIterations, array() );
			$counts = array();

			// Classement par catégorie
			foreach( $records as $record ) {
				$item = array(
					'Listepresence' => $record['Listepresence'],
					'Acteur' => $record['Acteur'],
					'VoteActeur' => $record['VoteActeur'],
					'Mandataire' => $record['Mandataire'],
					'VoteMandataire' => $record['VoteMandataire'],
					'Suppleant' => $record['Suppleant'],
					'VoteSuppleant' => $record['VoteSuppleant'],
				);

				// Mandate
				// TODO: que faire dans ce cas-là ?
				if( $record['Listepresence']['mandataire'] ) {
					$votes['mandate'][] = $item;
				}

				// Présences
				if( $record['Listepresence']['present'] ) {
					$votes['present'][] = $item;
				}
				else {
					$votes['absent'][] = $item;
				}

				// Votes
				// TODO: les autres si besoin
				if( $record['VoteActeur']['resultat'] == Vote::voteContre ) {
					$votes['contre'][] = $item;
				}
				else if( $record['VoteActeur']['resultat'] == Vote::votePour ) {
					$votes['pour'][] = $item;
				}
				else if( $record['VoteActeur']['resultat'] == Vote::abstention ) {
					$votes['abstention'][] = $item;
				}
				else if( $record['VoteActeur']['resultat'] == Vote::sansParticipation ) {
					$votes['sans_participation'][] = $item;
				}
			}

			// Transformation pour le retour
			$return = array();
			foreach( $this->gedoooIterations as $iterationName => $category ) {
				$return[$iterationName] = $this->Acteur->gedoooNormalizeList( $category, $votes[$category] );
			}

			return $return;
		}
}
?>
