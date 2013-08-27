<?php
	/**
	 * Code source de la classe Models4Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses('AppController', 'Controller');
	CakePlugin::load( 'Database', array( 'bootstrap' => true ) );
	CakePlugin::load( 'Gedooo2', array( 'bootstrap' => true ) );
	App::uses( 'Gedooo2Builder', 'Gedooo2.Utility' );

	/**
	 * La classe Models4Controller ...
	 *
	 * @package app.Controller
	 */
	class Models4Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Models4';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array( 'Gedooo2.Gedooo2Debugger' );

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array();

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Collectivite',  'Seance' );

		/**
		 * Gestion des droits
		 *
		 * @var array
		 */
		public $aucunDroit = array( 'generer' );

        /**
         * Chemins récoltés:
         *  - (Collectivite).*
         *  - (Deliberationseance).position
         *  - (Deliberationseance).commentaire
         *  - (Theme).libelle
         *  - (Typeseance).action
         *  - Deliberation.*
         *  - Rapporteur.*
         *  - Redacteur.*
         *  - Seance.*
         *  - President.*
         *  - PresidentSuppleant.*
         *  - Secretaire.*
         *  - SecretaireSuppleant.*
         *  - Commentaires.{n}.*
         *
         * @param integer $deliberation_id
         * @param integer $model_id
         */
        protected function _genererDeliberation( $deliberation_id, $model_id ) {
            // 1°) Lecture des enregistrements
            $data = $this->Collectivite->gedoooRead( 1 );
            $data = Hash::merge( $data, $this->Seance->Deliberation->gedoooRead( $deliberation_id ) );

            // 2°) Normalisation des enregistrements
            $data = $this->Collectivite->gedoooNormalize( $data );
            $data = $this->Seance->Deliberation->gedoooNormalize( $data );

            // 3°) Préparation de l'objet GDO_PartType
            // TODO: grouper / comment ?
            $paths = $this->Collectivite->gedoooPaths();
            $paths = Hash::merge( $paths, $this->Seance->Deliberation->gedoooPaths() );

            $types = $this->Collectivite->gedoooTypes();
            $types = Hash::merge( $types, $this->Seance->Deliberation->gedoooTypes() );

			$Document = new GDO_PartType();
			$Document = Gedooo2Builder::main( $Document, $data, $types, $paths );

            if( !empty( $data['Historiques'] ) ) {
                $Document = Gedooo2Builder::iteration( $Document, 'Historique', $data['Historiques'], $types, $paths );
            }

            foreach( array_keys( $this->Seance->Deliberation->Listepresence->gedoooIterations ) as $iterationName ) {
                if( isset( $data[$iterationName] ) ) {
                    $Document = Gedooo2Builder::iteration( $Document, $iterationName, $data[$iterationName], $types, $paths );
                }
            }

			// 4°) Fusion
			$this->Gedooo2Debugger->toCsv( $Document );

            /*debug( $paths );
            debug( $types );*/

            // debug( Gedooo2Debugger::hashPathsToCsv( $Document ) );
            debug( Gedooo2Debugger::allPathsToCsv( $Document, true ) );

echo '<pre>';
echo '<p><strong>'.sprintf( '%s:%d', __FILE__, __LINE__ ).'</strong></p>';
print_r( $data );
echo '</pre>';
return;
        }

        /**
         *
         * @param integer $deliberation_id
         * @param integer $model_id
         */
        public function generer( $deliberation_id, $model_id ) {
            $this->_genererDeliberation( $deliberation_id, $model_id );
        }
	}
?>
