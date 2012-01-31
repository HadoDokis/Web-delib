<?php
class ConversionComponent extends Object {

/**
 * conversion de format du fichie $fileUri vers le format $format
 * @return array tableau de rÃ©ponse composÃ© comme suit :
 * 	'resultat' => boolean
 *  'info' => string
 * 	'convertedFileUri' => nom et chemin du fichier converti
 */
function convertirFichier($fileName, $format) {
	// initialisations
	$ret = array();
	$result = array();
	$convertorType = Configure::read('CONVERSION_TYPE');

	if (empty($convertorType)) {
		$ret['resultat'] = false;
		$ret['info'] = __('Type du programme de conversion non déclaré dans le fichier de configuration de as@lae', true);
		return $ret;
	}

	switch($convertorType) {
	    case 'UNOCONV' :
		// lecture fichier exécutable de unoconv
		$convertorExec = Configure::read('CONVERSION_EXEC');
		if (empty($convertorExec)) {
		    return false;
		}
		// exécution
                $fileName = escapeshellarg($fileName);
		$cmd = "LANG=fr_FR.UTF-8; $convertorExec --stdout -f $format $fileName";
		$result = shell_exec($cmd);
	        // guess that if there is less than this characters probably an error
	        if (strlen($result) < 10) {
                    return false;
	        } else {
                    return ($result);
	        }
		break;
	    case 'CLOUDOOO' : 
                require_once 'XML/RPC.php';
		$content =  base64_encode(file_get_contents($fileName));
		$fileinfo =  pathinfo($fileName);
                if ($fileinfo['extension'] == 'pdf') $fileinfo['extension'] = 'odt';
 
                $params = array( new XML_RPC_Value($content, 'string'),
                                 new XML_RPC_Value($fileinfo['extension'],    'string'),
                                 new XML_RPC_Value($format,    'string'),
                                 new XML_RPC_Value(false,      'boolean'),
				 new XML_RPC_Value(true,       'boolean'));

                $url = Configure::read('CLOUDOOO_HOST').":".Configure::read('CLOUDOOO_PORT');
               
                $msg = new XML_RPC_Message('convertFile', $params);
                $cli = new XML_RPC_Client('/', $url);
                $resp = $cli->send($msg);
                return (base64_decode($resp->xv->me['string']));
           	break;       
	}
        return false;
}




}?>
