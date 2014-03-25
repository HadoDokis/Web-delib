<?php 
class AppSchema extends CakeSchema {

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $acos = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'alias' => array('type' => 'string', 'null' => false),
		'lft' => array('type' => 'integer', 'null' => true),
		'rght' => array('type' => 'integer', 'null' => true),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => '0'),
		'model' => array('type' => 'string', 'null' => true),
		'foreign_key' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'acos_idx1' => array('unique' => false, 'column' => array('lft', 'rght')),
			'acos_idx2' => array('unique' => false, 'column' => 'alias'),
			'acos_idx3' => array('unique' => false, 'column' => array('model', 'foreign_key')),
			'acos_leftright' => array('unique' => false, 'column' => array('lft', 'rght')),
			'lft' => array('unique' => false, 'column' => 'lft')
		),
		'tableParameters' => array()
	);
	public $acteurs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'typeacteur_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'nom' => array('type' => 'string', 'null' => false, 'length' => 50),
		'prenom' => array('type' => 'string', 'null' => false, 'length' => 50),
		'salutation' => array('type' => 'string', 'null' => false, 'length' => 50),
		'titre' => array('type' => 'string', 'null' => true, 'length' => 250),
		'position' => array('type' => 'integer', 'null' => false),
		'date_naissance' => array('type' => 'date', 'null' => true),
		'adresse1' => array('type' => 'string', 'null' => false, 'length' => 100),
		'adresse2' => array('type' => 'string', 'null' => false, 'length' => 100),
		'cp' => array('type' => 'string', 'null' => false, 'length' => 20),
		'ville' => array('type' => 'string', 'null' => false, 'length' => 100),
		'email' => array('type' => 'string', 'null' => false, 'length' => 100),
		'telfixe' => array('type' => 'string', 'null' => true, 'length' => 20),
		'telmobile' => array('type' => 'string', 'null' => true, 'length' => 20),
		'suppleant_id' => array('type' => 'integer', 'null' => true),
		'note' => array('type' => 'string', 'null' => false),
		'actif' => array('type' => 'boolean', 'null' => false, 'default' => true),
		'created' => array('type' => 'datetime', 'null' => false),
		'modified' => array('type' => 'datetime', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'acteurs_actif' => array('unique' => false, 'column' => 'actif'),
			'index' => array('unique' => false, 'column' => 'id'),
			'suppleant_id' => array('unique' => false, 'column' => 'suppleant_id'),
			'typeacteur' => array('unique' => false, 'column' => 'typeacteur_id')
		),
		'tableParameters' => array()
	);
	public $acteurs_seances = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'acteur_id' => array('type' => 'integer', 'null' => false),
		'seance_id' => array('type' => 'integer', 'null' => false),
		'mail_id' => array('type' => 'integer', 'null' => false),
		'date_envoi' => array('type' => 'datetime', 'null' => false),
		'date_reception' => array('type' => 'datetime', 'null' => true),
		'model' => array('type' => 'string', 'null' => false, 'length' => 20),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);
	public $acteurs_services = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'acteur_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'service_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'acteursservices_acteur_id' => array('unique' => false, 'column' => 'acteur_id'),
			'acteursservices_service_id' => array('unique' => false, 'column' => 'service_id')
		),
		'tableParameters' => array()
	);
	public $ados = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'alias' => array('type' => 'string', 'null' => false),
		'lft' => array('type' => 'integer', 'null' => true),
		'rght' => array('type' => 'integer', 'null' => true),
		'parent_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'model' => array('type' => 'string', 'null' => true),
		'foreign_key' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);
	public $annexes = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'model' => array('type' => 'string', 'null' => false),
		'foreign_key' => array('type' => 'integer', 'null' => false),
		'joindre_ctrl_legalite' => array('type' => 'boolean', 'null' => false),
		'joindre_fusion' => array('type' => 'boolean', 'null' => false),
		'titre' => array('type' => 'string', 'null' => false, 'length' => 200),
		'filename' => array('type' => 'string', 'null' => false, 'length' => 100),
		'filetype' => array('type' => 'string', 'null' => false),
		'size' => array('type' => 'integer', 'null' => false),
		'data' => array('type' => 'binary', 'null' => false),
		'data_pdf' => array('type' => 'binary', 'null' => true),
		'created' => array('type' => 'datetime', 'null' => true),
		'modified' => array('type' => 'datetime', 'null' => true),
		'edition_data' => array('type' => 'binary', 'null' => true),
		'edition_data_typemime' => array('type' => 'text', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'annexes_joindre' => array('unique' => false, 'column' => array('foreign_key', 'joindre_fusion')),
			'model_foreign_key' => array('unique' => false, 'column' => array('model', 'foreign_key'))
		),
		'tableParameters' => array()
	);
	public $aros = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'foreign_key' => array('type' => 'integer', 'null' => true),
		'alias' => array('type' => 'string', 'null' => false),
		'lft' => array('type' => 'integer', 'null' => true),
		'rght' => array('type' => 'integer', 'null' => true),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => '0'),
		'model' => array('type' => 'string', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'alias' => array('unique' => false, 'column' => 'alias'),
			'alias_2' => array('unique' => false, 'column' => 'alias'),
			'aros_idx1' => array('unique' => false, 'column' => array('lft', 'rght')),
			'aros_idx2' => array('unique' => false, 'column' => 'alias'),
			'aros_idx3' => array('unique' => false, 'column' => array('model', 'foreign_key')),
			'aros_leftright' => array('unique' => false, 'column' => array('lft', 'rght')),
			'parent_id' => array('unique' => false, 'column' => 'parent_id'),
			'rght' => array('unique' => false, 'column' => 'rght')
		),
		'tableParameters' => array()
	);
	public $aros_acos = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'aro_id' => array('type' => 'integer', 'null' => false),
		'aco_id' => array('type' => 'integer', 'null' => false),
		'_create' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 2),
		'_read' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 2),
		'_update' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 2),
		'_delete' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 2),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'aco_id' => array('unique' => false, 'column' => 'aco_id')
		),
		'tableParameters' => array()
	);
	public $aros_ados = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'aro_id' => array('type' => 'integer', 'null' => false),
		'ado_id' => array('type' => 'integer', 'null' => false),
		'_create' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 2),
		'_read' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 2),
		'_update' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 2),
		'_delete' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 2),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'ado_id' => array('unique' => false, 'column' => 'ado_id'),
			'aro_id' => array('unique' => false, 'column' => 'aro_id')
		),
		'tableParameters' => array()
	);
	public $circuits_users = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'circuit_id' => array('type' => 'integer', 'null' => false),
		'user_id' => array('type' => 'integer', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'user_id' => array('unique' => false, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);
	public $collectivites = array(
		'id' => array('type' => 'integer', 'null' => false, 'length' => 11, 'key' => 'primary'),
		'id_entity' => array('type' => 'integer', 'null' => true),
		'nom' => array('type' => 'string', 'null' => false, 'length' => 30),
		'adresse' => array('type' => 'string', 'null' => false),
		'CP' => array('type' => 'integer', 'null' => false),
		'ville' => array('type' => 'string', 'null' => false),
		'telephone' => array('type' => 'string', 'null' => false, 'length' => 20),
		'logo' => array('type' => 'binary', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);
	public $commentaires = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'delib_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'agent_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'texte' => array('type' => 'string', 'null' => true, 'length' => 1000),
		'pris_en_compte' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'commentaire_auto' => array('type' => 'boolean', 'null' => true),
		'created' => array('type' => 'datetime', 'null' => false),
		'modified' => array('type' => 'datetime', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);
	public $compteurs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'nom' => array('type' => 'string', 'null' => false),
		'commentaire' => array('type' => 'string', 'null' => false),
		'def_compteur' => array('type' => 'string', 'null' => false),
		'sequence_id' => array('type' => 'integer', 'null' => false),
		'def_reinit' => array('type' => 'string', 'null' => false),
		'val_reinit' => array('type' => 'string', 'null' => true),
		'created' => array('type' => 'datetime', 'null' => false),
		'modified' => array('type' => 'datetime', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);
	public $crons = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'nom' => array('type' => 'string', 'null' => false),
		'description' => array('type' => 'string', 'null' => true, 'default' => null),
		'plugin' => array('type' => 'string', 'null' => true),
		'action' => array('type' => 'string', 'null' => false),
		'has_params' => array('type' => 'boolean', 'null' => true),
		'params' => array('type' => 'string', 'null' => true, 'default' => null),
		'next_execution_time' => array('type' => 'datetime', 'null' => true),
		'execution_duration' => array('type' => 'string', 'null' => true),
		'last_execution_start_time' => array('type' => 'datetime', 'null' => true),
		'last_execution_end_time' => array('type' => 'datetime', 'null' => true),
		'last_execution_report' => array('type' => 'text', 'null' => true, 'length' => 1073741824),
		'last_execution_status' => array('type' => 'string', 'null' => true),
		'active' => array('type' => 'boolean', 'null' => true),
		'created' => array('type' => 'datetime', 'null' => false),
		'created_user_id' => array('type' => 'integer', 'null' => false),
		'modified' => array('type' => 'datetime', 'null' => false),
		'modified_user_id' => array('type' => 'integer', 'null' => false),
		'model' => array('type' => 'text', 'null' => true, 'default' => 'CronJob'),
		'lock' => array('type' => 'boolean', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);
	public $deliberations = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'typeacte_id' => array('type' => 'integer', 'null' => false, 'default' => '1'),
		'circuit_id' => array('type' => 'integer', 'null' => true, 'default' => '0'),
		'theme_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'service_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'redacteur_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'rapporteur_id' => array('type' => 'integer', 'null' => true, 'default' => '0'),
		'anterieure_id' => array('type' => 'integer', 'null' => true),
		'is_multidelib' => array('type' => 'boolean', 'null' => true),
		'parent_id' => array('type' => 'integer', 'null' => true),
		'objet' => array('type' => 'string', 'null' => false, 'length' => 1000),
		'objet_delib' => array('type' => 'string', 'null' => true, 'length' => 1000),
		'titre' => array('type' => 'string', 'null' => true, 'length' => 1000),
		'num_delib' => array('type' => 'string', 'null' => true, 'length' => 15),
		'num_pref' => array('type' => 'string', 'null' => true),
		'pastell_id' => array('type' => 'string', 'null' => true, 'length' => 10),
		'tdt_id' => array('type' => 'text', 'null' => true),
		'tdt_dateAR' => array('type' => 'string', 'null' => true, 'length' => 100),
		'texte_projet' => array('type' => 'binary', 'null' => true),
		'texte_projet_name' => array('type' => 'string', 'null' => true, 'length' => 75),
		'texte_projet_type' => array('type' => 'string', 'null' => true),
		'texte_projet_size' => array('type' => 'integer', 'null' => true),
		'texte_synthese' => array('type' => 'binary', 'null' => true),
		'texte_synthese_name' => array('type' => 'string', 'null' => true, 'length' => 75),
		'texte_synthese_type' => array('type' => 'string', 'null' => true),
		'texte_synthese_size' => array('type' => 'integer', 'null' => true),
		'deliberation' => array('type' => 'binary', 'null' => true),
		'deliberation_name' => array('type' => 'string', 'null' => true, 'length' => 75),
		'deliberation_type' => array('type' => 'string', 'null' => true),
		'deliberation_size' => array('type' => 'integer', 'null' => true),
		'date_limite' => array('type' => 'date', 'null' => true),
		'date_envoi' => array('type' => 'datetime', 'null' => true),
		'etat' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'parapheur_etat' => array('type' => 'integer', 'null' => true),
		'parapheur_commentaire' => array('type' => 'string', 'null' => true, 'length' => 1000),
		'sae_etat' => array('type' => 'boolean', 'null' => true),
		'reporte' => array('type' => 'boolean', 'null' => false),
		'montant' => array('type' => 'integer', 'null' => true),
		'debat' => array('type' => 'binary', 'null' => true),
		'debat_name' => array('type' => 'string', 'null' => true),
		'debat_size' => array('type' => 'integer', 'null' => true),
		'debat_type' => array('type' => 'string', 'null' => true),
		'avis' => array('type' => 'integer', 'null' => true),
		'created' => array('type' => 'datetime', 'null' => false),
		'modified' => array('type' => 'datetime', 'null' => false),
		'vote_nb_oui' => array('type' => 'integer', 'null' => true),
		'vote_nb_non' => array('type' => 'integer', 'null' => true),
		'vote_nb_abstention' => array('type' => 'integer', 'null' => true),
		'vote_nb_retrait' => array('type' => 'integer', 'null' => true),
		'vote_commentaire' => array('type' => 'string', 'null' => true, 'length' => 1000),
		'delib_pdf' => array('type' => 'binary', 'null' => true),
		'signature' => array('type' => 'binary', 'null' => true),
		'signee' => array('type' => 'boolean', 'null' => false),
		'commission' => array('type' => 'binary', 'null' => true),
		'commission_size' => array('type' => 'integer', 'null' => true),
		'commission_type' => array('type' => 'string', 'null' => true),
		'commission_name' => array('type' => 'string', 'null' => true),
		'date_acte' => array('type' => 'datetime', 'null' => true),
		'date_envoi_signature' => array('type' => 'datetime', 'null' => true),
		'parapheur_id' => array('type' => 'string', 'null' => true, 'length' => 50),
		'tdt_data_pdf' => array('type' => 'binary', 'null' => true),
		'tdt_data_bordereau_pdf' => array('type' => 'binary', 'null' => true),
		'president_id' => array('type' => 'integer', 'null' => true),
		'parapheur_cible' => array('type' => 'text', 'null' => true),
		'parapheur_bordereau' => array('type' => 'binary', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'etat' => array('unique' => false, 'column' => 'etat'),
			'nature_id' => array('unique' => false, 'column' => 'typeacte_id'),
			'parent' => array('unique' => false, 'column' => 'parent_id'),
			'rapporteur_id' => array('unique' => false, 'column' => 'rapporteur_id'),
			'redacteur_id' => array('unique' => false, 'column' => 'redacteur_id'),
			'service_id' => array('unique' => false, 'column' => 'service_id'),
			'theme_id' => array('unique' => false, 'column' => 'theme_id')
		),
		'tableParameters' => array()
	);
	public $deliberations_seances = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'deliberation_id' => array('type' => 'integer', 'null' => false),
		'seance_id' => array('type' => 'integer', 'null' => false),
		'position' => array('type' => 'integer', 'null' => true),
		'avis' => array('type' => 'boolean', 'null' => true),
		'commentaire' => array('type' => 'string', 'null' => true, 'length' => 1000),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'deliberations_id' => array('unique' => false, 'column' => 'deliberation_id'),
			'deliberations_seances_seance' => array('unique' => false, 'column' => 'seance_id'),
			'seances_id' => array('unique' => false, 'column' => 'seance_id')
		),
		'tableParameters' => array()
	);
	public $deliberations_typeseances = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'deliberation_id' => array('type' => 'integer', 'null' => false),
		'typeseance_id' => array('type' => 'integer', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);
	public $historiques = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'delib_id' => array('type' => 'integer', 'null' => false),
		'user_id' => array('type' => 'integer', 'null' => false),
		'circuit_id' => array('type' => 'integer', 'null' => true),
		'commentaire' => array('type' => 'text', 'null' => false, 'length' => 1073741824),
		'modified' => array('type' => 'datetime', 'null' => false),
		'created' => array('type' => 'datetime', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'user' => array('unique' => false, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);
	public $infosupdefs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'model' => array('type' => 'string', 'null' => false, 'default' => 'Deliberation', 'length' => 25),
		'nom' => array('type' => 'string', 'null' => false),
		'commentaire' => array('type' => 'string', 'null' => false),
		'ordre' => array('type' => 'integer', 'null' => false),
		'code' => array('type' => 'string', 'null' => false),
		'type' => array('type' => 'string', 'null' => false),
		'val_initiale' => array('type' => 'string', 'null' => true, 'length' => 1000),
		'recherche' => array('type' => 'boolean', 'null' => false),
		'created' => array('type' => 'datetime', 'null' => false),
		'modified' => array('type' => 'datetime', 'null' => false),
		'actif' => array('type' => 'boolean', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'model' => array('unique' => false, 'column' => 'model')
		),
		'tableParameters' => array()
	);
	public $infosupdefs_profils = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'profil_id' => array('type' => 'integer', 'null' => false),
		'infosupdef_id' => array('type' => 'integer', 'null' => false),
		'indexes' => array(
			
		),
		'tableParameters' => array()
	);
	public $infosuplistedefs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'infosupdef_id' => array('type' => 'integer', 'null' => false),
		'ordre' => array('type' => 'integer', 'null' => false),
		'nom' => array('type' => 'string', 'null' => false),
		'actif' => array('type' => 'boolean', 'null' => false, 'default' => true),
		'created' => array('type' => 'datetime', 'null' => false),
		'modified' => array('type' => 'datetime', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'INFOSUPDEF_ID_ORDRE' => array('unique' => false, 'column' => array('infosupdef_id', 'ordre'))
		),
		'tableParameters' => array()
	);
	public $infosups = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'model' => array('type' => 'string', 'null' => false, 'default' => 'Deliberation', 'length' => 25),
		'foreign_key' => array('type' => 'integer', 'null' => false),
		'infosupdef_id' => array('type' => 'integer', 'null' => true),
		'text' => array('type' => 'string', 'null' => true, 'length' => 1000),
		'date' => array('type' => 'date', 'null' => true),
		'file_name' => array('type' => 'string', 'null' => true),
		'file_size' => array('type' => 'integer', 'null' => true),
		'file_type' => array('type' => 'string', 'null' => true),
		'content' => array('type' => 'binary', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'foreign_key' => array('unique' => false, 'column' => 'foreign_key'),
			'infosupdef_id' => array('unique' => false, 'column' => 'infosupdef_id'),
			'text' => array('unique' => false, 'column' => 'text')
		),
		'tableParameters' => array()
	);
	public $listepresences = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'delib_id' => array('type' => 'integer', 'null' => false),
		'acteur_id' => array('type' => 'integer', 'null' => false),
		'present' => array('type' => 'boolean', 'null' => false),
		'mandataire' => array('type' => 'integer', 'null' => true),
		'suppleant_id' => array('type' => 'integer', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);
	public $natures = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'libelle' => array('type' => 'string', 'null' => false, 'length' => 100),
		'code' => array('type' => 'string', 'null' => false, 'length' => 3),
		'dua' => array('type' => 'string', 'null' => true, 'length' => 50),
		'sortfinal' => array('type' => 'string', 'null' => true, 'length' => 50),
		'communicabilite' => array('type' => 'string', 'null' => true, 'length' => 50),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);
	public $nomenclatures = array(
		'id' => array('type' => 'text', 'null' => false, 'length' => 11, 'key' => 'primary'),
		'parent_id' => array('type' => 'text', 'null' => false, 'default' => '0'),
		'libelle' => array('type' => 'text', 'null' => false),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => '0'),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => '0'),
		'created' => array('type' => 'datetime', 'null' => false),
		'modified' => array('type' => 'datetime', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);
	public $profils = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => '0'),
		'libelle' => array('type' => 'string', 'null' => false, 'length' => 100),
		'actif' => array('type' => 'boolean', 'null' => false, 'default' => true),
		'created' => array('type' => 'datetime', 'null' => false),
		'modified' => array('type' => 'datetime', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);
	public $seances = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'type_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'created' => array('type' => 'datetime', 'null' => false),
		'modified' => array('type' => 'datetime', 'null' => false),
		'date_convocation' => array('type' => 'datetime', 'null' => true),
		'date' => array('type' => 'datetime', 'null' => false),
		'traitee' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'commentaire' => array('type' => 'string', 'null' => true, 'length' => 500),
		'secretaire_id' => array('type' => 'integer', 'null' => true),
		'president_id' => array('type' => 'integer', 'null' => true),
		'debat_global' => array('type' => 'binary', 'null' => true),
		'debat_global_name' => array('type' => 'string', 'null' => true, 'length' => 75),
		'debat_global_size' => array('type' => 'integer', 'null' => true),
		'debat_global_type' => array('type' => 'string', 'null' => true),
		'pv_figes' => array('type' => 'integer', 'null' => true),
		'pv_sommaire' => array('type' => 'binary', 'null' => true),
		'pv_complet' => array('type' => 'binary', 'null' => true),
		'numero_depot' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'idelibre_id' => array('type' => 'text', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'seances_traitee' => array('unique' => false, 'column' => 'traitee'),
			'type_id' => array('unique' => false, 'column' => 'type_id')
		),
		'tableParameters' => array()
	);
	public $sequences = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'nom' => array('type' => 'string', 'null' => false),
		'commentaire' => array('type' => 'string', 'null' => false),
		'num_sequence' => array('type' => 'integer', 'null' => false),
		'created' => array('type' => 'datetime', 'null' => false),
		'modified' => array('type' => 'datetime', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);
	public $services = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => '0'),
		'order' => array('type' => 'string', 'null' => false, 'length' => 50),
		'libelle' => array('type' => 'string', 'null' => false, 'length' => 100),
		'circuit_defaut_id' => array('type' => 'integer', 'null' => false),
		'actif' => array('type' => 'boolean', 'null' => false, 'default' => true),
		'created' => array('type' => 'datetime', 'null' => false),
		'modified' => array('type' => 'datetime', 'null' => false),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => '0'),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);
	public $tdt_messages = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'delib_id' => array('type' => 'integer', 'null' => false),
		'message_id' => array('type' => 'integer', 'null' => false),
		'type_message' => array('type' => 'integer', 'null' => false),
		'type_reponse' => array('type' => 'integer', 'null' => true),
		'created' => array('type' => 'datetime', 'null' => false),
		'modified' => array('type' => 'datetime', 'null' => false),
		'date_message' => array('type' => 'date', 'null' => true),
		'data' => array('type' => 'binary', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'tdtmsg_' => array('unique' => false, 'column' => 'delib_id')
		),
		'tableParameters' => array()
	);
	public $themes = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => '0'),
		'order' => array('type' => 'string', 'null' => false, 'length' => 50),
		'libelle' => array('type' => 'string', 'null' => true, 'length' => 500),
		'actif' => array('type' => 'boolean', 'null' => false, 'default' => true),
		'created' => array('type' => 'datetime', 'null' => false),
		'modified' => array('type' => 'datetime', 'null' => false),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => '0'),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'themes_left' => array('unique' => false, 'column' => 'lft')
		),
		'tableParameters' => array()
	);
	public $typeactes = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'libelle' => array('type' => 'text', 'null' => false, 'length' => 1073741824),
		'modeleprojet_id' => array('type' => 'integer', 'null' => false),
		'modelefinal_id' => array('type' => 'integer', 'null' => false),
		'nature_id' => array('type' => 'integer', 'null' => false),
		'compteur_id' => array('type' => 'integer', 'null' => false),
		'created' => array('type' => 'date', 'null' => false),
		'modified' => array('type' => 'date', 'null' => false),
		'gabarit_projet' => array('type' => 'binary', 'null' => true),
		'gabarit_synthese' => array('type' => 'binary', 'null' => true),
		'gabarit_acte' => array('type' => 'binary', 'null' => true),
		'teletransmettre' => array('type' => 'boolean', 'null' => true, 'default' => true),
		'gabarit_acte_name' => array('type' => 'text', 'null' => true),
		'gabarit_projet_name' => array('type' => 'text', 'null' => true),
		'gabarit_synthese_name' => array('type' => 'text', 'null' => true),
		'indexes' => array(
			'typeactes_id_key' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);
	public $typeacteurs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'nom' => array('type' => 'string', 'null' => false),
		'commentaire' => array('type' => 'string', 'null' => false),
		'elu' => array('type' => 'boolean', 'null' => false),
		'created' => array('type' => 'datetime', 'null' => false),
		'modified' => array('type' => 'datetime', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'elu' => array('unique' => false, 'column' => 'elu')
		),
		'tableParameters' => array()
	);
	public $typeseances = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'libelle' => array('type' => 'string', 'null' => false, 'length' => 100),
		'retard' => array('type' => 'integer', 'null' => true, 'default' => '0'),
		'action' => array('type' => 'integer', 'null' => false),
		'compteur_id' => array('type' => 'integer', 'null' => false),
		'modelprojet_id' => array('type' => 'integer', 'null' => false),
		'modeldeliberation_id' => array('type' => 'integer', 'null' => false),
		'modelconvocation_id' => array('type' => 'integer', 'null' => false),
		'modelordredujour_id' => array('type' => 'integer', 'null' => false),
		'modelpvsommaire_id' => array('type' => 'integer', 'null' => false),
		'modelpvdetaille_id' => array('type' => 'integer', 'null' => false),
		'created' => array('type' => 'datetime', 'null' => false),
		'modified' => array('type' => 'datetime', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);
	public $typeseances_acteurs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'typeseance_id' => array('type' => 'integer', 'null' => false),
		'acteur_id' => array('type' => 'integer', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id')
		),
		'tableParameters' => array()
	);
	public $typeseances_typeactes = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'typeseance_id' => array('type' => 'integer', 'null' => false),
		'typeacte_id' => array('type' => 'integer', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'typeseancenature_nature' => array('unique' => false, 'column' => 'typeacte_id')
		),
		'tableParameters' => array()
	);
	public $typeseances_typeacteurs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'typeseance_id' => array('type' => 'integer', 'null' => false),
		'typeacteur_id' => array('type' => 'integer', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'typeseance_id' => array('unique' => false, 'column' => array('typeseance_id', 'typeacteur_id'))
		),
		'tableParameters' => array()
	);
	public $users = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'profil_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'statut' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'login' => array('type' => 'string', 'null' => false, 'length' => 50),
		'note' => array('type' => 'string', 'null' => true, 'length' => 300),
		'circuit_defaut_id' => array('type' => 'integer', 'null' => true),
		'password' => array('type' => 'string', 'null' => false, 'length' => 100),
		'nom' => array('type' => 'string', 'null' => false, 'length' => 50),
		'prenom' => array('type' => 'string', 'null' => false, 'length' => 50),
		'email' => array('type' => 'string', 'null' => false),
		'telfixe' => array('type' => 'string', 'null' => true, 'length' => 20),
		'telmobile' => array('type' => 'string', 'null' => true, 'length' => 20),
		'date_naissance' => array('type' => 'date', 'null' => true),
		'accept_notif' => array('type' => 'boolean', 'null' => true),
		'mail_refus' => array('type' => 'boolean', 'null' => false),
		'mail_traitement' => array('type' => 'boolean', 'null' => false),
		'mail_insertion' => array('type' => 'boolean', 'null' => false),
		'position' => array('type' => 'integer', 'null' => true),
		'created' => array('type' => 'datetime', 'null' => false),
		'modified' => array('type' => 'datetime', 'null' => false),
		'mail_modif_projet_cree' => array('type' => 'boolean', 'null' => true),
		'mail_modif_projet_valide' => array('type' => 'boolean', 'null' => true),
		'mail_retard_validation' => array('type' => 'boolean', 'null' => true),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'login' => array('unique' => true, 'column' => 'login')
		),
		'tableParameters' => array()
	);
	public $users_services = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'service_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'users_services_users' => array('unique' => false, 'column' => 'user_id')
		),
		'tableParameters' => array()
	);
	public $votes = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary'),
		'acteur_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'delib_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'resultat' => array('type' => 'integer', 'null' => true),
		'created' => array('type' => 'datetime', 'null' => false),
		'modified' => array('type' => 'datetime', 'null' => false),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'acteur_id' => array('unique' => false, 'column' => 'acteur_id'),
			'deliberation_id' => array('unique' => false, 'column' => 'delib_id')
		),
		'tableParameters' => array()
	);
}
