<?php
/**
 * AnnexFixture
 *
 */
class AnnexFixture extends CakeTestFixture {

/**
 * Import
 *
 * @var array
 */
	public $import = array('model' => 'Annex');

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 215,
			'model' => 'Projet',
			'foreign_key' => 341,
			'joindre_ctrl_legalite' => 1,
			'titre' => 'Annexe 1',
			'filename' => 'ADULLACT Commande_IN140006_259863.pdf',
			'filetype' => 'application/pdf',
			'size' => 501044,
			'data' => '',
			'filename_pdf' => null,
			'data_pdf' => null,
			'created' => '2014-01-28 14:44:54',
			'modified' => '2014-01-28 14:44:54',
			'joindre_fusion' => 1,
			'edition_data' => null,
                        'edition_data_typemime' => null
		),
                array(
			'id' => 216,
			'model' => 'Projet',
			'foreign_key' => 341,
			'joindre_ctrl_legalite' => 0,
			'titre' => 'Annexe 1',
			'filename' => 'ADULLACT Commande_IN140006_259863.pdf',
			'filetype' => 'application/pdf',
			'size' => 501044,
			'data' => '',
			'filename_pdf' => null,
			'data_pdf' => null,
			'created' => '2014-01-28 14:44:54',
			'modified' => '2014-01-28 14:44:54',
			'joindre_fusion' => 0,
			'edition_data' => null,
                        'edition_data_typemime' => null
		),
	);
}
