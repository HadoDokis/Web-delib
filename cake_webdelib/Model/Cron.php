<?php
class Cron extends AppModel {

var $name = 'Cron';

var $tablePrefix = '';

// constantes de la classe
// status d'exécution
const EXECUTION_STATUS_SUCCES = 'SUCCES';
const EXECUTION_STATUS_WARNING = 'WARNING';
const EXECUTION_STATUS_FAILED = 'FAILED';
// status sous forme de chaines de caractères a insérer dans le rapport d'exécution des procédures appelées par les crons
const MESSAGE_FIN_EXEC_SUCCES = 'TRAITEMENT_TERMINE_OK';
const MESSAGE_FIN_EXEC_WARNING = 'TRAITEMENT_TERMINE_ALERTE';
const MESSAGE_FIN_EXEC_ERROR = 'TRAITEMENT_TERMINE_ERREUR';
// nombre de tentative avec erreur, délais en seconde avant une nouvelle tentative, délais de nouvelle essai suite à une erreur
const TENTATIVES_NB = 3;
const TENTATIVES_DELAIS_SECONDES = 10;
const NOUVEL_ESSAI_DELAIS_DURATION = 'PT30M'; // toutes les 30 minutes

var $belongsTo = array(
	'CreatedUser' => array(
		'className' => 'User',
		'foreignKey' => 'created_user_id'),
	'ModifiedUser' => array(
		'className' => 'User',
		'foreignKey' => 'modified_user_id')
	);

function beforeSave($options = array()) {
	if (isset($this->data[$this->alias]['execution_duration']) && is_array($this->data[$this->alias]['execution_duration'])) {
		require_once(APP.'Lib'.DS.'tools.php');
		$this->data[$this->alias]['execution_duration'] = AppTools::arrayToDuration($this->data[$this->alias]['execution_duration']);
	}
	return true;
}

/**
 * retourne le libéllé du statut d'exécution
 */
function libelleStatus($status) {
	switch ($status) {
	    case self::EXECUTION_STATUS_SUCCES:
    	    $libelle = '<span class="label label-success" title="Opération exécutée avec succès"><i class="icon-ok-sign"></i> '.__('Exécutée avec succès', true).'</span>';
        	break;
	    case self::EXECUTION_STATUS_WARNING:
    	    $libelle = '<span class="label label-warning" title="Avertissement(s) détecté(s) lors de l\'exécution, voir les détails de la tâche"><i class="icon-info-sign"></i> '.__('Exécutée, en alerte', true).'</span>';
        	break;
	    case self::EXECUTION_STATUS_FAILED:
    	    $libelle = '<span class="label label-important" title="Erreur(s) détectée(s) lors de l\'exécution, voir les détails de la tâche"><i class="icon-warning-sign"></i> '.__('Non exécutée : erreur', true).'</span>';
        	break;
        default:
    	    $libelle = '<span class="label" title="Statut indéfini, la tâche a t-elle déjà été exécutée ?"><i class="icon-question-sign"></i> '.__('Indéfini', true).'</span>';
	}
	return $libelle;
}

/**
 * Retourne le libellé correspondant à l'état 'active'
 */
function libelleActive($active) {
	return $active ? __('Oui', true) : __('Non', true);
}

/**
 * Retourne la forme litérale de 'execution_duration'
 */
function libelleDuration($duration) {
	require_once(APP.'Lib'.DS.'tools.php');
	return AppTools::durationToString($duration);
}

/**
 * retourne la prochaine date d'exécution pour le cron $cron
 * @param integer||array $idOuData id ou tableau de données de l'enregistrement
 * @return string date-heure de la prochaine exécution sous forme 'Y-m-d H:i:s'
 */
function calcNextExecutionTime($idOuData) {
	// initialisations
	$now = date('Y-m-d H:i:s');

	// lecture en base ou initialisation de l'archive
	if (is_array($idOuData))
		$data = $idOuData;
	else
		$data = $this->find('first', array(
			'recursive'=>-1,
			'conditions' => array('id'=>$idOuData)));

	if (empty($data['Cron']['next_execution_time']))
		$nextExecutionTime = $now;
	else
		$nextExecutionTime = $data['Cron']['next_execution_time'];

	require_once(APP.'Lib'.DS.'tools.php');
	while($nextExecutionTime <= $now) {
		$nextExecutionTime = AppTools::addSubDurationToDate($nextExecutionTime, $data['Cron']['execution_duration'], 'Y-m-d H:i:s');
	}

	return $nextExecutionTime;
}

} ?>