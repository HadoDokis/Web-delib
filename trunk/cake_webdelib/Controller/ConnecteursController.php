<?php

App::uses('File', 'Utility');
class ConnecteursController extends AppController {

    // Gestion des droits : identiques aux droits des compteurs
    public $commeDroit = array(
        'edit' => 'Connecteurs:index',
        'makeconf' => 'Connecteurs:index'
    );

    function index() {
        $connecteurs = array(
            -2 => 'Editer le fichier webdelib.inc',
            -1 => 'Mode debug',
            1 => 'Génération des documents',
            2 => 'Configuration des mails',
            3 => 'Parapheur électronique (Signature)',
            4 => 'Tiers de télétransmission (TDT)',
            5 => 'GED (export CMIS)',
            6 => 'Service d\'archivage électronique (As@lae)',
            7 => 'I-delibRE',
//            10 => 'Pastell',
//            11 => 'S²LOW',
//            12 => 'IParapheur',
//            13 => 'AS@LAE',
        );

        $this->set('connecteurs', $connecteurs);
        return true;
    }

    function edit($id) {
        switch ($id) {
            case -1:
                // Mode Debug
                $this->render('debug');
                break;
            case -2:
                // Mode Config (texte)
                $this->set('content', APP . 'Config' . DS . 'webdelib.inc');
                $this->render('all');
                break;
            case 1:
                // Connecteur ODFGEDOOo et CLOUDOOo
                $this->render('conversion');
                break;
            case 2:
                // Connecteur mails
                $this->render('mail');
                break;
            case 3:
                // Configuration signature
                $protocoles = array('pastell' => 'Pastell', 'iparapheur' => 'i-Parapheur');
                $this->set('protocoles', $protocoles);
                $this->render('signature');
                break;
            case 4:
                // Configuration tdt
                $protocoles = array('pastell' => 'Pastell', 's2low' => 'S²low');
                $this->set('protocoles', $protocoles);
                $this->render('tdt');
                break;
            case 5:
                // Connecteur CMIS
                $this->render('cmis');
                break;
            case 7:
                // Connecteur idelibre
                $this->render('idelibre');
                break;
            case 6:
            case 13:
                // Connecteur AS@LAE
                $this->render('asalae');
                break;
            case 10:
                // Connecteur Pastell
                $this->render('pastell');
                break;
//            case 4:
            case 11:
                // Connecteur S2LOW
                $this->render('s2low');
                break;
            default:
                $this->Session->setFlash('Ce connecteur n\'est pas valide', 'growl', array('type' => 'erreur'));
                return $this->redirect(array('action'=>'index'));
        }
    }

    function _replaceValue($content, $param, $new_value) {
        if (is_bool(Configure::read($param)))
            $valeur = Configure::read($param) === true ? 'true' : 'false';
        else
            $valeur = Configure::read($param);

        $host_b = "Configure::write('$param', '" . $valeur . "');";
        $host_a = "Configure::write('$param', '$new_value');";
        $return = str_replace($host_b, $host_a, $content, $count);
        if ($count === 0) {
            $host_b = "Configure::write('$param', " . $valeur . ");";
            $host_a = "Configure::write('$param', $new_value);";

            $return = str_replace($host_b, $host_a, $content, $count);
        }

        return $return;
    }

    function makeconf($type) {
        $file = new File(APP . 'Config' . DS . 'webdelib.inc', true);
        $content = $file->read();
        switch ($type) {
            case 'signature' :
                $protocol = strtoupper($this->data['Connecteur']['signature_protocol']);
                $content = $this->_replaceValue($content, 'PARAPHEUR', $protocol);
                $content = $this->_replaceValue($content, 'USE_PARAPHEUR', $this->data['Connecteur']['use_signature']);
                if ($protocol != Configure::read('PARAPHEUR'))
                    $content = $this->_replaceValue($content, "USE_".Configure::read('PARAPHEUR'), 'false');
                $content = $this->_replaceValue($content, "USE_$protocol", $this->data['Connecteur']['use_signature']);
                $content = $this->_replaceValue($content, $protocol.'_HOST', $this->data['Connecteur']['host']);
                $content = $this->_replaceValue($content, $protocol.'_LOGIN', $this->data['Connecteur']['login']);
                $content = $this->_replaceValue($content, $protocol.'_PWD', $this->data['Connecteur']['pwd']);
                $content = $this->_replaceValue($content, 'IPARAPHEUR_TYPE', $this->data['Connecteur']['type']);
                if ($protocol == 'PASTELL' && !empty($this->data['Connecteur']['pastelltype']))
                    $content = $this->_replaceValue($content, 'PASTELL_TYPE', $this->data['Connecteur']['pastelltype']);
                if ($protocol == 'IPARAPHEUR'){
                    $content = $this->_replaceValue($content, 'IPARAPHEUR_CERTPWD', $this->data['Connecteur']['certpwd']);
                    if (file_exists($this->data['Connecteur']['clientcert']['tmp_name'])) {
                        $certs = array();
                        $path_dir_parapheur = APP . DS . 'Config' . DS . 'cert_parapheur' . DS;
                        $pkcs12 = file_get_contents($this->data['Connecteur']['clientcert']['tmp_name']);
                        if (openssl_pkcs12_read($pkcs12, $certs, $this->data['Connecteur']['certpwd'])) {
                            file_put_contents($path_dir_parapheur . 'cert.pem', $certs['pkey'] . $certs['cert']);
                            file_put_contents($path_dir_parapheur . 'ac.pem', $certs['extracerts'][0]);
                        }
                        else
                            $this->Session->setFlash('Le mot de passe du certificat est erroné', 'growl', array('type' => 'erreur'));
                    }
                }
                break;
            case 'tdt' :
                $protocol = strtoupper($this->data['Connecteur']['tdt_protocol']);
                $content = $this->_replaceValue($content, 'TDT', $protocol);
                $content = $this->_replaceValue($content, 'USE_TDT', $this->data['Connecteur']['use_tdt']);
                if (Configure::read('TDT') == 'PASTELL')
                    $content = $this->_replaceValue($content, "USE_S2LOW", 'false');
                $content = $this->_replaceValue($content, "USE_$protocol", $this->data['Connecteur']['use_tdt']);
                $content = $this->_replaceValue($content, $protocol.'_LOGIN', $this->data['Connecteur']['login']);
                $content = $this->_replaceValue($content, $protocol.'_PWD', $this->data['Connecteur']['pwd']);
                if ($protocol == 'S2LOW'){
                    if (strpos($this->request->data['Connecteur']['host'], 'https://')===false)
                        $this->request->data['Connecteur']['host'] = 'https://'.$this->request->data['Connecteur']['host'];
                    $content = $this->_replaceValue($content, 'S2LOW_CERTPWD', $this->data['Connecteur']['certpwd']);
                    $content = $this->_replaceValue($content, 'S2LOW_USEPROXY', $this->data['Connecteur']['use_proxy']);
                    $content = $this->_replaceValue($content, 'S2LOW_PROXYHOST', $this->data['Connecteur']['proxy_host']);
                    $content = $this->_replaceValue($content, 'S2LOW_MAILSEC', $this->data['Connecteur']['use_mails']);
                    $content = $this->_replaceValue($content, 'S2LOW_MAILSECPWD', $this->data['Connecteur']['mails_password']);
                    if (file_exists($this->data['Connecteur']['clientcert']['tmp_name'])) {
                        $certs = array();
                        $path_dir_s2low = APP . 'Config' . DS . 'cert_s2low' . DS;
                        $pkcs12 = file_get_contents($this->data['Connecteur']['clientcert']['tmp_name']);
                        if (openssl_pkcs12_read($pkcs12, $certs, $this->data['Connecteur']['certpwd'])) {
                            file_put_contents($path_dir_s2low . 'key.pem', $certs['pkey']);
                            file_put_contents($path_dir_s2low . 'client.pem', $certs['cert']);
                            file_put_contents($path_dir_s2low . 'ca.pem', $certs['extracerts'][0]);
                        }
                        else
                            $this->Session->setFlash('Le mot de passe du certificat est erroné', 'growl', array('type' => 'erreur'));
                    }
                }
                $content = $this->_replaceValue($content, $protocol.'_HOST', $this->request->data['Connecteur']['host']);
                break;
            case 's2low' :
                $content = $this->_replaceValue($content, 'USE_S2LOW', $this->data['Connecteur']['use_s2low']);
                $content = $this->_replaceValue($content, 'HOST', $this->data['Connecteur']['hostname']);
                $content = $this->_replaceValue($content, 'S2LOW_CERTPWD', $this->data['Connecteur']['password']);
                $content = $this->_replaceValue($content, 'S2LOW_USEPROXY', $this->data['Connecteur']['use_proxy']);
                $content = $this->_replaceValue($content, 'S2LOW_PROXYHOST', $this->data['Connecteur']['proxy_host']);
                $content = $this->_replaceValue($content, 'S2LOW_MAILSEC', $this->data['Connecteur']['use_mails']);
                $content = $this->_replaceValue($content, 'S2LOW_MAILSECPWD', $this->data['Connecteur']['mails_password']);
                if (file_exists($this->data['Connecteur']['certificat']['tmp_name'])) {
                    $certs = array();
                    $path_dir_s2low = APP . 'Config' . DS . 'cert_s2low' . DS;
                    $pkcs12 = file_get_contents($this->data['Connecteur']['certificat']['tmp_name']);
                    if (openssl_pkcs12_read($pkcs12, $certs, $this->data['Connecteur']['password'])) {
                        file_put_contents($path_dir_s2low . 'key.pem', $certs['pkey']);
                        file_put_contents($path_dir_s2low . 'client.pem', $certs['cert']);
                        file_put_contents($path_dir_s2low . 'ca.pem', $certs['extracerts'][0]);
                    }
                    else
                        $this->Session->setFlash('Le mot de passe du certificat est erroné', 'growl', array('type' => 'erreur'));
                    // a tester : file_put_contents($path_dir_s2low.'bundle.pem', $certs['extracerts'][1]);
                }
                break;
            case 'conversion' :
                $content = $this->_replaceValue($content, 'GEDOOO_WSDL', $this->data['Connecteur']['gedooo_url']);
                $content = $this->_replaceValue($content, 'CLOUDOOO_HOST', $this->data['Connecteur']['cloudooo_url']);
                $content = $this->_replaceValue($content, 'CLOUDOOO_PORT', $this->data['Connecteur']['cloudooo_port']);
                break;
            case 'cmis' :
                $content = $this->_replaceValue($content, 'USE_GED', $this->data['Connecteur']['use_ged']);
                $content = $this->_replaceValue($content, 'GED_HOST', $this->data['Connecteur']['ged_url']);
                $content = $this->_replaceValue($content, 'GED_LOGIN', $this->data['Connecteur']['ged_login']);
                $content = $this->_replaceValue($content, 'GED_PWD', $this->data['Connecteur']['ged_passwd']);
                $content = $this->_replaceValue($content, 'GED_REPO', $this->data['Connecteur']['ged_repo']);
                break;
            case 'mail' :
                $content = $this->_replaceValue($content, 'SMTP_USE', $this->data['Connecteur']['smtp_use']);
                $content = $this->_replaceValue($content, 'MAIL_FROM', $this->data['Connecteur']['mail_from']);
                $content = $this->_replaceValue($content, 'SMTP_HOST', $this->data['Connecteur']['smtp_host']);
                $content = $this->_replaceValue($content, 'SMTP_PORT', $this->data['Connecteur']['smtp_port']);
                $content = $this->_replaceValue($content, 'SMTP_TIMEOUT', $this->data['Connecteur']['smtp_timeout']);
                $content = $this->_replaceValue($content, 'SMTP_USERNAME', $this->data['Connecteur']['smtp_username']);
                $content = $this->_replaceValue($content, 'SMTP_PASSWORD', $this->data['Connecteur']['smtp_password']);
                break;
            case 'asalae' :
                $content = $this->_replaceValue($content, 'USE_ASALAE', $this->data['Connecteur']['use_asalae']);
                $content = $this->_replaceValue($content, 'ASALAE_WSDL', $this->data['Connecteur']['asalae_wsdl']);
                $content = $this->_replaceValue($content, 'ASALAE_SIREN_ARCHIVE', $this->data['Connecteur']['siren_archive']);
                $content = $this->_replaceValue($content, 'ASALAE_NUMERO_AGREMENT', $this->data['Connecteur']['numero_agrement']);
                $content = $this->_replaceValue($content, 'ASALAE_LOGIN', $this->data['Connecteur']['identifiant_versant']);
                $content = $this->_replaceValue($content, 'ASALAE_PWD', $this->data['Connecteur']['mot_de_passe']);
                break;
            case 'debug' :
                $content = $this->_replaceValue($content, 'debug', $this->data['Connecteur']['debug']);
                break;
            case 'all' :
                $content = $this->data['Connecteur']['all'];
                break;
            case 'idelibre' :
                $content = $this->_replaceValue($content, 'IDELIBRE_HOST', $this->data['Connecteur']['idelibre_host']);
                break;
            default :
                $this->Session->setFlash('Ce connecteur n\'est pas valide', 'growl', array('type' => 'erreur'));
                return $this->redirect(array('action'=>'index'));
        }
        if (!$file->writable()) {
            $this->Session->setFlash('Impossible de modifier le fichier de configuration, veuillez donner les droits sur fichier webdelib.inc', 'growl', array('type' => 'erreur'));
        } else {
            $success = $file->open('w+');
            $success = $success && $file->append($content);
            $success = $success && $file->close();
            if ($success)
                $this->Session->setFlash('La configuration du module &quot;'.$type.'&quot; a été enregistrée', 'growl');
            else
                $this->Session->setFlash('Un problème est survenu lors de la modification du fichier de configuration webdelib.inc', 'growl', array('type' => 'erreur'));
        }

        return $this->redirect(array('action'=>'index'));
    }

}
