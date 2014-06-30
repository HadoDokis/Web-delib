<?php
App::uses('TdtMessage', 'Model');

/**
 * TdtMessage Test Case
 *
 */
class TdtMessageTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.tdt_message',
		'app.deliberation',
		'app.service',
		'app.user',
		'app.profil',
		'app.infosupdef',
		'app.infosup',
		'app.infosuplistedef',
		'app.infosupdefs_profil',
		'app.historique',
		'app.composition',
		'app.etape',
		'app.circuit',
		'app.traitement',
		'app.visa',
		'app.signature',
		'app.users_service',
		'app.circuits_user',
		'app.acteur',
		'app.typeacteur',
		'app.acteurs_service',
		'app.theme',
		'app.typeacte',
		'app.compteur',
		'app.sequence',
		'app.typeseance',
		'app.model',
		'app.typeseances_typeacte',
		'app.typeseances_typeacteur',
		'app.typeseances_acteur',
		'app.nature',
		'app.annex',
		'app.commentaire',
		'app.listepresence',
		'app.vote',
		'app.deliberationseance',
		'app.seance',
		'app.deliberations_seance',
		'app.deliberationtypeseance',
		'app.deliberations_typeseance'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->TdtMessage = ClassRegistry::init('TdtMessage');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->TdtMessage);

		parent::tearDown();
	}

/**
 * testIsNewMessage method
 *
 * @return void
 */
	public function testIsNewMessage() {
	}

}
