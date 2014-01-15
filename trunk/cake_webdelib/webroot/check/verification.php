<?php
// affichage des erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

// initalisations
$fichier_conf = 'webdelib.inc';

$versionCakePHPAttendue = "2.2.9";
$versionPHPAttendue = "5.3";
$versionAPACHEAttendue = "2.2";
$mods_apache = array('mod_rewrite', 'mod_ssl', 'mod_dav', 'mod_dav_fs');
$exts_php    = array('soap', 'pgsql', 'xsl', 'curl', 'dom', 'zlib', 'gd');
$libs_php    = array('RPC');
$binaires    = array('ghostscript', 'pdftk', 'pear');
$appIniFiles = array('database.php', 'webdelib.inc');

// redéfinition des constantes principales de cake (un rep au dessus par rapport aux constantes cake)
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(dirname(dirname(__FILE__)))));
define('APP_DIR', basename(dirname(dirname(dirname(__FILE__)))));
define('APP', ROOT.DS.APP_DIR.DS);
define('CONFIGS', APP.'Config'.DS);
define('CAKE_CORE_INCLUDE_PATH', ROOT);
define('WEBROOT_DIR', basename(dirname(dirname(__FILE__))));
define('WWW_ROOT', dirname(dirname(__FILE__)) . DS);

define('LIBS', ROOT.DS.'lib'.DS.'Cake'.DS);
define ('MODELS', APP.'Model'.DS);
define ('BEHAVIORS', MODELS.'Behavior'.DS);
define ('CONTROLLERS', APP.'Controller'.DS);
define ('COMPONENTS', CONTROLLERS.'Component'.DS);
define ('VIEWS', APP.'View'.DS);
define ('HELPERS', VIEWS.'Helper'.DS);
define ('VENDORS', APP.'Vendor'.DS);
define ('CONSOLE', APP.'Console'.DS);

define('TMP', APP.'tmp'.DS);
define('CACHE', TMP.'cache'.DS);
define('LOGS', TMP.'logs'.DS);

// initalisations
include_once(LIBS.'basics.php');
include_once(LIBS.'Core'.DS.'App.php');
include_once(LIBS.'Core'.DS.'Object.php');
include_once(LIBS.'Controller/Component.php');
include_once(LIBS.'Core'.DS.'Configure.php');
include_once(LIBS.'Utility'.DS.'Hash.php');
include_once(LIBS.'Cache'.DS.'Cache.php');

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
if (file_exists(CONFIGS.$fichier_conf))
	include_once(CONFIGS.$fichier_conf);

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
function depliant($textTitle, $linkTitle,$textContent, $id, $classAttribute=null) {
    $classAttr = (empty($classAttribute)) ? "" : " class='$classAttribute'";
    d("$textTitle <a style='cursor: pointer;' onclick='javascript:$(\"#$id\").toggle();'>$linkTitle</a>", $classAttribute);
    echo "<div style='display:none;' id='$id'>$textContent</div>";
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
	
	if (!file_exists(CONFIGS.$fichier_conf))
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
	if (!file_exists(CONFIGS.'database.php')) {
		d("Fichier de configuration database.php non trouvé", 'ko');
		return;		
	}

	if (!class_exists('DATABASE_CONFIG'))
		@include(CONFIGS.'database.php');

    $db = new DATABASE_CONFIG();

	// affichage des infos de connexion
	d('Paramètres de connexion : ', 'info');
	echo '<ul>';
	foreach($db->default as $key=>$valeur) {
		if ($key == 'password') $valeur = '*******';
		t('li', $key.' : '.$valeur);
	}
	echo '</ul>';

    switch($db->default) {
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
		case 'postgres' :
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
	global $appli_path, $versionCakePHPAttendue, $versionPHPAttendue, $versionAPACHEAttendue;

	// affichage de la version de webdelib
	if (file_exists(CONFIGS.'core.php')) {
            include_once(CONFIGS.'core.php');
            if (!defined('VERSION')) 
        	d('Version de webdelib : déclaration de appVersion non trouvée dans le fichier core.php', 'ko');
            else
                d("Version de webdelib : ".VERSION, 'info');
	} else {
            d('Version de as@lae : fichier core.php non trouvé', 'ko');
	}

	// affichage de la version du schéma des tables	
//	$dataBaseVersion = getVersionDataBase();
//	if ($dataBaseVersion) {
//		$okko = ($dataBaseVersion == $verAsalae) ? 'ok' : 'ko';
//		d("Version du schéma de la base de données : $dataBaseVersion", $okko);
//	} else
//		d("Version du schéma de la base de données non trouvée", 'ko');

	// version de cakephp
	if (file_exists(LIBS.'VERSION.txt')) {
                $versionFile = file(LIBS. 'VERSION.txt');
		$fVer = trim(array_pop($versionFile));

		$okko = ($fVer==trim($versionCakePHPAttendue)) ? 'ok' : 'ko';
		d("Version de CakePHP (attendue $versionCakePHPAttendue) : $fVer", $okko);
	} else {
		d('Version de CakePHP : fichier de version de CakePHP non trouvé', 'ko');
	}

	// version de PHP
	$phpVer = phpversion();
	$okko = ($phpVer>=$versionPHPAttendue) ? 'ok' : 'ko';
	d("Version de PHP (attendue $versionPHPAttendue) : $phpVer", $okko);

// version de APACHE
        $apacheVer = apache_get_version();
        $okko = ($apacheVer>=$versionAPACHEAttendue) ? 'ok' : 'ko';
        d("Version d'APACHE (attendue $versionAPACHEAttendue) : $apacheVer", $okko);


	// affichage de la version de l'os du serveur
	$result=array();
	if (DIRECTORY_SEPARATOR === '\\') {
		// windows system
		exec("ver", $result);
		if (!empty($result[0]))
			$ver = $result[0];
		elseif (!empty($result[1]))
			$ver = $result[1];
		else
			$ver = 'WIN OS inconnu!';
	} else {
		if (file_exists('/etc/issue.net'))
			$ver = file_get_contents('/etc/issue.net');
		elseif (file_exists('/etc/issue')) {
			$ver = file_get_contents('/etc/issue');
			$ver = str_replace(array('\\n', '\\l'), '', $ver);
		} else {
			exec("lsb_release -d", $result);
			if (!empty($result))
				$ver = $result[0];
			else 
				$ver = 'UNIX OS inconnu!';
		}
	}
	d("Version de l'OS du serveur : $ver", 'info');
}

function getVersionDataBase() {
	
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
	if (!file_exists(CONFIGS.'database.php'))
		return '';

	if (!class_exists('DATABASE_CONFIG'))
		@include(CONFIGS.'database.php');

	$db = new DATABASE_CONFIG();

	return $db->default;	
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
	if (!file_exists(CONFIGS.'database.php'))
		return null;

	if (!class_exists('DATABASE_CONFIG'))
		@include(CONFIGS.'database.php');

	$db = new DATABASE_CONFIG();
	if (!array_key_exists('port', $db->default)) $db->default['port']='';

	switch($db->default) {
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
		if (file_exists(CONFIGS.$iniFile)) {
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
	$repLists[] = array('cle'=>'Repertoire', 'rep'=>'entree', 'nom'=>"Répertoire d'échange en entrée");
	$repLists[] = array('cle'=>'Repertoire', 'rep'=>'sortie', 'nom'=>"Répertoire d'échange en sortie");
	$repLists[] = array('cle'=>'Repertoire', 'rep' =>'stockageMessages', 'nom'=>"Répertoire de sauvegarde des bordereaux de transfert");
	$repLists[] = array('cle'=>'Edition', 'rep' =>'repertoireModeles', 'nom'=>"Répertoire des modèles odt");

	// vérification de la présence du fichier .ini
	if (!file_exists(CONFIGS.$fichier_conf)) {
		d("Fichier de configuration de l'application $fichier_conf non trouvé", 'ko');
		return false;
	}

	// pour chaque répertoire
	foreach($repLists as $repATester) {
		$cle = $repATester['cle'];
		$rep = $repATester['rep'];
		$name = $repATester['nom'];
		$repConfig = getValueFromAsalaeIniFile($cle, $rep);
		if ($repConfig == NON_TROUVE)
			d("$name : déclaration de ['$cle']['$rep'] non trouvée dans le fichier $fichier_conf", 'ko');
		elseif (empty($repConfig))
			d("$name : déclaration de ['$cle']['$rep'] non renseigné dans le fichier $fichier_conf", 'ko');
		else {
			$repConfig = str_replace('DS', DS, $repConfig);
			$repConfig = str_replace('WWW_ROOT', WWW_ROOT, $repConfig);
			$repConfig = str_replace('ROOT', ROOT, $repConfig);
			$repConfig = str_replace('\\', DS, $repConfig);
			$repConfig = str_replace('/', DS, $repConfig);
			if (!file_exists($repConfig))
				d("$name : $repConfig n'existe pas", 'ko');
			elseif (!is_writable($repConfig))
				d("$name : $repConfig n'est pas autorisé en écriture", 'ko');
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
	if (!file_exists(CONFIGS.$fichier_conf)) {
		d("Fichier de configuration de l'application $fichier_conf non trouvé", 'ko');
		return false;
	}
        
        verif_email();
        
	// mailadministrateur
        $useMail = Configure::read('SMTP_USE');
        if ($useMail) {
            d("Utilisation du SMTP : OUI", 'ok');
            d("Serveur du SMTP : ".Configure::read('SMTP_HOST'), 'ok'); 
            d("Port du serveur SMTP : ".Configure::read('SMTP_PORT'), 'ok'); 
            d("Utilisateur du serveur SMTP : ".Configure::read('SMTP_USERNAME'), 'ok'); 
            $passmail = Configure::read('SMTP_PASSWORD');
            if (!empty($passmail))
                d("Password du serveur SMTP : ********", 'ok'); 
   
        }

	$mailAdmin = Configure::read('MAIL_FROM');
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
	if (!file_exists(CONFIGS.$fichier_conf)) {
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
	if (!file_exists(CONFIGS.$fichier_conf)) {
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
			t('h5', 'Essais du validateur sur des fichiers de référence');
			// test d'un pdf valide
			$result = array();
			$checkFile = getcwd().DS.'files'.DS.'checkPdfOk.pdf';
			$commande = $appliHome."launch_asalae.sh $checkFile";
			exec("$commande 2>&1", $result);
			$rep = analyserResultatCINES($result);
			if (empty($rep))
				d("Erreur lors de l'analyse de la réponse de FormatValidator'", 'ko');
			else
				if ($rep['wf'])
					d("Test sur un fichier pdf valide réussi", 'ok');
				else
					d("Test sur un fichier pdf valide échoué", 'ko');
			// test d'un pdf non valide
			$result = array();
			$checkFile = getcwd().DS.'files'.DS.'checkPdfKo.pdf';
			$commande = $appliHome."launch_asalae.sh $checkFile";
			exec("$commande 2>&1", $result);
			$rep = analyserResultatCINES($result);
			if (empty($rep))
				d("Erreur lors de l'analyse de la réponse de FormatValidator'", 'ko');
			else
				if (!$rep['wf'])
					d("Test sur un fichier pdf non valide réussi", 'ok');
				else
					d("Test sur un fichier pdf non valide échoué", 'ko');
			// test d'un png valide
			$result = array();
			$checkFile = getcwd().DS.'files'.DS.'checkPngOk.png';
			$commande = $appliHome."launch_asalae.sh $checkFile";
			exec("$commande 2>&1", $result);
			$rep = analyserResultatCINES($result);
			if (empty($rep))
				d("Erreur lors de l'analyse de la réponse de FormatValidator'", 'ko');
			else
				if ($rep['wf'])
					d("Test sur un fichier png valide réussi", 'ok');
				else
					d("Test sur un fichier png valide échoué", 'ko');
			// test d'un png non valide
			$result = array();
			$checkFile = getcwd().DS.'files'.DS.'checkPngKo.png';
			$commande = $appliHome."launch_asalae.sh $checkFile";
			exec("$commande 2>&1", $result);
			$rep = analyserResultatCINES($result);
			if (empty($rep))
				d("Erreur lors de l'analyse de la réponse de FormatValidator'", 'ko');
			else
				if (!$rep['wf'])
					d("Test sur un fichier png non valide réussi", 'ok');
				else
					d("Test sur un fichier png non valide échoué", 'ko');
		break;
	}
}

function analyserResultatCINES($result) {
	$flag = 'fr.cines.pac.formatValidator';
	$tableau = array();
	foreach ($result as $ligne) {
		if (substr($ligne, 0, strlen($flag)) == $flag){
			$tableau['format']  = parseCINES($ligne, 'format=');
			$tableau['version'] = parseCINES($ligne, 'version=');
			$tableau['arch']    = parseCINES($ligne, 'arch=');
			$tableau['val']     = parseCINES($ligne,  'val=');
			$tableau['wf']      = parseCINES($ligne,  'wf=');
			return $tableau;
		}
	}
	return $tableau;
}
function parseCINES($ligne, $findme){
	$tmp = substr($ligne, strpos($ligne, $findme), strlen($ligne));
	if ($tmp == '')
		return $tmp;
	else{
		$tmp = substr($tmp, 0, strpos($tmp, ' ')-1);
		return (substr($tmp, strlen($findme), strlen($tmp))); 
	}
}


function verifHorodatage() {
	// initialisations
	global $appli_path, $fichier_conf;
	$horoParametresOk = false;

	require_once(COMPONENTS.'horodatage.php');
	$horodatage = new HorodatageComponent();

	$appliTypes = array(
		'OPENSIGN'=>'Serveur d\'horodatage de l\'Adullact OpenSSL',
		'IAIK'=>'Serveur d\'horodatage IAIK',
		'CRYPTOLOG'=>'Serveur d\'horodatage CRYPTOLOG');

	// vérification de la présence du fichier .ini
	if (!file_exists(CONFIGS.$fichier_conf)) {
		d("Fichier de configuration de l'application $fichier_conf non trouvé", 'ko');
		return false;
	}

	// type Horodatage
	$appliType = getValueFromAsalaeIniFile('Horodatage', 'type');
	if ($appliType == NON_TROUVE)
		d("Type du service d'horodatage : déclaration de ['Horodatage']['type'] non trouvée dans le fichier $fichier_conf", 'ko');
	elseif (empty($appliType))
		d("Type du service d'horodatage : ['Horodatage']['type'] non renseigné dans le fichier $fichier_conf", 'ko');
	elseif (!array_key_exists($appliType, $appliTypes))
		d("Type du service d'horodatage : $appliType n'est pas géré par as@ale", 'ko');
	else
		d("Type du service d'horodatage : $appliType ($appliTypes[$appliType])", 'ok');

	// vérification des paramètres selon le type
	switch($appliType) {
		case 'OPENSIGN' :
			// host
			$host = getValueFromAsalaeIniFile('Opensign', 'host');
			if ($host == NON_TROUVE)
				d("Opensign host : déclaration de ['Opensign']['host'] non trouvée dans le fichier $fichier_conf", 'ko');
			elseif (empty($host))
				d("Opensign host : ['Opensign']['host'] non renseigné dans le fichier $fichier_conf", 'ko');
			else {
				d("Opensign host : $host", 'ok');
				$horoParametresOk = true;
			}
		break;
		case 'IAIK' :
			// host
			$host = getValueFromAsalaeIniFile('IAIK', 'host');
			if ($host == NON_TROUVE)
				d("IAIK host : déclaration de ['IAIK']['host'] non trouvée dans le fichier $fichier_conf", 'ko');
			elseif (empty($host))
				d("IAIK host : ['IAIK']['host'] non renseigné dans le fichier $fichier_conf", 'ko');
			else {
				d("IAIK host : $host", 'ok');
				$horoParametresOk = true;
			}
		break;
		case 'CRYPTOLOG' :
			$horoParametresHostOk = false;
			$horoParametresUserPassOk = false;
			// host
			$host = getValueFromAsalaeIniFile('CRYPTOLOG', 'host');
			if ($host == NON_TROUVE)
				d("CRYPTOLOG host : déclaration de ['CRYPTOLOG']['host'] non trouvée dans le fichier $fichier_conf", 'ko');
			elseif (empty($host))
				d("CRYPTOLOG host : ['CRYPTOLOG']['host'] non renseigné dans le fichier $fichier_conf", 'ko');
			else {
				d("CRYPTOLOG host : $host", 'ok');
				$horoParametresHostOk = true;
			}
			// userpass
			$userpass = getValueFromAsalaeIniFile('CRYPTOLOG', 'userpass');
			if ($userpass == NON_TROUVE)
				d("CRYPTOLOG userpass : déclaration de ['CRYPTOLOG']['userpass'] non trouvée dans le fichier $fichier_conf", 'ko');
			elseif (empty($userpass))
				d("CRYPTOLOG userpass : ['CRYPTOLOG']['userpass'] non renseigné dans le fichier $fichier_conf", 'ko');
			else {
				d("CRYPTOLOG userpass : $userpass", 'ok');
				$horoParametresUserPassOk = true;
			}
			$horoParametresOk = $horoParametresHostOk && $horoParametresUserPassOk;
		break;
	}
	
	// essai de l'horodatage du fichier test
	if ($horoParametresOk) {
		t('h5', "Essai de l'horodatage");
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
		$jhFileUri = getcwd().DS.'files'.DS.'checkHorodatage.pdf.rep.tsa';
		if (file_exists($jhFileUri)) unlink($jhFileUri);

		$horodatage->horodateFichier($fichierSource);
		if (file_exists($jhFileUri) && filesize($jhFileUri)>0)
			d("Opération d'horodatage effectuée avec succés", 'ok');
		else
			d("Opération d'horodatage échouée", 'ko');

		if (file_exists($jhFileUri)) unlink($jhFileUri);
	}
}


function verifConsoleCakePhp() {
	// initialisations
	global $appli_path;
	$winOs = (DIRECTORY_SEPARATOR === '\\');

	// fichier exécutable de la console
	$consoleFileUri = APP.'Console'.DS. ($winOs ? 'cake.bat' : 'cake');
	
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
	$convTypes = array('UNOCONV'=>'Conversion Unoconv', 'CLOUDOOO'=> 'Conversion Unoconv');
	
	// vérification de la présence du fichier .ini
	if (!file_exists(CONFIGS.$fichier_conf)) {
		d("Fichier de configuration de l'application $fichier_conf non trouvé", 'ko');
		return false;
	}
       else 
           include_once(CONFIGS.$fichier_conf);
	
	// type convertisseur

	$convType = Configure::read('CONVERSION_TYPE');
	if ($convType == NON_TROUVE)
		d("Type d'outil de conversion : déclaration de ['Conversion']['type'] non trouvée dans le fichier $fichier_conf", 'ko');
	elseif (empty($convType))
		d("Type d'outil de conversion : ['Conversion']['type'] non renseigné dans le fichier $fichier_conf", 'ko');
	elseif (!array_key_exists($convType, $convTypes))
		d("Type d'outil de conversion : $convType n'est pas géré par as@ale", 'ko');
	else
		d("Type d'outil de conversion : $convType ", 'ok');
	
	// exécutable du convertisseur
	if ($convType == NON_TROUVE)
		d("Exécutable de l'outil de conversion : déclaration de ['Conversion']['exec'] non trouvée dans le fichier $fichier_conf", 'ko');
	elseif (empty($convType))
		d("Exécutable de l'outil de conversion : ['Conversion']['exec'] non renseigné dans le fichier $fichier_conf", 'ko');
	else {
		switch ($convType) {
			case 'CLOUDOOO' :
                            $repModels = APP.WEBROOT_DIR.DS.'files'.DS;
                            $modelFileName = 'empty.odt';
                            require_once 'XML/RPC.php';
                            $content =  base64_encode(file_get_contents($repModels.$modelFileName));
                            $fileinfo =  pathinfo($repModels.$modelFileName);
                            if ($fileinfo['extension'] == 'pdf') $fileinfo['extension'] = 'odt';

                            $params = array( new XML_RPC_Value($content, 'string'),
                                             new XML_RPC_Value($fileinfo['extension'],    'string'),
                                             new XML_RPC_Value("pdf",    'string'),
                                             new XML_RPC_Value(false,      'boolean'),
                                             new XML_RPC_Value(true,       'boolean'));

                             $url = Configure::read('CLOUDOOO_HOST').":".Configure::read('CLOUDOOO_PORT');
                             $msg = new XML_RPC_Message('convertFile', $params);
                             $cli = new XML_RPC_Client('/', $url);
                             $resp = $cli->send($msg);
                             if (!empty($resp->xv->me['string'])) {
                                 $return = base64_decode($resp->xv->me['string']);
                                 if (!empty($return))
		                     d("Exécutable de l'outil de conversion : $convType", 'ok');
                             }
                             else
		                d("Exécutable de l'outil de conversion : $convType", 'ko');
                             
                            //IP +PORT
                            $cloudoooHost = Configure::read('CLOUDOOO_HOST');
                            $cloudoooPort = Configure::read('CLOUDOOO_PORT');
                            if ($cloudoooHost !== NON_TROUVE && !empty($cloudoooHost))
                                    if ($cloudoooPort !== NON_TROUVE && !empty($cloudoooPort))
                                        d("Url de CLOUDOOO : $cloudoooHost:$cloudoooPort", 'info');
                                    else 
                                        d("Url de CLOUDOOO : $cloudoooHost (port non renseigné dans le fichier $fichier_conf)", 'ko');
                            else
                                    d("Url de CLOUDOOO : non renseigné dans le fichier $fichier_conf", 'ko');
                             
                        break;
			case 'UNOCONV' :
	                        $convExec = Configure::read('UNOCONV_EXEC');
		                 d("Exécutable de l'outil de conversion : $convExec", 'ok');
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
				t('h5', "Essai de conversion");
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


function verifVolumes() {
        return true;
	// intialisations
	global $appli_path;
	$volumeDefaut = array(
		'description' => '',
		'actif' => true,
		'date_debut' => '',
		'date_fin' => '',
		'alerte_delais_date_fin' => '',
		'alerte_taux_occupation' => 0,
		'type' => 'FS');
	$volumeTypes =array(
		'FS' => 'système de fichiers',
		'STS-PEA' => 'coffre fort numérique STS-PEA');

	// lecture des volumes définis dans le fichier de config
	$volumesIni = 	$mailAdmin = getValueFromAsalaeIniFile('Volume');
	foreach($volumesIni as $idVolume=>$volumeIni) {
		$volumeIni['identifier'] = $idVolume;
		$volumes[] = array('Volume'=>array_merge($volumeDefaut, $volumeIni));
	}

	// affichage des volumes
	foreach($volumes as $volume) {
		if ($volume['Volume']['actif']) {
			$checkVolume = checkVolume($volume);
			$okkoinfo = empty($checkVolume) ? 'ok' : 'ko';
		} else {
			$checkVolume = '';
			$okkoinfo = 'info';
		}
		d("Volume de stockage '".$volume['Volume']['identifier']."' : ".$checkVolume, $okkoinfo);
		// affichage des infos de connexion
		echo '<ul>';
			t('li', 'nom : '.$volume['Volume']['nom']);
			t('li', 'description : '.$volume['Volume']['description']);
			t('li', 'actif : '.($volume['Volume']['actif']?'oui':'non'));
			t('li', 'date de debut de validité : '.$volume['Volume']['date_debut']);
			t('li', 'date de fin de validité : '.$volume['Volume']['date_fin']);
			t('li', 'délais d\'alerte avant fin validité : '.$volume['Volume']['alerte_delais_date_fin']);
			t('li', 'taux d\'alerte d\'occupation : '.$volume['Volume']['alerte_taux_occupation']);
			if (array_key_exists($volume['Volume']['type'], $volumeTypes)) {
				t('li', 'type : '.$volume['Volume']['type'].' ('.$volumeTypes[$volume['Volume']['type']].')');
			} else {
				
			}
			switch($volume['Volume']['type']) {
				case 'FS' :
					// Système de fichiers
					t('li', 'répertoire : '.$volume['Volume']['repertoire']);
					break;
				case 'STS-PEA' :
					// Plateforme d'archivage électronique STS PEA
					t('li', 'url : '.$volume['Volume']['urlWsdl']);
					t('li', 'identifiant de connexion : '.$volume['Volume']['userLogin']);
					t('li', 'mot de passe de connexion : '.'*********');
					t('li', 'identifiant du coffre : '.$volume['Volume']['vaultId']);
					t('li', 'identifiant du conteneur : '.$volume['Volume']['containerId']);
					break;
			}
		echo '</ul>';
	}
}

function checkVolume($volume) {
	// initialisations
	$ret = '';

	switch($volume['Volume']['type']) {
		case 'FS' :
			// Système de fichiers
			if (!is_dir($volume['Volume']['repertoire']))
				return 'le répertoire n\'existe pas';
			if (!is_writable($volume['Volume']['repertoire']))
				return 'le répertoire n\'est pas accessible en écriture';
			if (!is_readable($volume['Volume']['repertoire']))
				return 'le répertoire n\'est pas accessible en lecture';
			break;
		case 'STS-PEA' :
			// Plateforme d'archivage électronique STS PEA
			require_once(APP.'libs'.DS.'stspea.php');
			$ping = AppStsPea::ping($volume['Volume']);
			if (!$ping)
				return 'le coffre fort STS-PEA n\'est pas accessible';
			break;
	}

	return $ret;
}

function verifAccords() {
	// intialisations
	global $appli_path;
	
	// type de base
	$typeBase = getTypeDataBase();
	if (empty($typeBase)) return '';
	if ($typeBase != 'postgres') return '';
	
	// connexion à la base de donnée
	$db = dbConnect();
	if (empty($db)) return '';


	// lecture de la table des volumes
	$query = 'SELECT * FROM "adm-accords"';
	$accords = pg_query($db, $query);
	while ($accord = pg_fetch_array($accords, NULL, PGSQL_ASSOC)) {
		if (empty($accord['volume_identifier'])) {
			$checkAccord = 'le volume de stockage n\'est pas spécifié';
			$okko = 'ko';
		} else {
			$checkAccord = '';
			$okko = 'ok';
		}
		d($accord['nom'].' (volume: '.$accord['volume_identifier'].')'.($okko == 'ko'?' : ':'').$checkAccord, $okko);
	}
}


function testerOdfGedooo() {
	// initialisations
	global $appli_path, $fichier_conf;
	$editionTypes = array('ODFGEDOOo'=>'Outil de fusion des modèles odt Gedooo');

	// vérification de la présence du fichier .ini
	if (!file_exists(CONFIGS.$fichier_conf)) {
        include_once(CONFIGS.$fichier_conf);
		d("Fichier de configuration de l'application $fichier_conf non trouvé", 'ko');
		return false;
	}

	// type outil d'édition
    // initialisations
    $gedoooWsdl = Configure::read('GEDOOO_WSDL');
    if ($gedoooWsdl == NON_TROUVE)
        d("Url wsdl de l'outil d'édition : déclaration de ['Edition']['GEDOOO_WSDL'] non trouvée dans le fichier $fichier_conf", 'ko');
    elseif (empty($gedoooWsdl))
        d("Url wsdl de l'outil d'édition : ['Edition']['GEDOOO_WSDL'] non renseigné dans le fichier $fichier_conf", 'ko');
    else
        d("Url wsdl de l'outil d'édition : $gedoooWsdl", 'info');

    if (empty($gedoooWsdl))
        return;

    try {
        $oService = new SoapClient($gedoooWsdl);
        d("Version de l'outil d'édition : ".$oService->__soapCall("Version", array()), 'info');
    } catch (Exception $e) {
        //Erreur lors de l'initialisation de la connexion : code 001
        d("Version de l'outil d'édition : Erreur lors de la connexion au WSDL : " . $e->getMessage(), 'ko');
    }

    // test d'édition
    t('h5', "Essai d'édition");

    $tmpFile = "/tmp/FILE_RESULT.odt";
    @unlink( $tmpFile );

    include_once (VENDORS.'GEDOOo/phpgedooo/GDO_Utility.class');
    include_once (VENDORS.'GEDOOo/phpgedooo/GDO_FieldType.class');
    include_once (VENDORS.'GEDOOo/phpgedooo/GDO_ContentType.class');
    include_once (VENDORS.'GEDOOo/phpgedooo/GDO_IterationType.class');
    include_once (VENDORS.'GEDOOo/phpgedooo/GDO_PartType.class');
    include_once (VENDORS.'GEDOOo/phpgedooo/GDO_FusionType.class');
    include_once (VENDORS.'GEDOOo/phpgedooo/GDO_MatrixType.class');
    include_once (VENDORS.'GEDOOo/phpgedooo/GDO_MatrixRowType.class');
    include_once (VENDORS.'GEDOOo/phpgedooo/GDO_AxisTitleType.class');

    $fichierSource = APP.WEBROOT_DIR.DS.'files'.DS.'empty.odt';
    $oTemplate = new GDO_ContentType("", "modele.odt", "application/vnd.oasis.opendocument.text", "binary", file_get_contents($fichierSource));

    $time_start = microtime(true);
    $oMainPart = new GDO_PartType();
    $oMainPart->addElement(new GDO_FieldType('ma_variable', 'OK', 'text'));
    $oFusion = new GDO_FusionType($oTemplate, "application/vnd.oasis.opendocument.text", $oMainPart);
    try {
        $oFusion->process();
        $oFusion->SendContentToFile($tmpFile);
        $time_end = microtime(true);
        $time = round($time_end - $time_start, 2);
        if (file_exists($tmpFile))  {
            d("Fusion réussie avec ODFGEDOOo en $time secondes", 'ok');
        } else
            d('Fusion échouée avec ODFGEDOOo', 'ko');
    } catch(Exception $e) {
        d("Fusion échouée avec ODFGEDOOo : ".$e->getMessage(), 'ko');
    }

}


//
function verifRelaxNG() {
        // initialisations
        global $appli_path, $fichier_conf;
        
        $relaxTypes = array(
                'JING'=>'Vérification avec JING',
                'DOM'=>'Vérification avec DOM PHP');

        // vérification de la présence du fichier .ini
        if (!file_exists(CONFIGS.$fichier_conf)) {
                d("Fichier de configuration de l'application $fichier_conf non trouvé", 'ko');
                return false;
        }

        // type RelaxNG
        $relaxType = getValueFromAsalaeIniFile('RelaxngValidation', 'type');
        if ($relaxType == NON_TROUVE)
                d("Type de vérificateur de format relaxNG : déclaration de ['RelaxngValidation']['type'] non trouvée dans le fichier $fichier_conf", 'ko');
        elseif (empty($relaxType))
                d("Type de vérificateur de format relaxNG : ['RelaxngValidation']['type'] non renseigné dans le fichier $fichier_conf", 'ko');
        elseif (!array_key_exists($relaxType, $relaxTypes))
                d("Type de vérificateur de format relaxNG : $relaxType n'est pas géré par as@ale", 'ko');
        else
                d("Type de vérificateur de format relaxNG : $relaxType ($relaxTypes[$relaxType])", 'ok');

}

function verifPresenceModelesOdt() {
    // initialisations
    $repModels = APP.WEBROOT_DIR.DS.'files'.DS;
    $modelFileName = 'empty.odt';
    if (file_exists($repModels.$modelFileName)) {
        $resultMessage = $repModels.$modelFileName;
 	$okko = 'ok';
    } else {
        $resultMessage = "$repModels$modelFileName non trouvé";
        $okko = 'ko';
    }
    d($resultMessage, $okko);
}

function getClassification(){
    $time_start = microtime(true);
    $pos =  strrpos ( getcwd(), 'webroot');
    $url = 'https://'.Configure::read('S2LOW_HOST').'/modules/actes/actes_classification_fetch.php';
    $data = array('api' => '1' );
    $url .= '?'.http_build_query($data);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if (Configure::read('USE_PROXY'))
        curl_setopt($ch, CURLOPT_PROXY, Configure::read('HOST_PROXY'));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_CAPATH, Configure::read('CA_PATH'));
    curl_setopt($ch, CURLOPT_SSLCERT, Configure::read('PEM'));
    curl_setopt($ch, CURLOPT_SSLCERTPASSWD, Configure::read('PASSWORD'));
    curl_setopt($ch, CURLOPT_SSLKEY, Configure::read('SSLKEY'));
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $reponse = curl_exec($ch);
    if (curl_errno($ch))
        d(curl_error($ch), 'ko');

    curl_close($ch);
    $time_end = microtime(true);
    if (stripos($reponse, 'DateClassification' ) !== false)  {
        $time = round($time_end - $time_start, 2);
        d(Configure::read('S2LOW_HOST'), 'info');
        d("Fichier de classification S2LOW récupéré en $time secondes", 'ok');
     }
     else {
        d('Echec de récupération du fichier de classification', 'ko');
     }
}

function getCircuitsParapheur() {
    $time_start = microtime(true);
    $circuits = array();
    if (Configure::read('USE_PARAPH')) {
        include_once(COMPONENTS.'IparapheurComponent.php');
        $Parafwebservice = new IparapheurComponent();
        $circuits = $Parafwebservice->getListeSousTypesWebservice(Configure::read('TYPETECH'));
        $time_end = microtime(true);
        if (!empty($circuits)) {
            d(Configure::read('WSTO'), 'info');
            $time = round($time_end - $time_start, 2);
            d(count($circuits)." circuits du iparapheur récupéré en $time secondes", 'ok');
        }
        else
            d('liste de circuit du iparapheur vide', 'ko');
    }
    else {
        d('Utilisation du i-parapheur désactivé', 'info');

    }
}

function getVersionAsalae() {

    $client = new SoapClient(Configure::read('ASALAE_WSDL'));
    $version = $client->__soapCall("wsGetVersion", array(Configure::read('IDENTIFIANT_VERSANT'), Configure::read('MOT_DE_PASSE')));
    if (is_int( $version)) {
        d("Echec d'authentification", 'ko');
    }
    else{
        d(Configure::read('ASALAE_WSDL'), 'info');
        d('Version de AS@LAE : '.$version, 'ok');
    }
}

function getPastellVersion() {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC) ;
    curl_setopt($curl, CURLOPT_USERPWD, Configure::read("PASTELL_LOGIN").":".Configure::read("PASTELL_PWD"));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $api = Configure::read("PASTELL_HOST").'/web/api/version.php';
    curl_setopt($curl, CURLOPT_URL, $api);

    $reponse = curl_exec($curl); 
    curl_close($curl);
    $reponse = json_decode($reponse);
    $reponse = (array)$reponse;
    if (is_array($reponse) and isset($reponse['version']))
        d('Version de PASTELL : '.$reponse['version'], 'ok');
    else
        d('Impossible de communiquer avec PASTELL : ', 'ko');


}

function check_binaires($binaires) {
    foreach ($binaires as $bin) {
        $output = exec('whereis -b ' . $bin);
        $okko = ($output == $bin . ":") ? 'ko' : 'ok';
        d($bin, $okko);
    }
}

function verif_email() {
    $destinataire = Configure::read("MAIL_FROM");
    $ok = mail($destinataire, 'Test de la fonction mail', 'Ceci est un message de test de webdelib, veuillez ne pas répondre.');
    $okko = $ok ? "ok" : "ko";
    
    d("Envoi de courrier électronique : $okko", $okko);
}

function php_check_librairies($libs) {
    foreach ( $libs as $lib ){
        $output = exec("locate $lib.php");
        $okko = !empty($output) ? 'ok' : 'ko';
        d($lib, $okko);
    }
}

function checkGED(){
    $urlGED     = Configure::read("GED_URL");
    $repoGED    = Configure::read("GED_REPO");
    d("URL de la GED : ".$urlGED, 'info');
    d("Dossier distant : ".$repoGED, 'info');
}

function checkLDAP(){
    
    $ldapInfos = array();
    if(Configure::read('USE_OPENLDAP')){
        $ldapInfos["Serveur"] = Configure::read("LDAP_HOST");
        $ldapInfos["Port"] = Configure::read("LDAP_PORT");
        $ldapInfos["Login"] = Configure::read("LDAP_LOGIN");
        $ldapInfos["Password"] = Configure::read("LDAP_PASSWD");
        $ldapInfos["Unique_ID"] = Configure::read("UNIQUE_ID");
        $ldapInfos["Base_DN"] = Configure::read("BASE_DN");
        $ldapInfos["Account_suffix"] = Configure::read("ACCOUNT_SUFFIX");
        $ldapInfos["DN"] = Configure::read("DN");
    }
    elseif(Configure::read('USE_AD')){
        $ldapInfos = array();
        $ldapInfos["Serveur"] = LDAP_HOST;
        $ldapInfos["Port"] = LDAP_PORT;
        $ldapInfos["Login"] = LDAP_LOGIN;
        $ldapInfos["Password"] = LDAP_PASSWD;
        $ldapInfos["Unique_ID"] = UNIQUE_ID;
        $ldapInfos["Base_DN"] = BASE_DN;
        $ldapInfos["Account_suffix"] = ACCOUNT_SUFFIX;
        $ldapInfos["DN"] = DN;      
    }
    
    // affichage des infos LDAP
    d('Paramètres de connexion : ', 'info');
    echo '<ul>';
    foreach($ldapInfos as $key=>$valeur) {
            if ($key == 'Password') $valeur = '*******';
            t('li', $key.' : '.$valeur);
    }
    echo '</ul>';
    
    // ouverture de la connexion
    $conn = ldap_connect($ldapInfos["Serveur"], $ldapInfos["Port"]);
    if ($conn) {
        if (Configure::read('USE_AD')){
            ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);
        }
        
        // authentification
        if(Configure::read('USE_OPENLDAP'))
            @ldap_bind($conn, $ldapInfos["Unique_ID"].'='.$ldapInfos["Login"].','.$ldapInfos["Base_DN"], $ldapInfos["Password"]);
        else
            @ldap_bind($conn, $ldapInfos["Login"], $ldapInfos["Password"]);
        
        if (ldap_error($conn) == "Can't contact LDAP server"){
            d('Impossible de se connecter au serveur LDAP \''.$ldapInfos["Serveur"] .':'. $ldapInfos["Port"].'\'', 'ko');
        }else{
            d('Connexion au serveur '.$ldapInfos["Serveur"] .':'. $ldapInfos["Port"].' réussie', 'ok');
            if(ldap_error($conn) == "Success"){
                d('Authentification à l\'annuaire LDAP réussie', 'ok');
            } else {
                d('Echec de l\'authentification à l\'annuaire LDAP : '.ldap_error($conn), 'ko');
                if(Configure::read('USE_OPENLDAP')){
                    // recherche de l'utilisateur dans l'annuaire (ne fonctionne que si ldap public)
                    $result = @ldap_search($conn, $ldapInfos["Base_DN"], $ldapInfos["Unique_ID"]."=".$ldapInfos["Login"]);
                    $info = @ldap_get_entries($conn, $result);
                    if ($info['count'] > 0){
                        d('L\'utilisateur \''.$ldapInfos["Login"].'\' est bien présent dans l\'annuaire', 'ok');
                        d('Le mot de passe de l\'utilisateur \''.$ldapInfos["Login"].'\'doit être incorrect', 'ko');
                    } else
                        d('L\'utilisateur \''.$ldapInfos["Login"].'\' est introuvable (ou les droits d\'accès sont insuffisants)', 'ko');
                }
            }
        }
        
        // Fermeture de la connexion
        @ldap_close($conn);
    } else {
        d('Connexion au serveur LDAP : échec', 'ko');
    }
}

/**
 * Vérifie l'intégrité du schéma de la base de données
 */
function checkSchema(){
    $command = CONSOLE.'cake schema update --dry';
    try{
        $retour = exec($command, $message);
        t('h5', "Test d'intégrité du schéma");
        if ($retour == 'Schema is up to date.') {
            d('Schéma de la base de données : Valide !', 'ok');
        } else {
            //Supprime les 12 premières et les deux dernieres lignes du tableau constituant le message de retour de l'éxécution (infos inutiles)
            removeFromArray($message, 12, 2);
            throw new Exception(implode('<br>', $message));
        }
    } catch (Exception $e) {
        depliant('Schéma de la base de données : Problème d\'intégrité !!', 'Afficher/Masquer les différences..', $e->getMessage(), 'databaseIntegrity','ko');
    }
}

/**
 * @param $array    le tableau à redimmensionner
 * @param $begin    nouvel indice de début de tableau (nombre d'éléments à supprimer en début du tableau)
 * @param $nbToPop  nouvel indice de fin de tableau (nombre d'éléments à supprimer en fin du tableau)
 */
function removeFromArray(&$array, $begin, $end){
    for($i = 0; $i<$begin; $i++){
        array_shift($array);
    }
    for($i = 0; $i<$end; $i++){
        array_pop($array);
    }
}

?>
