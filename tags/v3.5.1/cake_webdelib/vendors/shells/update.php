<?php

    App::import(array('Model', 'AppModel', 'File'));

    class UpdateShell extends Shell{

	function main () {
	    $url = 'http://webdelib.adullact.net/patch-3.5.1.tgz';
	    $tmp_path = '/tmp/';
            $appli_path = '/var/www/webdelib/';

            $last_file = $this->getLastVersion($url, $tmp_path);
	    if (!file_exists($last_file)) die ("Erreur lors de la récupération du fichier !");
            echo ("Fichier  $last_file récupéré : (".filesize($last_file). ") \n\n"); 
	    $list_files = $this->unTarFile($last_file, $tmp_path);
	    foreach ($list_files as $file) {
		$fichier = substr($file['filename'], strlen($tmp_path)+strlen('webdelib/'), strlen($file['filename']));
		if ($fichier == "VERSION") {
		    $new_version = $this->getNewVersion($fichier, $tmp_path);
		    echo  ("Version du patch : $new_version \n");
		}
            }
            foreach ($list_files as $file) {
		$fichier = substr($file['filename'], strlen($tmp_path)+strlen('webdelib/'), strlen($file['filename']));
		if (trim($new_version) == "3.5.1") {
		    if (($fichier != '.') || ($fichier != '..')) {
			if (!is_dir( $appli_path.$fichier)){
			    if ($fichier != "VERSION" ) {
                                if (file_exists($appli_path.$fichier)) {
                                    echo "copy($appli_path.$fichier, $appli_path.$fichier.'-orig')\n";
                                    echo "copy($tmp_path.'webdelib/'.$fichier, $appli_path.$fichier)\n";
                                }
                            }
                        }
                    }
                }
            }
        }
	
	function getLastVersion($url,  $tmp_path) {
            $last_file =  $tmp_path."latest.tar.gz";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            // curl_setopt($ch, CURLOPT_PROXY, '138.239.254.17:8080');
            curl_setopt($ch, CURLOPT_POST, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_CAPATH, Configure::read('CA_PATH'));
            curl_setopt($ch, CURLOPT_SSLCERT, Configure::read('PEM'));
            curl_setopt($ch, CURLOPT_SSLCERTPASSWD, Configure::read('PASSWORD'));
            curl_setopt($ch, CURLOPT_SSLKEY,  Configure::read('SSLKEY'));
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_VERBOSE, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    $curl_return = curl_exec($ch);

	    file_put_contents($last_file, $curl_return);
            return  $last_file;
	}

        function unTarFile ($file, $tmp_path) {
            require_once(APP_DIR.'/vendors/pcltar/pcltar.lib.php');
            return (@PclTarExtract($file, $tmp_path, '', 'tgz'));
	}
   
	function getNewVersion($file, $tmp_path) {
            $version = file_get_contents($tmp_path.'webdelib/'.$file);
            return $version;
        }
}
?>
