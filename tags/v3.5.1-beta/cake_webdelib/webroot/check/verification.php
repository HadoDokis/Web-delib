<?php
// initalisations
$fichier_conf = 'webdelib.inc';

$versionCakePHPAttendue = "1.2.10";
$versionPHPAttendue = "5.2.4";
$mods_apache = array('mod_rewrite', 'mod_dav', 'mod_dav_fs');
$exts_php    = array('soap', 'curl', 'dom');
$appIniFiles = array('database.php', 'webdelib.inc');

// redéfinition des constantes principales de cake (un rep au dessus par rapport aux constantes cake)
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(dirname(dirname(__FILE__)))));
define('APP_DIR', basename(dirname(dirname(dirname(__FILE__)))));
define('APP', ROOT.DS.APP_DIR.DS);
define('CONFIGS', APP.'config'.DS);
define('CAKE_CORE_INCLUDE_PATH', ROOT);
define('WEBROOT_DIR', basename(dirname(dirname(__FILE__))));
define('WWW_ROOT', dirname(dirname(__FILE__)) . DS);
if (function_exists('ini_set') && ini_set('include_path', CAKE_CORE_INCLUDE_PATH . PATH_SEPARATOR . ROOT . DS . APP_DIR . DS . PATH_SEPARATOR . ini_get('include_path'))) {
	define('APP_PATH', null);
	define('CORE_PATH', null);
} else {
	define('APP_PATH', ROOT . DS . APP_DIR . DS);
	define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
}
if (!defined('E_DEPRECATED'))
	define('E_DEPRECATED', 8192);
$appli_path = ROOT.DS;

// constantes pour la recherche
define('FICHIER_NON_TROUVE', 'CONSTANTE_FICHIER_NON_TROUVE');
define('NON_TROUVE', 'CONSTANTE_NON_TROUVE');

// chargement du fichier asalae.ini.php si il existe
if (file_exists($appli_path.'app'.DS.'config'.DS.$fichier_conf))
	include_once($appli_path.'app'.DS.'config'.DS.$fichier_conf);

/**
 * fonctions utilisées par le script
 */
function d($textContent, $classAttribute=null) {
	t('div', $textContent, $classAttribute);
}
function t($tagName, $textContent, $classAttribute=null) {
	$classAttr = (empty($classAttribute)) ? "" : " class='$classAttribute'";
	echo "<$tagName$classAttr>$textContent</$tagName>";
}

function getValueFromIniFile($iniFileURI, $searchDeb, $searchFin=';') {
	if (!file_exists($iniFileURI))
		return FICHIER_NON_TROUVE;

	// lecture du fichier .ini
	$fIni = file_get_contents($iniFileURI);
	$fIni = preg_replace('!/\*.*?\*/!s', '', $fIni);
	$fIni = preg_replace('/(?<!:)\/\/.*?\n/', '', $fIni);

	$debPos = strpos($fIni, $searchDeb);
	if ($debPos === false)
		return NON_TROUVE;
		
	$debPos += strlen($searchDeb)+1;
	$finPos = strpos($fIni, $searchFin, $debPos)-1;
	$searched = trim(substr($fIni, $debPos, ($finPos-$debPos)+1));

	$searched = str_replace(' ', '', $searched);
	$searched = str_replace('=', '', $searched);
	$searched = str_replace('\'', '', $searched);
	$searched = str_replace('\"', '', $searched);
	
	return $searched;
}

function getValueFromAsalaeIniFile($niv1, $niv2 = null) {
	// initialisation
	$ret = null;
	global $appli_path;
	global $fichier_conf;
	global $config;
	
	if (!file_exists($appli_path.'app'.DS.'config'.DS.$fichier_conf))
		return FICHIER_NON_TROUVE;

	if (!isset($config[$niv1]) || (!empty($niv2) && !isset($config[$niv1][$niv2])))
		return NON_TROUVE;
	
	if (!empty($niv2))
		$ret = $config[$niv1][$niv2];
	else
		$ret = $config[$niv1];

	$ret = str_replace('<', '&lt;', $ret);
	$ret = str_replace('>', '&gt;', $ret);

	return $ret;
}


function apache_check_modules($mods) {
	$modules = apache_get_modules();
	foreach ($mods as $module){
		$okko = in_array($module, $modules) ? 'ok' : 'ko';
		d($module, $okko);
	}
}

function php_check_extensions($extentions) {
	foreach ( $extentions as $extension){
		$okko = extension_loaded($extension) ? 'ok' : 'ko';
		d($extension, $okko);
	}
}

function infoDataBase() {
	// initialisation
	global $appli_path;

	// fichier de conf database.php
	if (!file_exists($appli_path.'app'.DS.'config'.DS.'database.php')) {
		d("Fichier de configuration database.php non trouvé", 'ko');
		return;		
	}

	if (!class_exists('DATABASE_CONFIG'))
		include $appli_path.'app'.DS.'config'.DS.'database.php';

    $db = new DATABASE_CONFIG();

	// affichage des infos de connexion
	d('Paramètres de connexion : ', 'info');
	echo '<ul>';
	foreach($db->default as $key=>$valeur) {
		if ($key == 'password') $valeur = '*******';
		t('li', $key.' : '.$valeur);
	}
	echo '</ul>';

    switch($db->default['driver']) {
    	case 'mysql' :
			$link = mysql_connect($db->default['host'], $db->default['login'], $db->default['password'])
				or die ("<div class='ko'>Impossible de se connecter au SGBD</div>");
			echo ("<div class='ok'>Connexion au SGBD reussie</div>");
			$db_selected = mysql_select_db($db->default['database'], $link);
			if (!$db_selected) 
				echo ("<div class='ko'>Impossible d'utiliser la base de données </div>");
			else
				echo ("<div class='ok'>Utilisation de la base de données </div>");
			mysql_close($link);
/*		case 'postgres' :
			if (!array_key_exists('port', $db->default)) $db->default['port']='';
			$conn  = "host='{$db->default['host']}' port='{$db->default['port']}' dbname='{$db->default['database']}' ";
			$conn .= "user='{$db->default['login']}' password='{$db->default['password']}'";
			$link = pg_pconnect($conn);
			if (empty($link))
				d("Connexion à la base de données échouée", 'ko');
			else {
				d("Connexion à la base de données réussie", 'ok');
				// version de la base de données
				$results = pg_query($link, 'SELECT VERSION();');
				$result = pg_fetch_row($results);
				d('Version de la base de données : '.$result[0], 'info');
			}
			pg_close($link);
		break;
*/
    }
}

function php_can_access_applis($prefs, $wsdls=array()) {
    foreach($wsdls as $wsdl ){
         $pos = strpos($prefs, $wsdl);
         $tmp = substr($prefs,  $pos+strlen($wsdl) , strlen($prefs));
         $pos = strpos($tmp, 'http');
         $tmp = substr($tmp,  $pos, strlen($tmp));
         $wsdl = substr($tmp, 0, strpos($tmp, '"'));
             $content = file_get_contents($wsdl);
             die($content);
        }
    }

function verifVersions() {
	// initialisations
	global $appli_path, $versionCakePHPAttendue, $versionPHPAttendue;
	$verAsalae = '';

	// affichage de la version de asalae
	if (file_exists($appli_path.'app'.DS.'config'.DS.'core.php')) {
		$fCore = file_get_contents($appli_path.'app'.DS.'config'.DS.'core.php');
		$verPos = strpos($fCore, 'appVersion');
		if ($verPos === false) {
			d('Version de as@lae : déclaration de appVersion non trouvée dans le fichier core.php', 'ko');
		} else {
			$debPos = strpos($fCore, "'", $verPos+11)+1;
			$finPos = strpos($fCore, "'", $debPos)-1;
			$verAsalae = substr($fCore, $debPos, ($finPos-$debPos)+1);
			d("Version de as@lae : $verAsalae", 'info');
		}
	} else {
		d('Version de as@lae : fichier core.php non trouvé', 'ko');
	}

	// affichage de la version du schéma des tables	
	$dataBaseVersion = getVersionDataBase();
	if ($dataBaseVersion) {
		$okko = ($dataBaseVersion == $verAsalae) ? 'ok' : 'ko';
		d("Version du schéma de la base de données : $dataBaseVersion", $okko);
	} else
		d("Version du schéma de la base de données non trouvée", 'ko');

	// version de cakephp
	if (file_exists($appli_path.'cake'.DS.'VERSION.txt')) {
		$fVer = file_get_contents($appli_path.'cake'.DS.'VERSION.txt');
		$okko = ($fVer==$versionCakePHPAttendue) ? 'ok' : 'ko';
		d("Version de CakePHP (attendue $versionCakePHPAttendue) : $fVer", $okko);
	} else {
		d('Version de CakePHP : fichier de version de CakePHP non trouvé', 'ko');
	}

	// version de PHP
	$phpVer = phpversion();
	$okko = ($phpVer>=$versionPHPAttendue) ? 'ok' : 'ko';
	d("Version de PHP (attendue $versionPHPAttendue) : $phpVer", $okko);

	// affichage de la version de l'os du serveur
	$result=array();
	exec("lsb_release -d", $result);
	if (!empty($result)) {
		$ver = $result[0];
	} else {
		exec("ver", $result);
		if (!empty($result[0]))
			$ver = $result[0];
		elseif (!empty($result[1]))
			$ver = $result[1];
		else
			$ver = 'OS inconnu!';
	}
	d("Version de l'OS du serveur : $ver", 'info');
}

function getVersionDataBase() {
	// intialisations
	global $appli_path;
	
	// type de base
	$typeBase = getTypeDataBase();
	if (empty($typeBase)) return '';
	
	// connexion à la base de donnée
	$db = dbConnect();
	if (empty($db)) return '';

	// lecture de la table versions
	$query = 'SELECT version FROM versions ORDER BY date DESC LIMIT 1';

	switch($typeBase) {
		case 'mysql' :
			$link = mysql_connect($db->default['host'], $db->default['login'], $db->default['password']);
		break;
		case 'postgres' :
			$results = pg_query($db, $query);
			$result = pg_fetch_row($results);
			return $result[0];
		break;
	}
}

function getTypeDataBase() {
	// intialisations
	global $appli_path;

	// fichier de conf database.php
	if (!file_exists($appli_path.'app'.DS.'config'.DS.'database.php'))
		return '';

	if (!class_exists('DATABASE_CONFIG'))
		include $appli_path.'app'.DS.'config'.DS.'database.php';

	$db = new DATABASE_CONFIG();

	return $db->default['driver'];	
}

function my_warning_handler($errno, $errstr) {
	// on ne fait rien
}

function dbConnect() {
	// intialisations
	global $appli_path;
	$link = null;
	set_error_handler("my_warning_handler", E_WARNING);

	// fichier de conf database.php
	if (!file_exists($appli_path.'app'.DS.'config'.DS.'database.php'))
		return null;

	if (!class_exists('DATABASE_CONFIG'))
		include $appli_path.'app'.DS.'config'.DS.'database.php';

	$db = new DATABASE_CONFIG();
	if (!array_key_exists('port', $db->default)) $db->default['port']='';

	switch($db->default['driver']) {
		case 'mysql' :
			$link = mysql_connect($db->default['host'], $db->default['login'], $db->default['password']);
		break;
		case 'postgres' :
			$conn  = "host='{$db->default['host']}' port='{$db->default['port']}' dbname='{$db->default['database']}' ";
			$conn .= "user='{$db->default['login']}' password='{$db->default['password']}'";
			$link = pg_pconnect($conn);
		break;
	}

	return $link;
}


function verifPresenceFichierIni() {
	// initialisations
	global $appli_path, $appIniFiles;

	foreach($appIniFiles as $iniFile) {
		if (file_exists($appli_path.'app'.DS.'config'.DS.$iniFile)) {
			$resultMessage = $iniFile;
			$okko = 'ok';
		} else {
			$resultMessage = "$iniFile non trouvé : renommer le fichier $iniFile.default en $iniFile";
			$okko = 'ko';
		}
		d($resultMessage, $okko);
	}
}

function verifRepEchangeStockage() {
	// initialisations
	global $appli_path, $fichier_conf;
	$repLists = array();
	$repLists[] = array('rep'=>'entree', 'nom'=>"Répertoire d'échange en entrée");
	$repLists[] = array('rep'=>'sortie', 'nom'=>"Répertoire d'échange en sortie");
	$repLists[] = array('rep' =>'stockageMessages', 'nom'=>"Répertoire de sauvegarde des bordereaux de transfert");
	$repLists[] = array('rep'=>'planRangementRacine', 'nom'=>"Réperoire racine du plan de rangement");

	// vérification de la présence du fichier .ini
	if (!file_exists($appli_path.'app'.DS.'config'.DS.$fichier_conf)) {
		d("Fichier de configuration de l'application $fichier_conf non trouvé", 'ko');
		return false;
	}

	// pour chaque répertoire
	foreach($repLists as $repATester) {
		$rep = $repATester['rep'];
		$name = $repATester['nom'];
		$repConfig = getValueFromAsalaeIniFile('Repertoire', $rep);
		if ($repConfig == NON_TROUVE)
			d("$name : déclaration de ['Repertoire']['$rep'] non trouvée dans le fichier $fichier_conf", 'ko');
		elseif (empty($repConfig))
			d("$name : déclaration de ['Repertoire']['$rep'] non renseigné dans le fichier $fichier_conf", 'ko');
		else {
			$repConfig = str_replace('DS', DS, $repConfig);
			$repConfig = str_replace('WWW_ROOT', WWW_ROOT, $repConfig);
			$repConfig = str_replace('ROOT', ROOT, $repConfig);
			$repConfig = str_replace('.', '', $repConfig);
			$repConfig = str_replace('\'', '', $repConfig);
			$repConfig = str_replace('\"', '', $repConfig);
			if (!file_exists($repConfig))
				d("$name : $repConfig n'existe pas", 'ko');
			elseif (!is_writable($repConfig))
				d("$name : $searchRep n'est pas autorisé en écriture", 'ko');
			elseif (!is_readable($repConfig))
				d("$name : $repConfig n'est pas autorisé en lecture", 'ko');
			else
				d("$name : $repConfig", 'ok');
		}
	}
}

function infoMails() {
	// initialisations
	global $appli_path, $fichier_conf;

	// vérification de la présence du fichier .ini
	if (!file_exists($appli_path.'app'.DS.'config'.DS.$fichier_conf)) {
		d("Fichier de configuration de l'application $fichier_conf non trouvé", 'ko');
		return false;
	}

	// mailadministrateur
	$mailAdmin = getValueFromAsalaeIniFile('Mail', 'administrateur');
	if ($mailAdmin == NON_TROUVE)
		d("Mail de l'administrateur : déclaration de ['Mail']['administrateur'] non trouvée dans le fichier $fichier_conf", 'ko');
	elseif (empty($mailAdmin))
		d("Mail de l'administrateur : ['Mail']['administrateur'] non renseigné dans le fichier $fichier_conf", 'ko');
	else
		d("Mail de l'administrateur : $mailAdmin", 'ok');
}

function verifAntivirus() {
	// initialisations
	global $appli_path, $fichier_conf;
	$avTypes = array('CLAMAV'=>'Antivirus Clamav');

	// vérification de la présence du fichier .ini
	if (!file_exists($appli_path.'app'.DS.'config'.DS.$fichier_conf)) {
		d("Fichier de configuration de l'application $fichier_conf non trouvé", 'ko');
		return false;
	}

	// type antivirus
	$avType = getValueFromAsalaeIniFile('Antivirus', 'type');
	if ($avType == NON_TROUVE)
		d("Type de l'antivirus : déclaration de ['Antivirus']['type'] non trouvée dans le fichier $fichier_conf", 'ko');
	elseif (empty($avType))
		d("Type de l'antivirus : ['Antivirus']['type'] non renseigné dans le fichier $fichier_conf", 'ko');
	elseif (!array_key_exists($avType, $avTypes))
		d("Type de l'antivirus : $avType n'est pas géré par as@ale", 'ko');
	else
		d("Type de l'antivirus : $avType ($avTypes[$avType])", 'ok');

	// exécutable de l'antivirus
	$avExec = getValueFromAsalaeIniFile('Antivirus', 'exec');
	if ($avType == NON_TROUVE)
		d("Exécutable de l'antivirus : déclaration de ['Antivirus']['exec'] non trouvée dans le fichier $fichier_conf", 'ko');
	elseif (empty($avType))
		d("Exécutable de l'antivirus : ['Antivirus']['exec'] non renseigné dans le fichier $fichier_conf", 'ko');
	elseif (!file_exists($avExec))
		d("Exécutable de l'antivirus : le fichier exécutable de l'antivirus $avExec est introuvable", 'ko');
	elseif (!is_executable($avExec))
		d("Exécutable de l'antivirus : le fichier exécutable de l'antivirus $avExec est non exécutable", 'ko');
	else {
		d("Exécutable de l'antivirus : $avExec", 'ok');
		switch ($avType) {
			case 'CLAMAV' :
				$result = '';
				exec($avExec." --version", $result);
				if (isset($result[0]))
					d("Version de l'antivirus : ".$result[0], 'info');
				elseif (isset($result[1]))
					d("Version de l'antivirus : ".$result[1], 'info');
				else
					d("Impossible de lire la version de l'antivirus", 'ko');
			break;
		}
	}
}

function verifFormatValidator() {
	// initialisations
	global $appli_path, $fichier_conf;
	$appliTypes = array('CINES'=>'Format validator du CINES');

	// vérification de la présence du fichier .ini
	if (!file_exists($appli_path.'app'.DS.'config'.DS.$fichier_conf)) {
		d("Fichier de configuration de l'application $fichier_conf non trouvé", 'ko');
		return false;
	}

	// type FormatValidator
	$appliType = getValueFromAsalaeIniFile('FormatValidator', 'type');
	if ($appliType == NON_TROUVE) {
		d("Type du valideur de format : déclaration de ['FormatValidator']['type'] non trouvée dans le fichier $fichier_conf", 'ko');
		return;		
	} elseif (empty($appliType)) {
		d("Type du valideur de format : ['FormatValidator']['type'] non renseigné dans le fichier $fichier_conf", 'ko');
		return;		
	} elseif (!array_key_exists($appliType, $appliTypes)) {
		d("Type du valideur de format : $appliType n'est pas géré par as@ale", 'ko');
		return;		
	}
	d("Type du valideur de format : $appliType ($appliTypes[$appliType])", 'ok');

	// repertoire home
	$appliHome = getValueFromAsalaeIniFile('FormatValidator', 'home');
	if ($appliHome == NON_TROUVE) {
		d("Répertoire d'installation (home) : déclaration de ['FormatValidator']['home'] non trouvée dans le fichier $fichier_conf", 'ko');
		return;		
	} elseif (empty($appliHome)) {
		d("Répertoire d'installation (home) : ['FormatValidator']['home'] non renseigné dans le fichier $fichier_conf", 'ko');
		return;		
	} elseif (!file_exists($appliHome)) {
		d("Répertoire d'installation (home) : le répertoire d'installation (home) $appliHome est introuvable", 'ko');
		return;		
	}
	d("Répertoire d'installation (home) : $appliHome", 'ok');

	// Shell launch_asalae.sh
	if (!file_exists($appliHome.'launch_asalae.sh')) {
		d("Le fichier ".$appliHome."launch_asalae.sh est introuvable", 'ko');
		return;		
	} elseif(!is_executable($appliHome.'launch_asalae.sh')) {
		d("Le fichier ".$appliHome."launch_asalae.sh n'est pas exécutable", 'ko');
		return;
	}
	d("Le fichier ".$appliHome."launch_asalae.sh est présent et exécutable", 'ok');

	switch ($appliType) {
		case 'CINES' :
			t('h4', 'Essais du validateur sur des fichiers de référence');
			// test d'un pdf valide
			$result = array();
			$checkFile = getcwd().DS.'files'.DS.'checkPdfOk.pdf';
			$commande = $appliHome."launch_asalae.sh $checkFile";
			exec("$commande 2>&1", $result);
			if (isset($result[8]))
				if (strpos($result[8], 'wf=1') !== false)
					d("Test sur un fichier pdf valide réussi : ".$result[8], 'ok');
				else
					d("Test sur un fichier pdf valide échoué : ".$result[8], 'ko');
			else
				d("Test sur un fichier pdf valide échoué", 'ko');
			// test d'un pdf non valide
			$result = array();
			$checkFile = getcwd().DS.'files'.DS.'checkPdfKo.pdf';
			$commande = $appliHome."launch_asalae.sh $checkFile";
			exec("$commande 2>&1", $result);
			if (isset($result[8]))
				if (strpos($result[8], 'wf=0') !== false)
					d("Test sur un fichier pdf non valide réussi : ".$result[8], 'ok');
				else
					d("Test sur un fichier pdf non valide échoué : ".$result[8], 'ko');
			else
				d("Test sur un fichier pdf non valide échoué", 'ko');
			// test d'un png valide
			$result = array();
			$checkFile = getcwd().DS.'files'.DS.'checkPngOk.png';
			$commande = $appliHome."launch_asalae.sh $checkFile";
			exec("$commande 2>&1", $result);
			if (isset($result[9]))
				if (strpos($result[9], 'wf=1') !== false)
					d("Test sur un fichier png valide réussi : ".$result[9], 'ok');
				else
					d("Test sur un fichier png valide échoué : ".$result[9], 'ko');
			else
				d("Test sur un fichier png valide échoué", 'ko');
			// test d'un png non valide
			$result = array();
			$checkFile = getcwd().DS.'files'.DS.'checkPngKo.png';
			$commande = $appliHome."launch_asalae.sh $checkFile";
			exec("$commande 2>&1", $result);
			if (isset($result[8]))
				if (strpos($result[8], 'wf=0') !== false)
					d("Test sur un fichier png non valide réussi : ".$result[8], 'ok');
				else
					d("Test sur un fichier png non valide échoué : ".$result[8], 'ko');
			else
				d("Test sur un fichier png non valide échoué", 'ko');
		break;
	}
}


function verifHorodatage() {
	// initialisations
	global $appli_path, $fichier_conf;
	$appliTypes = array(
		'SIGNSERVER'=>'Serveur d\'horodatage SignServer',
		'OPENSIGN'=>'Serveur d\'horodatage de l\'Adullact OpenSSL');

	// vérification de la présence du fichier .ini
	if (!file_exists($appli_path.'app'.DS.'config'.DS.$fichier_conf)) {
		d("Fichier de configuration de l'application $fichier_conf non trouvé", 'ko');
		return false;
	}

	// type Horodateur
	$appliType = getValueFromAsalaeIniFile('Horodatage', 'type');
	if ($appliType == NON_TROUVE)
		d("Type du service d\'horodatage : déclaration de ['Horodatage']['type'] non trouvée dans le fichier $fichier_conf", 'ko');
	elseif (empty($appliType))
		d("Type du service d\'horodatage : ['Horodatage']['type'] non renseigné dans le fichier $fichier_conf", 'ko');
	elseif (!array_key_exists($appliType, $appliTypes))
		d("Type du service d\'horodatage : $appliType n'est pas géré par as@ale", 'ko');
	else
		d("Type du service d\'horodatage : $appliType ($appliTypes[$appliType])", 'ok');

	switch($appliType) {
		case 'SIGNSERVER' :
			// host
			$host = getValueFromAsalaeIniFile('Signserver', 'host');
			if ($host == NON_TROUVE)
				d("Signserver host : déclaration de ['Signserver']['host'] non trouvée dans le fichier $fichier_conf", 'ko');
			elseif (empty($host))
				d("Signserver host : ['Signserver']['host'] non renseigné dans le fichier $fichier_conf", 'ko');
			else
				d("Signserver host : $host", 'ok');
			// port
			$port = getValueFromAsalaeIniFile('Signserver', 'port');
			if ($port == NON_TROUVE)
				d("Signserver port : déclaration de ['Signserver']['port'] non trouvée dans le fichier $fichier_conf", 'ko');
			else
				d("Signserver port : $port", 'ok');
			// java home
			$javaHome = getValueFromAsalaeIniFile('Signserver', 'java_home');
			if ($javaHome == NON_TROUVE)
				d("Signserver java home : déclaration de ['Signserver']['java_home'] non trouvée dans le fichier $fichier_conf", 'ko');
			elseif (empty($javaHome))
				d("Signserver java home : ['Signserver']['java_home'] non renseigné dans le fichier $fichier_conf", 'ko');
			else
				d("Signserver java home : $javaHome", 'ok');
		
			if (!empty($host) && !empty($javaHome)) {
				t('h4', "Essai de l'horodatage");
				$result = array();
				$fichierSource = getcwd().DS.'files'.DS.'checkHorodatage.pdf';
				if (!file_exists($fichierSource)) {
					d("Le fichier test pour l'horodatage $fichierSource est introuvable", 'ko');
					return;
				}
				if (!is_writable(getcwd().DS.'files')) {
					$repDest = getcwd().DS.'files';
					d("Le répertoire $repDest n'est pas accessible en écriture", 'ko');
					return;
				}
				$url = "http://$host:$port/signserver/process?workerId=1";
				$timeStampClientJar = ROOT.DS.APP_DIR.DS.'vendors'.DS.'timestampclient'.DS.'timeStampClient.jar';
				$outrep = getcwd().DS.'files'.DS.'checkHorodatage.rep.tsa';
				$outreq = getcwd().DS.'files'.DS.'checkHorodatage.req.tsa';
		
				if (file_exists($outreq)) unlink($outreq);
				if (file_exists($outrep)) unlink($outrep);
		
				$cmd = ' -jar '.$timeStampClientJar.' -base64 -infile '.$fichierSource.' -outreq '.$outreq.' -outrep '.$outrep.' -url '.$url;
		        $java = $javaHome."java";
		        ob_start();
		          passthru("$java $cmd 2>&1", $result);
		        ob_end_clean();
		        
		        if (file_exists($outreq) && file_exists($outrep))
					d("Opération d'horodatage effectuée avec succés", 'ok');
				else
					d("Opération d'horodatage échouée", 'ko');
			
				if (file_exists($outreq)) unlink($outreq);
				if (file_exists($outrep)) unlink($outrep);
			}
		break;
		case 'OPENSIGN' :
			// host
			$host = getValueFromAsalaeIniFile('Opensign', 'host');
			if ($host == NON_TROUVE)
				d("Opensign host : déclaration de ['Opensign']['host'] non trouvée dans le fichier $fichier_conf", 'ko');
			elseif (empty($host))
				d("Opensign host : ['Opensign']['host'] non renseigné dans le fichier $fichier_conf", 'ko');
			else
				d("Opensign host : $host", 'ok');
			
			if (!empty($host)) {
				t('h4', "Essai de l'horodatage");
				$result = array();
				$fichierSource = getcwd().DS.'files'.DS.'checkHorodatage.pdf';
				if (!file_exists($fichierSource)) {
					d("Le fichier test pour l'horodatage $fichierSource est introuvable", 'ko');
					return;
				}
				if (!is_writable(getcwd().DS.'files')) {
					$repDest = getcwd().DS.'files';
					d("Le répertoire $repDest n'est pas accessible en écriture", 'ko');
					return;
				}
				$outrep = getcwd().DS.'files'.DS.'checkHorodatage.rep.tsa';
				$outreq = getcwd().DS.'files'.DS.'checkHorodatage.req.tsa';
				if (file_exists($outreq)) unlink($outreq);
				if (file_exists($outrep)) unlink($outrep);

		        $sha1 = sha1_file($fichierSource);
		        $params = array('sha1'=> $sha1);
		        $client = new SoapClient($host);
		        $requete = $client->__soapCall("createRequest", array('parameters' => $params));
		        $params = array('request'=> $requete);
		        $response = $client->__soapCall("createResponse", array('parameters' => $params));
		        $params = array('response'=> $response);
		        $token = $client->__soapCall("extractToken", array('parameter' => $params));
		        file_put_contents($outreq, $requete);
		        file_put_contents($outrep, $token);

		        if (file_exists($outreq) && file_exists($outrep))
					d("Opération d'horodatage effectuée avec succés", 'ok');
				else
					d("Opération d'horodatage échouée", 'ko');
			
				if (file_exists($outreq)) unlink($outreq);
				if (file_exists($outrep)) unlink($outrep);
			}
		break;
	}
}


function verifConsoleCakePhp() {
	// initialisations
	global $appli_path;
	$winOs = (DIRECTORY_SEPARATOR === '\\');

	// fichier exécutable de la console
	$consoleFileUri = $appli_path.'cake'.DS.'console'.DS. ($winOs ? 'cake.bat' : 'cake');
	
	if (file_exists($consoleFileUri)) {
		d('Console de CakePHP : '.$consoleFileUri, 'ok');
		if (!$winOs) {
			if (is_executable($consoleFileUri))
				d("Console de CakePHP : le fichier exécutable de la console CakePHP '$consoleFileUri' a les droits en exécution", 'ok');
			else
				d("Console de CakePHP : le fichier exécutable de la console CakePHP '$consoleFileUri' n'a pas les droits en exécution", 'ko');
		}
	} else {
		d('Console de CakePHP : le fichier exécutable de la console CakePHP non trouvé', 'ko');
	}
}

function verifConversion() {
	// initialisations
	global $appli_path, $fichier_conf;
	$convTypes = array('UNOCONV'=>'Conversion Unoconv');
	
	// vérification de la présence du fichier .ini
	if (!file_exists($appli_path.'app'.DS.'config'.DS.$fichier_conf)) {
		d("Fichier de configuration de l'application $fichier_conf non trouvé", 'ko');
		return false;
	}
	
	// type convertisseur
	$convType = getValueFromAsalaeIniFile('Conversion', 'type');
	if ($convType == NON_TROUVE)
		d("Type d'outil de conversion : déclaration de ['Conversion']['type'] non trouvée dans le fichier $fichier_conf", 'ko');
	elseif (empty($convType))
		d("Type d'outil de conversion : ['Conversion']['type'] non renseigné dans le fichier $fichier_conf", 'ko');
	elseif (!array_key_exists($convType, $convTypes))
		d("Type d'outil de conversion : $convType n'est pas géré par as@ale", 'ko');
	else
		d("Type d'outil de conversion : $convType ($convTypes[$convType])", 'ok');
	
	// exécutable du convertisseur
	$convExec = getValueFromAsalaeIniFile('Conversion', 'exec');
	if ($convType == NON_TROUVE)
		d("Exécutable de l'outil de conversion : déclaration de ['Conversion']['exec'] non trouvée dans le fichier $fichier_conf", 'ko');
	elseif (empty($convType))
		d("Exécutable de l'outil de conversion : ['Conversion']['exec'] non renseigné dans le fichier $fichier_conf", 'ko');
	elseif (!file_exists($convExec))
		d("Exécutable de l'outil de conversion : le fichier exécutable de l'outil de conversion $convExec est introuvable", 'ko');
	elseif (!is_executable($convExec))
		d("Exécutable de l'outil de conversion : le fichier exécutable de l'outil de conversion $convExec est non exécutable", 'ko');
	else {
		d("Exécutable de l'outil de conversion : $convExec", 'ok');
		switch ($convType) {
			case 'UNOCONV' :
				// affichage de la version de UnoConv
				$result = '';
				exec($convExec." --version", $result);
				if (isset($result[0]))
					d("Version de l'outil de conversion : ".$result[0], 'info');
				elseif (isset($result[1]))
					d("Version de l'outil de conversion : ".$result[1], 'info');
				else
					d("Impossible de lire la version de l'outil de conversion", 'ko');

				// test de conversion de fichier
				t('h4', "Essai de conversion");
				$result = array();
				$fichierSource = getcwd().DS.'files'.DS.'checkConversion.odt';
				if (!file_exists($fichierSource)) {
					d("Le fichier test pour la conversion de format $fichierSource est introuvable", 'ko');
					return;
				}
				if (!is_writable(getcwd().DS.'files')) {
					$repDest = getcwd().DS.'files';
					d("Le répertoire $repDest n'est pas accessible en écriture", 'ko');
					return;
				}

				// préparation de la chaine de commande à exécuter
				$cmd = "$convExec --stdout -f pdf $fichierSource";
	
				// exécution
				$locale = 'fr_FR.UTF-8';
				setlocale(LC_ALL, $locale);
				putenv('LC_ALL='.$locale);
	 			$result = shell_exec($cmd);
	
		        // guess that if there is less than this characters probably an error
		        if (strlen($result) < 10) {
					d("Opération de conversion de format échouée", 'ko');
		        } else {
					d("Opération de conversion de format effectuée avec succés", 'ok');
		        }
			break;
		}
	}
}

function isMulti() {
	$repMulti = CONFIGS.'multi'.DS;
	// listage de tous les fichiers .ini.php
	$iniFiles = glob($repMulti.'*.ini.php');
	return !empty($iniFiles);
}

function afficheMulti() {
	if (!empty($_SERVER['SERVER_NAME']) && strpos($_SERVER['SERVER_NAME'], '.')!==false) {
		$urlSufix = @substr($_SERVER['SERVER_NAME'], @strpos($_SERVER['SERVER_NAME'], '.'));
	} else return;



	$repMulti = CONFIGS.'multi'.DS;
	// listage de tous les fichiers .ini.php
	$iniFiles = glob($repMulti.'*.ini.php');
	echo '<ul>';
	foreach($iniFiles as $iniFile) {
		$nomIniFile = basename($iniFile);
		$nomMulti = @substr($nomIniFile, 0, @strpos($nomIniFile, '.'));
		$url = 'http://'.$nomMulti.$urlSufix.'/check';
		$urlTag = "<a href='$url'>$url</a>";
		t('li', 'Page de vérification de \''.$nomMulti.'\' : '.$urlTag);
	}
	echo '</ul>';
	echo '<br/>';
}

?>
