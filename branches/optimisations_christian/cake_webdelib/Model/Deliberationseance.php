<?php
class Deliberationseance extends AppModel {
    var $name = 'Deliberationseance';
    var $useTable = 'deliberations_seances';
    var $belongsTo = array('Deliberation', 'Seance');

    // ---------------------------------------------------------------------

        /**
         * Lecture des passages en "Seance" d'une "Délibération".
         *
         * @param integer $deliberation_id
         * @return array
         */
        public function gedoooRead( $deliberation_id, $deep = true ) {
            $results = $this->find(
                'all',
                array(
                    'fields' => array_merge(
                        $this->fields(),
                        $this->Seance->fields(),
                        $this->Seance->President->fields(),
                        alias_querydata( $this->Seance->President->Suppleant->fields(), array( 'Suppleant' => 'PresidentSuppleant' ) ),
                        $this->Seance->Secretaire->fields(),
                        alias_querydata( $this->Seance->Secretaire->Suppleant->fields(), array( 'Suppleant' => 'SecretaireSuppleant' ) ),
                        $this->Seance->Typeseance->fields() // Délibérante: Typeseance::actionVote
                    ),
                    'recursive' => -1,
                    'joins' => array(
                        $this->join( 'Seance' ),
						$this->Seance->join( 'President', array( 'type' => 'LEFT OUTER' ) ),
						alias_querydata( $this->Seance->President->join( 'Suppleant' ), array( 'Suppleant' => 'PresidentSuppleant' ) ),
						$this->Seance->join( 'Secretaire' ),
						alias_querydata( $this->Seance->Secretaire->join( 'Suppleant' ), array( 'Suppleant' => 'SecretaireSuppleant' ) ),
						$this->Seance->join( 'Typeseance', array( 'type' => 'LEFT OUTER' ) ),
                    ),
                    'conditions' => array(
                        'Deliberationseance.deliberation_id' => $deliberation_id
                    ),
                    'order' => 'Deliberationseance.position ASC',
                )
            );

            if( $deep && !empty( $results ) ) {
                foreach( $results as $i => $result ) {
                    // Informations supplémentaires de la séance
                    $result['Infossups'] = $this->Seance->Infosup->gedoooReadAll( 'Seance', $result['Seance']['id'] );

                    $results[$i] = $result;
                }
            }

            return $results;
        }
}
?>
