<?php
/**
 * WkfVisaFixture
 *
 */
class WkfVisaFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'traitement_id' => array('type' => 'integer', 'null' => false),
		'trigger_id' => array('type' => 'integer', 'null' => false),
		'signature_id' => array('type' => 'integer', 'null' => true),
		'etape_nom' => array('type' => 'string', 'null' => true, 'length' => 250),
		'etape_type' => array('type' => 'integer', 'null' => false),
		'action' => array('type' => 'string', 'null' => false, 'length' => 2),
		'commentaire' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'date' => array('type' => 'datetime', 'null' => true),
		'numero_traitement' => array('type' => 'integer', 'null' => false),
		'type_validation' => array('type' => 'string', 'null' => false, 'length' => 1),
		'etape_id' => array('type' => 'integer', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'wkf_visas_traitements' => array('unique' => false, 'column' => 'traitement_id')
		),
		'tableParameters' => array()
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'traitement_id' => 1,
			'trigger_id' => 1,
			'signature_id' => 1,
			'etape_nom' => 'Lorem ipsum dolor sit amet',
			'etape_type' => 1,
			'action' => '',
			'commentaire' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'date' => '2014-01-13 10:03:49',
			'numero_traitement' => 1,
			'type_validation' => 'Lorem ipsum dolor sit ame',
			'etape_id' => 1
		),
	);

}
