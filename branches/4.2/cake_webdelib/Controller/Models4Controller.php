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
	App::uses( 'Gedooo2ConverterCloudooo', 'Gedooo2.Utility/Converter' );
//	App::uses( 'Gedooo2ConverterUnoconv', 'Gedooo2.Utility/Converter' );

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
		public $uses = array( 'Collectivite',  'Seance', 'Model' );

		/**
		 * Gestion des droits
		 *
		 * @var array
		 */
		public $aucunDroit = array( 'generer', 'test' );

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
            $this->log(sprintf("%s()<generation /%d/%d>", __METHOD__,$deliberation_id, $model_id),LOG_DEBUG);
            $total = 0;

            // 1°) Lecture des enregistrements
            $start = microtime(true);
            $data = $this->Collectivite->gedoooRead( 1 );
            $data = Hash::merge( $data, $this->Seance->Deliberation->gedoooRead( $deliberation_id ) );
            $total += microtime(true) - $start;
            $this->log(sprintf("%s()\tlecture des données: %.4fs", __METHOD__, microtime(true) - $start),LOG_DEBUG);

            // 2°) Normalisation des enregistrements
            $start = microtime(true);
            $data = $this->Collectivite->gedoooNormalize( $data );
            $data = $this->Seance->Deliberation->gedoooNormalize( $data );
            $total += microtime(true) - $start;
            $this->log(sprintf("%s()\tnormalisation des données: %.4fs", __METHOD__, microtime(true) - $start),LOG_DEBUG);

            //------------------------------------------------------------------

            // 3°) Récupération des méta-données (chemins, types) -> TODO: distinction statique/dynamiques ?
            $start = microtime(true);
            $paths = $this->Collectivite->gedoooPaths();
            $paths = Hash::merge( $paths, $this->Seance->Deliberation->gedoooPaths() );

            $types = $this->Collectivite->gedoooTypes();
            $types = Hash::merge( $types, $this->Seance->Deliberation->gedoooTypes() );
            $total += microtime(true) - $start;
            $this->log(sprintf("%s()\tlecture des méta-données: %.4fs", __METHOD__, microtime(true) - $start),LOG_DEBUG);

            // 4°) Préparation du document Gedooo -> TODO: une méthode dans le modèle ?
            $start = microtime(true);
			$Document = new GDO_PartType();
			$Document = Gedooo2Builder::main( $Document, $data, $types, $paths );

            /*if( !empty( $data['Historiques'] ) ) {
                $Document = Gedooo2Builder::iteration( $Document, 'Historique', $data['Historiques'], $types, $paths );
            }

            foreach( array_keys( $this->Seance->Deliberation->Listepresence->gedoooIterations ) as $iterationName ) {
                if( isset( $data[$iterationName] ) ) {
                    $Document = Gedooo2Builder::iteration( $Document, $iterationName, $data[$iterationName], $types, $paths );
                }
            }

            if( !empty( $data['Convoques'] ) ) {
                $Document = Gedooo2Builder::iteration( $Document, 'Convoques', $data['Convoques'], $types, $paths );
            }

            if( !empty( $data['AvisSeance'] ) ) {
                $Document = Gedooo2Builder::iteration( $Document, 'AvisSeance', $data['AvisSeance'], $types, $paths );
            }

            if( !empty( $data['Deliberations'] ) ) {
                $Document = Gedooo2Builder::iteration( $Document, 'Deliberations', $data['Deliberations'], $types, $paths );
            }
            else {
                $empty = array( array( 'Deliberation' => array( 'id' => null, 'objet' => null ) ) );
                $Document = Gedooo2Builder::iteration( $Document, 'Deliberations', $empty, $types, $paths );
            }

            // Annexes
            $Document->addElement( new GDO_FieldType( 'nombre_annexe', count($data['Annexes']), 'text' ) );
            if( !empty( $data['Annexes'] ) ) {
                $Annexes = new GDO_IterationType( 'Annexes' );

                foreach( $data['Annexes'] as $annexe ) {
                        $Annexe = new GDO_PartType();

                        $Annexe->addElement(new GDO_FieldType('titre_annexe', $annexe['Annex']['titre'], 'text'));
                        $Annexe->addElement(new GDO_FieldType('nom_fichier', $annexe['Annex']['filename'], 'text'));

                        if (($annexe['Annex']['filetype'] == "application/vnd.oasis.opendocument.text")) {
                            $Annexe->addElement(new GDO_ContentType('fichier', $annexe['Annex']['filename'], 'application/vnd.oasis.opendocument.text', 'binary', $annexe['Annex']['data']));
                        } elseif (($annexe['Annex']['filetype'] == "application/pdf") && !empty($annexe['Annex']['data'])) {
                            $Annexe->addElement(new GDO_ContentType('fichier', $annexe['Annex']['filename'], 'application/vnd.oasis.opendocument.text', 'binary', $annexe['Annex']['data']));
                        }

                        $Annexes->addPart( $Annexe );
                }
                $Document->addElement( $Annexes );
            }

            // FIXME: suffixe _seances et pas _seance
            if( !empty( $data['Seances'] ) ) {
                $Seances = new GDO_IterationType( 'Seances' );

                // Données de la séance -> TODO: $types['Seances'], $paths['Seances']
                foreach( $data['Seances'] as $seance ) {
                        $Seance = new GDO_PartType();

                        $Seance = Gedooo2Builder::main( $Seance, $seance, $types, $paths );

                        if( !empty( $seance['AvisSeance'] ) ) {
                            $Seance = Gedooo2Builder::iteration( $Seance, 'AvisSeance', $seance['AvisSeance'], $types, array( 'commentaire' => 'Deliberationseance.commentaire' ) );
                        }

                        if( !empty( $seance['Convoques'] ) ) {
                            $Seance = Gedooo2Builder::iteration( $Seance, 'Convoques', $seance['Convoques'], $types, $paths );
                        }

                        $Seances->addPart( $Seance );
                }
                $Document->addElement( $Seances );
            }

            // TODO: Début normalisation
            if (Configure::read('GENERER_DOC_SIMPLE')) {
                $contents = array(
                    array('target' => 'texte_projet', 'name' => 'texte_projet.odt', 'path' => 'Deliberation.texte_projet'),
                    array('target' => 'note_synthese', 'name' => 'texte_synthese.odt', 'path' => 'Deliberation.texte_synthese'),
                    array('target' => 'texte_deliberation', 'name' => 'deliberation.odt', 'path' => 'Deliberation.deliberation'),
                    array('target' => 'debat_deliberation', 'name' => 'debat.odt', 'path' => 'Deliberation.debat'),
                    array('target' => 'debat_commission', 'name' => 'commission.odt', 'path' => 'Deliberation.commission'),
                );
            } else {
                $contents = array(
                    array('target' => 'texte_projet', 'name' => 'text_projet.odt', 'path' => 'Deliberation.texte_projet'),
                    array('target' => 'texte_deliberation', 'name' => 'td.odt', 'path' => 'Deliberation.deliberation'),
                    array('target' => 'note_synthese', 'name' => 'ns.odt', 'path' => 'Deliberation.texte_synthese'),
                    array('target' => 'debat_deliberation', 'name' => 'debat.odt', 'path' => 'Deliberation.debat'),
                    array('target' => 'debat_commission', 'name' => 'debat_commission.odt', 'path' => 'Deliberation.commission'),
                );
            }

            Configure::write(
                'Gedooo2.Gedooo2ConverterCloudooo',
                array(
                    'server' => Configure::read('CLOUDOOO_HOST'),
                    'port' => Configure::read('CLOUDOOO_PORT')
                )
            );

            foreach( $contents as $content ) {
                $value = Hash::get( $data, $content['path'] );

                if( Configure::read('GENERER_DOC_SIMPLE') ) {
                    $value = Gedooo2ConverterCloudooo::convert( $value, 'pdf', 'odt' );
                }

                if( !empty( $value ) ) {
                    $Element = new GDO_ContentType(
                        $content['target'],
                        $content['name'],
                        'application/vnd.oasis.opendocument.text',
                        'binary',
                        $value
                    );
                }
                else {
                    $Element = new GDO_FieldType(
                        $content['target'],
                        $value,
                        'text'
                    );
                }

                $Document->addElement( $Element );
            }*/
            // TODO: Fin normalisation
            $total += microtime(true) - $start;
            $this->log(sprintf("%s()\tconstruction Gedooo: %.4fs", __METHOD__, microtime(true) - $start),LOG_DEBUG);

            // -----------------------------------------------------------------

			// 5°) Fusion -> FIXME
            $start = microtime(true);
			$this->Gedooo2Debugger->toCsv( $Document );
            $total += microtime(true) - $start;
            $this->log(sprintf("%s()\texport CSV: %.4fs", __METHOD__, microtime(true) - $start),LOG_DEBUG);

            $this->log(sprintf("%s()</generation /%d/%d: %.4fs>", __METHOD__,$deliberation_id, $model_id,$total),LOG_DEBUG);

            // -----------------------------------------------------------------
            // FIXME: ce sont des tests basiques
            // -----------------------------------------------------------------
            /*$sMimeType = 'application/vnd.oasis.opendocument.text';

            $model = $this->Model->find(
                'first',
                array(
                    'fields' => array(
                        'name',
                        'content',
                        'joindre_annexe'
                    ),
                    'conditions' => array('Model.id' => $model_id),
                    'recursive' => -1,
                )
            );

            $Template = new GDO_ContentType( "", $model['Model']['name'], "application/vnd.oasis.opendocument.text", "binary", $model['Model']['content']);
            $Fusion = new GDO_FusionType($Template, $sMimeType, $Document);
            $Fusion->process();

            // $success = ( $Fusion->getCode() == 'OK' );

            $content = $Fusion->getContent();

            header("Content-type: {$sMimeType}");
            header("Content-Disposition: attachment; filename=\"test.odt\"");
            header("Content-Length: " . strlen($content->binary));
            die($content->binary);*/

            // TODO: concaténer les annexes PDF à la fin du document

//            $allPathsToCsv = Gedooo2Debugger::allPathsToCsv( $Document, true );
            $allPathsToCsv = Gedooo2Debugger::hashPathsToCsv( $Document );
echo '<pre>';
echo '<p><strong>'.sprintf( '%s:%d', __FILE__, __LINE__ ).'</strong></p>';
print_r( $allPathsToCsv );
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
