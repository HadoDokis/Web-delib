<?php
    include_once('header.php');

    $appli_path = '/var/www/webdelib-dev';
    $fichier_conf = 'webdelib.inc';   
    $mods_apache = array('mod_dav', 'mod_dav_fs', 'mod_rewrite', 'fleurenplastique');   
    $exts_php    = array('mysql', 'curl', 'openssl', 'soap', 'dom', 'xml', 'toto');
    $variables   = array('GEDOOO_WSDL');

    define ('CONFIGS', "$appli_path/app/config/");
    require_once("$appli_path/cake/libs/object.php");
    require_once("$appli_path/cake/libs/configure.php");
    require_once(CONFIGS."/database.php");

    // modules apache
    echo ('<h2>APACHE</h2>');
    echo 'Proprietaire du script courant : ' . get_current_user();
    apache_check_modules($mods_apache);

    //  librairie PHP
    echo ('<h2>PHP</h2>');
    php_check_extensions($exts_php);
    echo ('<h3>accès disque</h3>');
    // droit d'ecritures
    php_can_write("$appli_path/app/webroot");
    php_can_write("$appli_path/app/tmp");
    // connexion Base de données
    echo ('<h3>Base de données</h3>');
    php_can_connect() ;

    // connexion aux applis tiers
    echo ('<h3>applications tiers</h3>'); 
    $prefs = file_get_contents("$appli_path/app/config/$fichier_conf");
    php_can_access_applis($prefs, $variables);

    function apache_check_modules($mods) {
        $modules = apache_get_modules();
        foreach ($mods as $module){
            if (apache_is_module_loaded( $modules, $module))
                $result = 'ok';
            else 
                $result = 'ko';
            echo ("<div class='$result'> $module</div>");
        }
    }

    function apache_is_module_loaded($modules, $mod_name) {
        return in_array($mod_name, $modules);
    }   

    function php_check_extensions($extentions) {
        foreach ( $extentions as $extension){
            if (extension_loaded($extension))
                $result = 'ok';
            else 
                $result = 'ko';
            echo ("<div class='$result'> $extension</div>");
        }
    }

    function php_can_write($path) {
       if (is_writable($path)) 
           echo ("<div class='ok'>PHP a les droits d'écriture sur $path</div>");
       else 
           echo ("<div class='ko'>PHP n'a pas les droits d'écriture sur $path</div>");
    }
    
    function php_can_connect() {
        $db = new DATABASE_CONFIG();
        if ($db->default['driver'] == 'mysql') {
            $link = mysql_connect($db->default['host'], $db->default['login'], $db->default['password'])
                or die ("<div class='ko'>Impossible de se connecter au SGBD</div>");
            echo ("<div class='ok'>Connexion au SGBD Reussi</div>");
            $db_selected = mysql_select_db($db->default['database'], $link);
            if (!$db_selected) 
                echo ("<div class='ko'>Impossible d'utiliser la base de données </div>");
            else
                echo ("<div class='ok'>Utilisation de la base de données </div>");
            mysql_close($link);	
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

    include_once('footer.php');
?>
