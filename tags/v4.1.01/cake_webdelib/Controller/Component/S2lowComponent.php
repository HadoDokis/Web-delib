<?php

class S2lowComponent extends Component {
        var $components = array('Date');   

        function send($acte) {
            $url = 'https://'.Configure::read('HOST').'/modules/actes/actes_transac_create.php';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            if (Configure::read('USE_PROXY'))
                curl_setopt($ch, CURLOPT_PROXY, Configure::read('HOST_PROXY'));

            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $acte );
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            //curl_setopt($ch, CURLOPT_CAPATH, Configure::read('CA_PATH'));
            curl_setopt($ch, CURLOPT_CAINFO, Configure::read('WEBDELIB_PATH').'Config/cert_s2low/bundle.pem');
            curl_setopt($ch, CURLOPT_SSLCERT, Configure::read('PEM'));
            curl_setopt($ch, CURLOPT_SSLCERTPASSWD, Configure::read('PASSWORD'));
            curl_setopt($ch, CURLOPT_SSLKEY,  Configure::read('SSLKEY'));
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            return (curl_exec($ch));
        }

        function getClassification(){
            $pos =  strrpos ( getcwd(), 'webroot');
            $path = substr(getcwd(), 0, $pos);

            $url = 'https://'.Configure::read('HOST').'/modules/actes/actes_classification_fetch.php';
            $data = array(
                'api'           => '1',
            );
            $url .= '?'.http_build_query($data);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            if (Configure::read('USE_PROXY'))
                curl_setopt($ch, CURLOPT_PROXY, Configure::read('HOST_PROXY'));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        //    curl_setopt($ch, CURLOPT_CAPATH, Configure::read('CA_PATH'));
            curl_setopt($ch, CURLOPT_CAINFO, Configure::read('WEBDELIB_PATH').'Config/cert_s2low/bundle.pem');
            curl_setopt($ch, CURLOPT_SSLCERT, Configure::read('PEM'));
            curl_setopt($ch, CURLOPT_SSLCERTPASSWD, Configure::read('PASSWORD'));
            curl_setopt($ch, CURLOPT_SSLKEY, Configure::read('SSLKEY'));
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $reponse = curl_exec($ch);

            if (curl_errno($ch))
                print curl_error($ch);
            curl_close($ch);

            // Assurons nous que le fichier est accessible en ecriture
            if (!$handle = fopen(Configure::read('FILE_CLASS'), 'w')) {
                echo "Impossible d'ouvrir le fichier (".Configure::read('FILE_CLASS').")";
                exit;
            }
            // Ecrivons quelque chose dans notre fichier.
            elseif (fwrite($handle, $reponse) === FALSE) {
                echo "Impossible d'ecrire dans le fichier ($filename)";
                exit;
            }
            else
                return true;
            fclose($handle);
        }

        function getFluxRetour ($tdt_id) {
            $url = 'https://'.Configure::read('HOST')."/modules/actes/actes_transac_get_status.php?transaction=$tdt_id";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            if (Configure::read('USE_PROXY'))
                curl_setopt($ch, CURLOPT_PROXY, Configure::read('HOST_PROXY'));
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
            return($curl_return);
        }

        function getActeTampon ($tdt_id) {
            $url = 'https://'.Configure::read('HOST')."/modules/actes/actes_transac_get_tampon.php?transaction=$tdt_id";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, trim($url));
            if (Configure::read('USE_PROXY'))
                curl_setopt($ch, CURLOPT_PROXY, Configure::read('HOST_PROXY'));
            curl_setopt($ch, CURLOPT_POST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_CAPATH, Configure::read('CA_PATH'));
            curl_setopt($ch, CURLOPT_SSLCERT, Configure::read('PEM'));
            curl_setopt($ch, CURLOPT_SSLCERTPASSWD, Configure::read('PASSWORD'));
            curl_setopt($ch, CURLOPT_SSLKEY,  Configure::read('SSLKEY'));
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

            $curl_return = curl_exec($ch);
            header('Content-type: application/pdf');
            header('Content-Length: '.strlen($curl_return));
            header('Content-Disposition: attachment; filename=Acquittement.pdf');
            echo $curl_return;
            exit();
        }
        

        function getAR($tdt_id, $toFile = false) {
            $toFile = (boolean)$toFile;
            $url = 'https://'.Configure::read('HOST')."/modules/actes/actes_create_pdf.php?trans_id=$tdt_id";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            if (Configure::read('USE_PROXY'))
                curl_setopt($ch, CURLOPT_PROXY, Configure::read('HOST_PROXY'));
            curl_setopt($ch, CURLOPT_POST, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_CAPATH, Configure::read('CA_PATH'));
            curl_setopt($ch, CURLOPT_SSLCERT, Configure::read('PEM'));
            curl_setopt($ch, CURLOPT_SSLCERTPASSWD, Configure::read('PASSWORD'));
            curl_setopt($ch, CURLOPT_SSLKEY,  Configure::read('SSLKEY'));
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $curl_return = curl_exec($ch);
            if ($toFile == false){
                header('Content-type: application/pdf');
                header('Content-Length: '.strlen($curl_return));
                header('Content-Disposition: attachment; filename=Acquittement.pdf');
                echo $curl_return;
                exit();
            }
            else {
                return $curl_return;
            }
        }

        function getDateClassification(){
            $doc = new DOMDocument();
            if(!@$doc->load(Configure::read('FILE_CLASS')))
                return false;
            $date = $doc->getElementsByTagName('DateClassification')->item(0)->nodeValue;
            return ($this->Date->frenchDate(strtotime($date )));
        }

        function sendMail ($data) {
            $url = 'https://'.Configure::read('HOST')."/modules/mail/api/send-mail.php";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            if (Configure::read('USE_PROXY'))
                curl_setopt($ch, CURLOPT_PROXY, Configure::read('HOST_PROXY'));

            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data );
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_CAPATH, Configure::read('CA_PATH'));
            curl_setopt($ch, CURLOPT_SSLCERT, Configure::read('PEM'));
            curl_setopt($ch, CURLOPT_SSLCERTPASSWD, Configure::read('PASSWORD'));
            curl_setopt($ch, CURLOPT_SSLKEY,  Configure::read('SSLKEY'));
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $curl_return = curl_exec($ch);
            return($curl_return);
        }
 
        function checkMail($mail_id) {
            $url = 'https://'.Configure::read('HOST')."/modules/mail/api/detail-mail.php?id=$mail_id";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            if (Configure::read('USE_PROXY'))
                curl_setopt($ch, CURLOPT_PROXY, Configure::read('HOST_PROXY'));

            curl_setopt($ch, CURLOPT_POST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_CAPATH, Configure::read('CA_PATH'));
            curl_setopt($ch, CURLOPT_SSLCERT, Configure::read('PEM'));
            curl_setopt($ch, CURLOPT_SSLCERTPASSWD, Configure::read('PASSWORD'));
            curl_setopt($ch, CURLOPT_SSLKEY,  Configure::read('SSLKEY'));
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_VERBOSE, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $curl_return = curl_exec($ch);
            return($curl_return);
        }

}
?>
