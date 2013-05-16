<?php

$versionCakePHPAttendue = "1.2.10";
$versionPHPAttendue = "5.2.4";
$mods_apache = array('mod_rewrite', 'mod_dav', 'mod_dav_fs');
$exts_php    = array('soap', 'curl', 'dom');
$appIniFiles = array('database.php', 'webdelib.inc');

// red�finition des constantes principales de cake (un rep au dessus par rapport aux constantes cake)
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(dirname(dirname(__FILE__)))));
define('APP_DIR', basename(dirname(dirname(dirname(__FILE__)))));
define('APP', ROOT.DS.APP_DIR.DS);
define('CONFIGS', APP.'config'.DS);
define('CAKE_CORE_INCLUDE_PATH', ROOT);
define('WEBROOT_DIR', basename(dirname(dirname(__FILE__))));
define('WWW_ROOT', dirname(dirname(__FILE__)) . DS);
define('LIBS', ROOT.DS.'cake'.DS.'libs'.DS);

define ('MODELS', APP.'models'.DS);
define ('BEHAVIORS', MODELS.'behaviors'.DS);
define ('CONTROLLERS', APP.'controllers'.DS);
define ('COMPONENTS', CONTROLLERS.'components'.DS);
define ('VIEWS', APP.'views'.DS);
define ('HELPERS', VIEWS.'helpers'.DS);
define ('VENDORS', APP.'vendors'.DS);

// initalisations
include_once(ROOT.DS.'cake'.DS.'basics.php');
include_once(LIBS.'object.php');
include_once(LIBS.'configure.php');
include_once(LIBS.'cache.php');
include_once(LIBS.'inflector.php');
$fichier_conf = 'webdelib.inc';


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

// chargement du fichier webdelib.inc si il existe
if (file_exists($appli_path.'app'.DS.'config'.DS.$fichier_conf))
	include_once($appli_path.'app'.DS.'config'.DS.$fichier_conf);

/**
 * fonctions utilis�es par le script
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

function getValueFromWebdelibIncFile($niv1) {
        // initialisation
        $ret = null;
        global $appli_path;
        global $fichier_conf;
        global $config;

        if (!file_exists($appli_path.'app'.DS.'config'.DS.$fichier_conf))
                return FICHIER_NON_TROUVE;

        $ret = Configure::read($niv1);

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
		d("Fichier de configuration database.php non trouv�", 'ko');
		return;		
	}

	if (!class_exists('DATABASE_CONFIG'))
		include $appli_path.'app'.DS.'config'.DS.'database.php';

    $db = new DATABASE_CONFIG();

	// affichage des infos de connexion
	d('Param�tres de connexion : ', 'info');
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
				echo ("<div class='ko'>Impossible d'utiliser la base de donn�es </div>");
			else
				echo ("<div class='ok'>Utilisation de la base de donn�es </div>");
			mysql_close($link);
/*		case 'postgres' :
			if (!array_key_exists('port', $db->default)) $db->default['port']='';
			$conn  = "host='{$db->default['host']}' port='{$db->default['port']}' dbname='{$db->default['database']}' ";
			$conn .= "user='{$db->default['login']}' password='{$db->default['password']}'";
			$link = pg_pconnect($conn);
			if (empty($link))
				d("Connexion � la base de donn�es �chou�e", 'ko');
			else {
				d("Connexion � la base de donn�es r�ussie", 'ok');
				// version de la base de donn�es
				$results = pg_query($link, 'SELECT VERSION();');
				$result = pg_fetch_row($results);
				d('Version de la base de donn�es : '.$result[0], 'info');
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

	// affichage de la version de webdelib
	if (file_exists($appli_path.'app'.DS.'config'.DS.'core.php')) {
		$fCore = file_get_contents($appli_path.'app'.DS.'config'.DS.'core.php');
		$verPos = strpos($fCore, 'VERSION');
		if ($verPos === false) {
			d('Version de webdelib : d�claration de Version non trouv�e dans le fichier core.php', 'ko');
		} else {
			$debPos = strpos($fCore, "'", $verPos+8)+1;
			$finPos = strpos($fCore, "'", $debPos)-1;
			$verAsalae = substr($fCore, $debPos, ($finPos-$debPos)+1);
			d("Version de Webdelib : $verAsalae", 'info');
		}
	} else {
		d('Version de Webdelib : fichier core.php non trouv�', 'ko');
	}

	// version de cakephp
	if (file_exists($appli_path.'cake'.DS.'VERSION.txt')) {
		$fVer = file_get_contents($appli_path.'cake'.DS.'VERSION.txt');
		$okko = ($fVer==$versionCakePHPAttendue) ? 'ok' : 'ko';
		d("Version de CakePHP (attendue $versionCakePHPAttendue) : $fVer", $okko);
	} else {
		d('Version de CakePHP : fichier de version de CakePHP non trouv�', 'ko');
	}

	// version de PHP
	$phpVer = phpversion();
	$okko = ($phpVer>=$versionPHPAttendue) ? 'ok' : 'ko';
	d("Version de PHP (attendue $versionPHPAttendue) : $phpVer", $okko);

	// affichage de la version de l'os du serveur
	$result=array();
        $version = file_get_contents('/etc/issue');
	d("Version de l'OS du serveur : $version", 'info');
}

function getVersionDataBase() {
	// intialisations
	global $appli_path;
	
	// type de base
	$typeBase = getTypeDataBase();
	if (empty($typeBase)) return '';
	
	// connexion � la base de donn�e
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
			$resultMessage = "$iniFile non trouv� : renommer le fichier $iniFile.default en $iniFile";
			$okko = 'ko';
		}
		d($resultMessage, $okko);
	}
}

function verifRepEchangeStockage() {
	// initialisations
	global $appli_path, $fichier_conf;
	$repLists = array();

	// v�rification de la pr�sence du fichier .ini
	if (!file_exists($appli_path.'app'.DS.'config'.DS.$fichier_conf)) {
		d("Fichier de configuration de l'application $fichier_conf non trouv�", 'ko');
		return false;
	}

	// pour chaque r�pertoire
	foreach($repLists as $repATester) {
		$rep = $repATester['rep'];
		$name = $repATester['nom'];
		$repConfig = getValueFromWebdelibIncFile('Repertoire', $rep);
		if ($repConfig == NON_TROUVE)
			d("$name : d�claration de ['Repertoire']['$rep'] non trouv�e dans le fichier $fichier_conf", 'ko');
		elseif (empty($repConfig))
			d("$name : d�claration de ['Repertoire']['$rep'] non renseign� dans le fichier $fichier_conf", 'ko');
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
				d("$name : $searchRep n'est pas autoris� en �criture", 'ko');
			elseif (!is_readable($repConfig))
				d("$name : $repConfig n'est pas autoris� en lecture", 'ko');
			else
				d("$name : $repConfig", 'ok');
		}
	}
}

function infoMails() {
	// initialisations
	global $appli_path, $fichier_conf;

	// v�rification de la pr�sence du fichier .ini
	if (!file_exists($appli_path.'app'.DS.'config'.DS.$fichier_conf)) {
		d("Fichier de configuration de l'application $fichier_conf non trouv�", 'ko');
		return false;
	}

	// mailadministrateur
	$mailAdmin = getValueFromWebdelibIncFile('MAIL_FROM');
	if ($mailAdmin == NON_TROUVE)
		d("Mail de l'administrateur : d�claration de ['Mail']['administrateur'] non trouv�e dans le fichier $fichier_conf", 'ko');
	elseif (empty($mailAdmin))
		d("Mail de l'administrateur : ['Mail']['administrateur'] non renseign� dans le fichier $fichier_conf", 'ko');
	else
		d("Adresse exp�diteur : $mailAdmin", 'ok');

	if (getValueFromWebdelibIncFile('SMTP_USE')) {
	    d('Param�tres de connexion : ', 'info');
	    echo ("<ul>"); 
            t('li', 'Serveur SMTP : '.getValueFromWebdelibIncFile('SMTP_HOST'));
            t('li', 'Port SMTP : '.getValueFromWebdelibIncFile('SMTP_PORT'));
            t('li', 'Username SMTP : '.getValueFromWebdelibIncFile('SMTP_USERNAME'));
            t('li', 'Password SMTP : ********');
            echo ("</ul>"); 
	}
        else {
		d("Utilisation du MTA local", 'info');
        }
}

function verifAntivirus() {
	// initialisations
	global $appli_path, $fichier_conf;
	$avTypes = array('CLAMAV'=>'Antivirus Clamav');

	// v�rification de la pr�sence du fichier .ini
	if (!file_exists($appli_path.'app'.DS.'config'.DS.$fichier_conf)) {
		d("Fichier de configuration de l'application $fichier_conf non trouv�", 'ko');
		return false;
	}

	// type antivirus
	$avType = getValueFromWebdelibIncFile('Antivirus', 'type');
	if ($avType == NON_TROUVE)
		d("Type de l'antivirus : d�claration de ['Antivirus']['type'] non trouv�e dans le fichier $fichier_conf", 'ko');
	elseif (empty($avType))
		d("Type de l'antivirus : ['Antivirus']['type'] non renseign� dans le fichier $fichier_conf", 'ko');
	elseif (!array_key_exists($avType, $avTypes))
		d("Type de l'antivirus : $avType n'est pas g�r� par webdelib", 'ko');
	else
		d("Type de l'antivirus : $avType ($avTypes[$avType])", 'ok');

	// ex�cutable de l'antivirus
	$avExec = getValueFromWebdelibIncFile('Antivirus', 'exec');
	if ($avType == NON_TROUVE)
		d("Ex�cutable de l'antivirus : d�claration de ['Antivirus']['exec'] non trouv�e dans le fichier $fichier_conf", 'ko');
	elseif (empty($avType))
		d("Ex�cutable de l'antivirus : ['Antivirus']['exec'] non renseign� dans le fichier $fichier_conf", 'ko');
	elseif (!file_exists($avExec))
		d("Ex�cutable de l'antivirus : le fichier ex�cutable de l'antivirus $avExec est introuvable", 'ko');
	elseif (!is_executable($avExec))
		d("Ex�cutable de l'antivirus : le fichier ex�cutable de l'antivirus $avExec est non ex�cutable", 'ko');
	else {
		d("Ex�cutable de l'antivirus : $avExec", 'ok');
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


function verifConsoleCakePhp() {
	// initialisations
	global $appli_path;
	$winOs = (DIRECTORY_SEPARATOR === '\\');

	// fichier ex�cutable de la console
	$consoleFileUri = $appli_path.'cake'.DS.'console'.DS. ($winOs ? 'cake.bat' : 'cake');
	
	if (file_exists($consoleFileUri)) {
		d('Console de CakePHP : '.$consoleFileUri, 'ok');
		if (!$winOs) {
			if (is_executable($consoleFileUri))
				d("Console de CakePHP : le fichier ex�cutable de la console CakePHP '$consoleFileUri' a les droits en ex�cution", 'ok');
			else
				d("Console de CakePHP : le fichier ex�cutable de la console CakePHP '$consoleFileUri' n'a pas les droits en ex�cution", 'ko');
		}
	} else {
		d('Console de CakePHP : le fichier ex�cutable de la console CakePHP non trouv�', 'ko');
	}
}

function verifConversion() {
	// initialisations
	global $appli_path, $fichier_conf;
	$convTypes = array('UNOCONV'=>'Conversion Unoconv', 'CLOUDOOO'=>'Serveur de Conversion');
	
	// v�rification de la pr�sence du fichier .ini
	if (!file_exists($appli_path.'app'.DS.'config'.DS.$fichier_conf)) {
		d("Fichier de configuration de l'application $fichier_conf non trouv�", 'ko');
		return false;
	}
	
	// type convertisseur
	$convType = getValueFromWebdelibIncFile('CONVERSION_TYPE');
	if ($convType == NON_TROUVE)
		d("Type d'outil de conversion : d�claration de ['Conversion']['type'] non trouv�e dans le fichier $fichier_conf", 'ko');
	elseif (empty($convType))
		d("Type d'outil de conversion : ['Conversion']['type'] non renseign� dans le fichier $fichier_conf", 'ko');
	elseif (!array_key_exists($convType, $convTypes))
		d("Type d'outil de conversion : $convType n'est pas g�r� par webdelib", 'ko');
	else {
                if ($convType == "CLOUDOOO"){
		    d("Type d'outil de conversion : $convType ($convTypes[$convType])", 'ok');
                    return true;
                }
                else
		    d("Type d'outil de conversion : $convType ($convTypes[$convType])", 'ok');
        }
	
	// ex�cutable du convertisseur
	$convExec = getValueFromWebdelibIncFile('CONVERSION_EXEC');
	if ($convType == NON_TROUVE)
		d("Ex�cutable de l'outil de conversion : d�claration de ['Conversion']['exec'] non trouv�e dans le fichier $fichier_conf", 'ko');
	elseif (empty($convType))
		d("Ex�cutable de l'outil de conversion : ['Conversion']['exec'] non renseign� dans le fichier $fichier_conf", 'ko');
	elseif (!file_exists($convExec))
		d("Ex�cutable de l'outil de conversion : le fichier ex�cutable de l'outil de conversion $convExec est introuvable", 'ko');
	elseif (!is_executable($convExec))
		d("Ex�cutable de l'outil de conversion : le fichier ex�cutable de l'outil de conversion $convExec est non ex�cutable", 'ko');
	else {
		d("Ex�cutable de l'outil de conversion : $convExec", 'ok');
		switch ($convType) {
			case 'UNOCONV' :
                                $time_start = microtime(true);
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
					d("Le r�pertoire $repDest n'est pas accessible en �criture", 'ko');
					return;
				}

				// pr�paration de la chaine de commande � ex�cuter
				$cmd = "$convExec --stdout -f pdf $fichierSource";
	
				// ex�cution
				$locale = 'fr_FR.UTF-8';
				setlocale(LC_ALL, $locale);
				putenv('LC_ALL='.$locale);
				$result = shell_exec($cmd);
                                $time_end = microtime(true);
	
		        // guess that if there is less than this characters probably an error
		        if (strlen($result) < 10) {
                            d("Op�ration de conversion de format �chou�e", 'ko');
			} else { 
                            $time = round($time_end - $time_start, 2);
                            d("Op�ration de conversion de format effectu�e avec succ�s en $time secondes", 'ok');
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
		t('li', 'Page de v�rification de \''.$nomMulti.'\' : '.$urlTag);
	}
	echo '</ul>';
	echo '<br/>';
}

function getClassification($id=null){
    $time_start = microtime(true);
    $pos =  strrpos ( getcwd(), 'webroot');
    $path = substr(getcwd(), 0, $pos);

    $url = 'https://'.Configure::read('HOST').'/modules/actes/actes_classification_fetch.php';
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
    if ($reponse != "")  {
        $time = round($time_end - $time_start, 2);
	d(Configure::read('HOST'), 'info');
	d("Fichier de classification S2LOW r�cup�r� en $time secondes", 'ok');
     }
     else {
	d('Echec de r�cup�ration du fichier de classification', 'ko');
     }

}
 

function getCircuitsParapheur() { 
    $time_start = microtime(true);
    $circuits = array();
    if (Configure::read('USE_PARAPH')) {
        include_once(COMPONENTS.'iparapheur.php');      
        $Parafwebservice = new IparapheurComponent();
	$circuits = $Parafwebservice->getListeSousTypesWebservice(Configure::read('TYPETECH'));
        $time_end = microtime(true);
        if (!empty($circuits)) {
	    d(Configure::read('WSTO'), 'info');
            $time = round($time_end - $time_start, 2);
	    d(count($circuits)." circuits du iparapheur r�cup�r� en $time secondes", 'ok');
        }
        else 
	    d('liste de circuit du iparapheur vide', 'ko');
    }
    else {
        d('Utilisation du i-parapheur d�sactiv�', 'info');

    }


}

function getVersionAsalae() {
 
    $client = new SoapClient(ASALAE_WSDL);
    $version = $client->__soapCall("wsGetVersion", array(IDENTIFIANT_VERSANT, MOT_DE_PASSE));
    if (is_int( $version)) {
	d("Echec d'authentification", 'ko');
    } 
    else{
	d(ASALAE_WSDL, 'info');
	d('Version de AS@LAE : '.$version, 'ok');
    }
}

function testerOdfGedooo() {

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

    $fichierSource = getcwd().DS.'files'.DS.'recettage.odt';
    $oTemplate = new GDO_ContentType("", "modele.odt", "application/vnd.oasis.opendocument.text", "binary", file_get_contents($fichierSource));

    $time_start = microtime(true); 
    $oMainPart = new GDO_PartType();
    $oMainPart->addElement(new GDO_FieldType('ma_variable', 'OK', 'text'));
    $oFusion = new GDO_FusionType($oTemplate, "application/vnd.oasis.opendocument.text", $oMainPart);
    $oFusion->process();
  
    $oFusion->SendContentToFile($tmpFile);
    $time_end = microtime(true);
    $time = round($time_end - $time_start, 2);
    if (file_exists($tmpFile))  {
        d(Configure::read('GEDOOO_WSDL'), 'info');
	d("Fusion r�ussie avec ODFGEDOOo en $time secondes", 'ok');
    }
    else 
        d('Fusion �chou�e avec ODFGEDOOo', 'ko');

}

?>
