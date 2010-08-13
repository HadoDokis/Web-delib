<?php

	if( !defined( 'CAKEPHP_UNIT_TEST_EXECUTION' ) ) {
		define( 'CAKEPHP_UNIT_TEST_EXECUTION', 1 );
	}

	ClassRegistry::config(array('ds' => 'test_suite'));

	class CakeAppTestCase extends CakeTestCase {

		/**
		* Tables de données à utiliser
		*/

		public $fixtures = array (
			'app.typeseance',
			'app.service',
			'app.listepresence',
			'app.aro_aco',
			'app.user',
			'app.deliberation',
			'app.user_circuit',
			'app.seance',
			'app.sequence',
			'app.acteur',
			'app.user_service',
			'app.commentaire',
			'app.infosuplistedef',
			'app.typeacteur',
			'app.infosup',
			'app.typeseance_typeacteur',
			'app.acteur_service',
			'app.aco',
			'app.theme',
			'app.compteur',
			'app.model',
			'app.profil',
			'app.typeseance_acteur',
			'app.collectivite',
			'app.circuit',
			'app.vote',
			'app.infosupdef',
			'app.acteur_service',
			'app.annex',
			'app.aro',
			'app.traitement',
			'app.historique',
		);

		function startCase() { Cache::clear(); clearCache(); }

	}

?>