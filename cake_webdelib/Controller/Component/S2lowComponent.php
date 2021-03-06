<?php

/**
 * Code source de la classe S2lowComponent.
 *
 * PHP 5.3
 *
 * @package app.Controller.Component
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
/**
 * Classe S2lowComponent.
 *
 * @package app.Controller.Component
 * 
 */
class S2lowComponent extends Component {


    function send($acte) {
        $url = Configure::read('S2LOW_HOST') . '/modules/actes/actes_transac_create.php';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if (Configure::read('S2LOW_USEPROXY'))
            curl_setopt($ch, CURLOPT_PROXY, Configure::read('S2LOW_PROXYHOST'));

        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $acte);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSLCERT, Configure::read('S2LOW_PEM'));
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, Configure::read('S2LOW_CERTPWD'));
        curl_setopt($ch, CURLOPT_SSLKEY, Configure::read('S2LOW_SSLKEY'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    function getClassification() {
        $sucess = true;
        $pos = strrpos(getcwd(), 'webroot');

        $url = Configure::read('S2LOW_HOST') . '/modules/actes/actes_classification_fetch.php';
        $data = array(
            'api' => '1',
        );
        $url .= '?' . http_build_query($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if (Configure::read('S2LOW_USEPROXY'))
            curl_setopt($ch, CURLOPT_PROXY, Configure::read('S2LOW_PROXYHOST'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSLCERT, Configure::read('S2LOW_PEM'));
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, Configure::read('S2LOW_CERTPWD'));
        curl_setopt($ch, CURLOPT_SSLKEY, Configure::read('S2LOW_SSLKEY'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $reponse = curl_exec($ch);

        if (curl_errno($ch))
            print curl_error($ch);

        curl_close($ch);
        //Passage d'un xml ISO-8859-1 vers utf8
        {
            $reponse = str_replace('ISO-8859-1', 'UTF-8', $reponse);
            $xml = simplexml_load_string(utf8_encode($reponse));
            if ($xml === false && $sucess)
                $sucess = false;
            else {
                $dom_xml = dom_import_simplexml($xml);
                if ($xml === false && $sucess)
                    $sucess = false;
                else {
                    $dom = new DOMDocument('1.0', 'UTF-8');
                    $dom_xml = $dom->importNode($dom_xml, true);
                    $dom->appendChild($dom_xml);
                }
            }
        }
        if ($sucess) {
            $file = new File(Configure::read('S2LOW_CLASSIFICATION'), true);
            $file->delete();
            $file->create();
            $dom->saveXML();
            if ($file->writable())
                $file->write($dom->saveXML());
            else
                $sucess = false;
            $file->close();
        }
        
        //echo "Impossible d'ecrire dans le fichier ($filename)";
        return $sucess;
    }

    function getFluxRetour($tdt_id) {
        $url = Configure::read('S2LOW_HOST') . "/modules/actes/actes_transac_get_status.php?transaction=$tdt_id";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if (Configure::read('S2LOW_USEPROXY'))
            curl_setopt($ch, CURLOPT_PROXY, Configure::read('S2LOW_PROXYHOST'));
        curl_setopt($ch, CURLOPT_POST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSLCERT, Configure::read('S2LOW_PEM'));
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, Configure::read('S2LOW_CERTPWD'));
        curl_setopt($ch, CURLOPT_SSLKEY, Configure::read('S2LOW_SSLKEY'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $curl_return = curl_exec($ch);
        curl_close($ch);
        return($curl_return);
    }

    function getActeTampon($tdt_id) {
        $url = Configure::read('S2LOW_HOST') . "/modules/actes/actes_transac_get_tampon.php?transaction=$tdt_id";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, trim($url));
        if (Configure::read('S2LOW_USEPROXY'))
            curl_setopt($ch, CURLOPT_PROXY, Configure::read('S2LOW_PROXYHOST'));
        curl_setopt($ch, CURLOPT_POST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSLCERT, Configure::read('S2LOW_PEM'));
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, Configure::read('S2LOW_CERTPWD'));
        curl_setopt($ch, CURLOPT_SSLKEY, Configure::read('S2LOW_SSLKEY'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        $curl_return = curl_exec($ch);
        curl_close($ch);
        return $curl_return;
    }

    function getAR($tdt_id) {
        $url = Configure::read('S2LOW_HOST') . "/modules/actes/actes_create_pdf.php?trans_id=$tdt_id";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, trim($url));
        if (Configure::read('S2LOW_USEPROXY'))
            curl_setopt($ch, CURLOPT_PROXY, Configure::read('S2LOW_PROXYHOST'));
        curl_setopt($ch, CURLOPT_POST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSLCERT, Configure::read('S2LOW_PEM'));
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, Configure::read('S2LOW_CERTPWD'));
        curl_setopt($ch, CURLOPT_SSLKEY, Configure::read('S2LOW_SSLKEY'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        $curl_return = curl_exec($ch);
        curl_close($ch);
        return $curl_return;
    }

    function sendMail($data) {
        $url = Configure::read('S2LOW_HOST') . "/modules/mail/api/send-mail.php";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if (Configure::read('S2LOW_USEPROXY'))
            curl_setopt($ch, CURLOPT_PROXY, Configure::read('S2LOW_PROXYHOST'));
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_CAPATH, Configure::read('S2LOW_CAPATH'));
        curl_setopt($ch, CURLOPT_SSLCERT, Configure::read('S2LOW_PEM'));
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, Configure::read('S2LOW_CERTPWD'));
        curl_setopt($ch, CURLOPT_SSLKEY, Configure::read('S2LOW_SSLKEY'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $curl_return = curl_exec($ch);
        curl_close($ch);
        return($curl_return);
    }

    function checkMail($mail_id) {
        $url = Configure::read('S2LOW_HOST') . "/modules/mail/api/detail-mail.php?id=$mail_id";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if (Configure::read('S2LOW_USEPROXY'))
            curl_setopt($ch, CURLOPT_PROXY, Configure::read('S2LOW_PROXYHOST'));
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_CAPATH, Configure::read('S2LOW_CAPATH'));
        curl_setopt($ch, CURLOPT_SSLCERT, Configure::read('S2LOW_PEM'));
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, Configure::read('S2LOW_CERTPWD'));
        curl_setopt($ch, CURLOPT_SSLKEY, Configure::read('S2LOW_SSLKEY'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $curl_return = curl_exec($ch);
        curl_close($ch);
        return($curl_return);
    }
    
     function getDateAR($fluxRetour) {
        // +21 Correspond a la longueur du string : actes:DateReception"
       return substr($fluxRetour, strpos($fluxRetour, 'actes:DateReception') + 21, 10);
    }
    

    function getIDActe() { // Utilisé ? 
        
        $fluxRetour;
        // +21 Correspond a la longueur du string : actes:DateReception"
        return substr($fluxRetour, strpos($fluxRetour, 'actes:DateReception') + 21, 10);
    }

    function getNewFlux($tdt_id) {
        $url = Configure::read('S2LOW_HOST') . "/modules/actes/actes_transac_get_document.php?id=$tdt_id";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if (Configure::read('S2LOW_USEPROXY'))
            curl_setopt($ch, CURLOPT_PROXY, Configure::read('S2LOW_PROXYHOST'));
        curl_setopt($ch, CURLOPT_POST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_CAPATH, Configure::read('S2LOW_CAPATH'));
        curl_setopt($ch, CURLOPT_CAINFO, APP . 'Config/cert_s2low/bundle.pem');
        curl_setopt($ch, CURLOPT_SSLCERT, Configure::read('S2LOW_PEM'));
        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, Configure::read('S2LOW_CERTPWD'));
        curl_setopt($ch, CURLOPT_SSLKEY, Configure::read('S2LOW_SSLKEY'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $curl_return = curl_exec($ch);
        curl_close($ch);
        return($curl_return);
    }
    
    function getDocument($document_id) {
        
        $url = Configure::read('S2LOW_HOST') . "/modules/actes/actes_transac_get_document.php?id=$document_id";
        try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, trim($url));
            if (Configure::read('USE_PROXY'))
                curl_setopt($curl, CURLOPT_PROXY, Configure::read('S2LOW_PROXYHOST'));
            curl_setopt($curl, CURLOPT_POST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSLCERT, Configure::read('S2LOW_PEM'));
            curl_setopt($curl, CURLOPT_SSLCERTPASSWD, Configure::read('S2LOW_CERTPWD'));
            curl_setopt($curl, CURLOPT_SSLKEY, Configure::read('S2LOW_SSLKEY'));
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);

            $folder = new Folder(AppTools::newTmpDir(TMP . 'files' . DS . 'S2low'), true, 0777);
            $file = new File($folder->path . DS . 'WD_S2LOW_DOC', true, 0777);
            $fp = fopen($file->path, 'w');
            curl_setopt($curl, CURLOPT_FILE, $fp);

            $response = curl_exec($curl);

            fclose($fp);
            $content = $file->read();
            $folder->delete();

            if ($response === false) {
                 throw new Exception(curl_error($curl));
                 $this->log(curl_error($ch), 's2low');
            }
        }
        catch (Exception $e)
        {
            return false;
        }
        curl_close($curl);
        
        return $content;
    }
    
    function isNewMessage($delib_id, $type, $reponse, $message_id) {
        $message = $this->TdtMessage->find('first', array('conditions' =>
            array('TdtMessage.delib_id' => $delib_id,
                'TdtMessage.type_message' => $type,
                'TdtMessage.reponse' => $reponse,
                'TdtMessage.message_id' => $message_id)));
        return (empty($message));
    }

}
