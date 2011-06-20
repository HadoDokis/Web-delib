<?php
class ConversionComponent extends Object {

/**
 * conversion de format du fichie $fileUri vers le format $format
 * @return array tableau de réponse composé comme suit :
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
                        file_put_contents('/tmp/toto.odt', file_get_contents($fileName));
			// exécution
			$cmd = "$convertorExec --stdout -f $format $fileName";
			$result = shell_exec($cmd);
                        $this->log( $result );
	        // guess that if there is less than this characters probably an error
	        if (strlen($result) < 10) {
                    return false;
	        } else {
                    return ($result);
	        }
		break;
	}
        return false;
}

}?>
