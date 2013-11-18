<?php
/*
 * Created on 5 avr. 2011
 *
 * Librairie regroupant des fonctions hors MVC
 * utilisation en CakePhp 1.2 : require_once(APP.'libs'.DS.'tools.php');
 * utilisation en CakePhp 2.X : require_once(APP.'Lib'.DS.'tools.php');
 *
 */
 
 class AppTools {

/**
 * Ajout ou soustrait un délai (xs:duration) à une date
 * @param string $date date
 * @param string $duration délai sous la forme xs:duration
 * @param string $format format de sortie de la date
 * @param string $operateur 'add' pour l'ajout et 'sub' pour la soustraction du délai
 * @return string résultat formaté ou null en cas d'erreur
 * 
 */
function addSubDurationToDate($date, $duration, $format = 'Y-m-d', $operateur = 'add') {
	// initialisation
	$ret = null;
	try {
		$thisDate = new DateTime($date);
		$thisDuration = new DateInterval($duration);
		if ($operateur == 'add')
			$thisDate->add($thisDuration);
		elseif ($operateur == 'sub')
			$thisDate->sub($thisDuration);
		$ret = $thisDate->format($format);
	} catch (Exception $e) {
		debug('Fonction webdelib::addSubDurationToDate : '.$e->getMessage());
	}
	return $ret; 
}

/**
 * formate une date issue de la base de donnée
 * @param string $dateBD date issue de la lecture d'un enregistrement en base de données
 * @param string $format format de sortie utilisée par la fonction date()
 * @return string date mise en forme
 */
function timeFormat($dateBD, $format='Y-m-d') {
	if (empty($dateBD)) return '';
	$dateTime = strtotime($dateBD);
	return date($format, $dateTime);
}

/**
 * formate une xs:duration sous forme litérale
 * @param string $duration délai sous la forme xs:duration
 * @return string délai mise en forme litérale
 */
function durationToString($duration) {
	// initialisation
	$ret = array();

	if (empty($duration)) return '';

	$thisDuration = new DateInterval($duration);
	$annees = $thisDuration->y;
	$mois = $thisDuration->m;
	$jours = $thisDuration->d;
	$heures = $thisDuration->h;
	$minutes = $thisDuration->i;
	$secondes = $thisDuration->s;
	if ($annees==1) $ret[] = '1 an';
	elseif ($annees>1) $ret[] = $annees.' ans';
	if ($mois>0) $ret[] = $mois.' mois';
	if ($jours==1) $ret[] = '1 jour';
	elseif ($jours>1) $ret[] = $jours.' jours';
	if ($heures==1) $ret[] = '1 heure';
	elseif ($heures>1) $ret[] = $heures.' heures';
	if ($minutes==1) $ret[] = '1 minute';
	elseif ($minutes>1) $ret[] = $minutes.' minutes';
	if ($secondes==1) $ret[] = '1 seconde';
	elseif ($secondes>1) $ret[] = $secondes.' secondes';

	if (empty($ret))
		return '';
	else
		return implode(' et ', $ret);
}

/**
 * transforme une xs:duration sous forme de tableau
 * @param string $duration délai sous la forme xs:duration
 * @return array délai sous forme de tableau array('year', 'month', 'day', 'hour', 'minute', 'seconde')
 */
function durationToArray($duration) {
	// initialisation
	$ret = array('year'=>0, 'month'=>0, 'day'=>0, 'hour'=>0, 'minute'=>0, 'seconde'=>0);

	if (!empty($duration)) {
		$thisDuration = new DateInterval($duration);
		$ret['year'] = $thisDuration->y;
		$ret['month'] = $thisDuration->m;
		$ret['day'] = $thisDuration->d;
		$ret['hour'] = $thisDuration->h;
		$ret['minute'] = $thisDuration->i;
		$ret['seconde'] = $thisDuration->s;
	}

	return $ret;
}

/**
 * transforme une tableau (array('year', 'month', ...)) en xs:duration ('D1Y...')
 * @param array $duration délai sous forme de tableau array('year', 'month', 'day', 'hour', 'minute', 'seconde')
 * @return string délai sous la forme xs:duration
 */
function arrayToDuration($duration) {
	// initialisation
	$ret = $periode = $temps = '';
	$defaut = array('year'=>0, 'month'=>0, 'day'=>0, 'hour'=>0, 'minute'=>0, 'seconde'=>0);

	if (empty($duration) || !is_array($duration))
		return '';

	$duration = array_merge($defaut, $duration);
	if (!empty($duration['year'])) $periode .= $duration['year'].'Y'; 
	if (!empty($duration['month'])) $periode .= $duration['month'].'M'; 
	if (!empty($duration['day'])) $periode .= $duration['day'].'D'; 
	if (!empty($duration['hour'])) $temps .= $duration['hour'].'H'; 
	if (!empty($duration['minute'])) $temps .= $duration['minute'].'M'; 
	if (!empty($duration['seconde'])) $temps .= $duration['seconde'].'S'; 
	
	if (!empty($periode) || !empty($temps)) {
		$ret = 'P'.$periode;
		if (!empty($temps)) $ret .= 'T'.$temps;
	}

	return $ret;
}

function url_exists($url) {
    if (!$fp = curl_init($url)) return false;
    return true;
}

}?>
