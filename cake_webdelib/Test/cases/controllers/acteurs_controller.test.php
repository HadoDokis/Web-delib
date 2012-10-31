<?php

	require_once( dirname( __FILE__ ).'/../cake_app_controller_test_case.php' );

	App::import('Controller', 'Acteurs');

	class TestActeursController extends ActeursController {

		public $autoRender = false;
		public $redirectUrl;
		public $redirectStatus;
		public $renderedAction;
		public $renderedLayout;
		public $renderedFile;
		public $stopped;
		public $name='Acteurs';

		public function redirect($url, $status = null, $exit = true) {
			$this->redirectUrl = $url;
			$this->redirectStatus = $status;
		}

		public function render($action = null, $layout = null, $file = null) {
			$this->renderedAction = $action;
			$this->renderedLayout = (is_null($layout) ? $this->layout : $layout);
			$this->renderedFile = $file;
		}

		public function _stop($status = 0) {
			$this->stopped = $status;
		}

		public function assert( $condition, $error = 'error500', $parameters = array() ) {
			$this->condition = $condition;
			$this->error = $error;
			$this->parameters = $parameters;
		}
		
	}

	class ActeursControllerTest extends CakeAppControllerTestCase {
		
		public function testIndex() {
			$this->ActeursController->index();
			$result=$this->ActeursController->viewVars['acteurs'];
			$expected=array(
				0 => array (
					'Acteur' => array (
						'id' => 1,
						'typeacteur_id' => 1,
						'nom' => 'Test',
						'prenom' => 'Test',
						'salutation' => 'Monsieur',
						'titre' => '',
						'position' => 1,
						'date_naissance' => '',
						'adresse1' => '',
						'adresse2' => '',
						'cp' => '',
						'ville' => '',
						'email' => '',
						'telfixe' => '',
						'telmobile' => '',
						'note' => '',
						'created' => '2010-04-27 12:12:35',
						'modified' => '2010-04-27 12:12:35'
					),
					'Typeacteur' => array (
						'id' => 1,
						'nom' => 'Elu(s) majorité',
						'commentaire' => '',
						'elu' => 1,
						'created' => '2010-04-27 12:12:27',
						'modified' => '2010-04-27 12:12:27'
					),
					'Service' => array (
						0 => array (
							'id' => 1,
							'parent_id' => 0,
							'order' => '',
							'libelle' => 'Défaut',
							'circuit_defaut_id' => 1,
							'actif' => 1,
							'created' => '2009-04-06 08:35:48',
							'modified' => '2010-05-03 17:06:58',
							'ActeurService' => array(
								'id' => 1,
								'acteur_id' => 1,
						        'service_id' => 1
							)
						)
					)
				),
				1 => array (
					'Acteur' => array (
						'id' => 2,
						'typeacteur_id' => 1,
						'nom' => 'Selenium',
						'prenom' => 'Selenium',
						'salutation' => 'Madame',
						'titre' => '',
						'position' => 2,
						'date_naissance' => '',
						'adresse1' => '',
						'adresse2' => '',
						'cp' => '',
						'ville' => '',
						'email' => '',
						'telfixe' => '',
						'telmobile' => '',
						'note' => '',
						'created' => '2010-04-27 12:12:41',
						'modified' => '2010-04-27 12:12:41'
					),
					'Typeacteur' => array (
						'id' => 1,
						'nom' => 'Elu(s) majorité',
						'commentaire' => '',
						'elu' => 1,
						'created' => '2010-04-27 12:12:27',
						'modified' => '2010-04-27 12:12:27'
					),
					'Service' => array (
						0 => array (
							'id' => 1,
							'parent_id' => 0,
							'order' => '',
							'libelle' => 'Défaut',
							'circuit_defaut_id' => 1,
							'actif' => 1,
							'created' => '2009-04-06 08:35:48',
							'modified' => '2010-05-03 17:06:58',
							'ActeurService' => array(
								'id' => 2,
								'acteur_id' => 2,
						        'service_id' => 1
							)
						)
					)
				)
			);
			$this->assertEqual($result,$expected);
		}
		
		public function testDelete() {
			$this->ActeursController->delete(1);
			$this->assertEqual('/acteurs/index',$this->ActeursController->redirectUrl);
		}
		
	}

?>
