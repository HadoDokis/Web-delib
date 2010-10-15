<?php

	require_once( dirname( __FILE__ ).'/../cake_app_model_test_case.php' );

	App::import('Model', 'Acteur');

	class ActeurTestCase extends CakeAppModelTestCase {
		
		public function testFind() {
			$expected = array(
				'Acteur' => array(
					'id' => '1',
					'typeacteur_id' => '1',
					'nom' => 'Test',
					'prenom' => 'Test',
					'salutation' => 'Monsieur',
					'titre' => null,
					'position' => '1',
					'date_naissance' => null,
					'adresse1' => null,
					'adresse2' => null,
					'cp' => null,
					'ville' => null,
					'email' => null,
					'telfixe' => null,
					'telmobile' => null,
					'note' => null,
					'created' => '2010-04-27 12:12:35',
					'modified' => '2010-04-27 12:12:35'
				)
			);
			$result = $this->Acteur->find('first',array('conditions'=>array('Acteur.id'=>1),'recursive'=>-1));
			$this->assertEqual($result,$expected);
		}
		
	}

?>
