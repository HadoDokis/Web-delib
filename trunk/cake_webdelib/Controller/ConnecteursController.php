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
            -1 => 'Editer le fichier webdelib.inc',
            0 => 'Mode debug',
            1 => 'Génération des documents',
            2 => 'Configuration des mails',
            3 => 'Parapheur électronique (Signature)',
            4 => 'Tiers de télétransmission (TDT)',
            5 => 'I-delibRE',
            6 => 'Gestion électronique de document (GED)',
            7 => 'Service d\'archivage électronique (SAE)',
            8 => 'LDAP',
            9 => 'Pastell',
        );

        $this->set('connecteurs', $connecteurs);
        return true;
    }

    function edit($id) {
        switch ($id) {
            case -1:
                // Mode Config (texte)
                $this->set('content', file_get_contents(APP . 'Config' . DS . 'webdelib.inc'));
                $this->render('all');
                break;
            case 0:
                // Mode Debug
                $this->render('debug');
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
                $protocoles = array('PASTELL' => 'Pastell', 'IPARAPHEUR' => 'i-Parapheur');
                if (!Configure::read('USE_PASTELL')){
                    unset($protocoles['PASTELL']);
                }
                $this->set('protocoles', $protocoles);
                $this->render('signature');
                break;
            case 4:
                // Configuration tdt
                $protocoles = array('PASTELL' => 'Pastell', 'S2LOW' => 'S²low');
                 if (!Configure::read('USE_PASTELL')){
                    unset($protocoles['PASTELL']);
                }
                $this->set('protocoles', $protocoles);
                $this->render('tdt');
                break;
            case 5:
                // Connecteur idelibre
                $this->render('idelibre');
                break;
            case 6:
                // Connecteur CMIS
                $this->render('cmis');
                break;
            case 7:
                // Connecteur SAE
                $protocoles = array('PASTELL' => 'Pastell');
                $this->set('protocoles', $protocoles);
                $this->render('sae');
                break;
            case 8:
                // Connecteur LDAP
                $protocoles = array('AD' => 'Active Directory', 'OPENLDAP' => 'OpenLDAP');
                $this->set('protocoles', $protocoles);
                $this->render('ldap');
                break;
            case 9:
                // Connecteur Pastell
                $this->render('pastell');
                break;
            default:
                $this->Session->setFlash('Ce connecteur n\'est pas valide', 'growl', array('type' => 'erreur'));
                return $this->redirect(array('action' => 'index'));
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
                $content = $this->_replaceValue($content, 'IPARAPHEUR_HOST', $this->data['Connecteur']['host']);
                $content = $this->_replaceValue($content, 'IPARAPHEUR_LOGIN', $this->data['Connecteur']['login']);
                $content = $this->_replaceValue($content, 'IPARAPHEUR__PWD', $this->data['Connecteur']['pwd']);
                $content = $this->_replaceValue($content, 'IPARAPHEUR_TYPE', $this->data['Connecteur']['type']);
                if ($protocol == 'IPARAPHEUR') {
                    $content = $this->_replaceValue($content, 'IPARAPHEUR_CERTPWD', $this->data['Connecteur']['certpwd']);
                    if (file_exists($this->data['Connecteur']['clientcert']['tmp_name'])) {
                        $certs = array();
                        $path_dir_parapheur = APP . DS . 'Config' . DS . 'cert_parapheur' . DS;
                        $pkcs12 = file_get_contents($this->data['Connecteur']['clientcert']['tmp_name']);
                        if (openssl_pkcs12_read($pkcs12, $certs, $this->data['Connecteur']['certpwd'])) {
                            file_put_contents($path_dir_parapheur . 'cert.pem', $certs['pkey'] . $certs['cert']);
                            file_put_contents($path_dir_parapheur . 'ac.pem', $certs['extracerts'][0]);
                        } else
                            $this->Session->setFlash('Le mot de passe du certificat est erroné', 'growl', array('type' => 'erreur'));
                    }
                }
                break;
            case 'tdt' :
                $protocol = strtoupper($this->data['Connecteur']['tdt_protocol']);
                $content = $this->_replaceValue($content, 'TDT', $protocol);
                $content = $this->_replaceValue($content, 'USE_TDT', $this->data['Connecteur']['use_tdt']);
                if ($protocol == 'S2LOW') {
                    $content = $this->_replaceValue($content, 'USE_S2LOW', 'true');
                    if (strpos($this->request->data['Connecteur']['host'], 'https://') === false) {
                        $this->request->data['Connecteur']['host'] = 'https://' . $this->request->data['Connecteur']['host'];
                    }
                    $content = $this->_replaceValue($content, 'S2LOW_HOST', $this->data['Connecteur']['host']);
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
                        } else
                            $this->Session->setFlash('Le mot de passe du certificat est erroné', 'growl', array('type' => 'erreur'));
                    }
                }elseif ($protocol == 'PASTELL'){
                    $content = $this->_replaceValue($content, "USE_S2LOW", 'false');
                }
                break;

            case 'idelibre' :
                $content = $this->_replaceValue($content, 'USE_IDELIBRE', $this->data['Connecteur']['use_idelibre']);
                $content = $this->_replaceValue($content, 'IDELIBRE_CONN', $this->data['Connecteur']['idelibre_conn']);
                $content = $this->_replaceValue($content, 'IDELIBRE_LOGIN', $this->data['Connecteur']['idelibre_login']);
                $content = $this->_replaceValue($content, 'IDELIBRE_PWD', $this->data['Connecteur']['idelibre_pwd']);
                $content = $this->_replaceValue($content, 'IDELIBRE_USE_CERT', $this->data['Connecteur']['idelibre_use_cert']);
                if (file_exists($this->data['Connecteur']['clientcert']['tmp_name'])) {
                    $certs = array();
                    $path_cert = APP . 'Config' . DS . 'cert_idelibre' . DS;
                    $pkcs12 = file_get_contents($this->data['Connecteur']['clientcert']['tmp_name']);
                    if (openssl_pkcs12_read($pkcs12, $certs, $this->data['Connecteur']['idelibre_certpwd'])) {
                        file_put_contents($path_cert . 'key.pem', $certs['pkey']);
                        file_put_contents($path_cert . 'client.pem', $certs['cert']);
                        file_put_contents($path_cert . 'ca.pem', $certs['extracerts'][0]);
                        $content = $this->_replaceValue($content, 'IDELIBRE_CERTPWD', $this->data['Connecteur']['idelibre_certpwd']);
                    } else
                        $this->Session->setFlash('Le mot de passe du certificat est erroné', 'growl', array('type' => 'erreur'));
                }
                $content = $this->_replaceValue($content, 'IDELIBRE_HOST', $this->data['Connecteur']['idelibre_host']);
                break;
            case 'conversion' :
                $content = $this->_replaceValue($content, 'GEDOOO_WSDL', $this->data['Connecteur']['gedooo_url']);
                $content = $this->_replaceValue($content, 'CLOUDOOO_HOST', $this->data['Connecteur']['cloudooo_url']);
                $content = $this->_replaceValue($content, 'CLOUDOOO_PORT', $this->data['Connecteur']['cloudooo_port']);
                break;
            case 'cmis' :
                $content = $this->_replaceValue($content, 'USE_GED', $this->data['Connecteur']['use_ged']);
                $content = $this->_replaceValue($content, 'CMIS_HOST', $this->data['Connecteur']['ged_url']);
                $content = $this->_replaceValue($content, 'CMIS_LOGIN', $this->data['Connecteur']['ged_login']);
                $content = $this->_replaceValue($content, 'CMIS_PWD', $this->data['Connecteur']['ged_passwd']);
                $content = $this->_replaceValue($content, 'CMIS_REPO', $this->data['Connecteur']['ged_repo']);
                $content = $this->_replaceValue($content, 'GED_XML_VERSION', $this->data['Connecteur']['ged_xml_version']);
                break;
            case 'mail' :
                $content = $this->_replaceValue($content, 'SMTP_USE', $this->data['Connecteur']['smtp_use']);
                $content = $this->_replaceValue($content, 'MAIL_FROM', $this->data['Connecteur']['mail_from']);
                $content = $this->_replaceValue($content, 'SMTP_HOST', $this->data['Connecteur']['smtp_host']);
                $content = $this->_replaceValue($content, 'SMTP_PORT', $this->data['Connecteur']['smtp_port']);
                $content = $this->_replaceValue($content, 'SMTP_TIMEOUT', $this->data['Connecteur']['smtp_timeout']);
                $content = $this->_replaceValue($content, 'SMTP_USERNAME', $this->data['Connecteur']['smtp_username']);
                $content = $this->_replaceValue($content, 'SMTP_PASSWORD', $this->data['Connecteur']['smtp_password']);
                $content = $this->_replaceValue($content, 'SMTP_CLIENT', $this->data['Connecteur']['smtp_client']);
                
                break;
            case 'sae' :
                $content = $this->_replaceValue($content, 'USE_ASALAE', $this->data['Connecteur']['use_asalae']);
                $content = $this->_replaceValue($content, 'ASALAE_WSDL', $this->data['Connecteur']['asalae_wsdl']);
                $content = $this->_replaceValue($content, 'ASALAE_SIREN_ARCHIVE', $this->data['Connecteur']['siren_archive']);
                $content = $this->_replaceValue($content, 'ASALAE_NUMERO_AGREMENT', $this->data['Connecteur']['numero_agrement']);
                $content = $this->_replaceValue($content, 'ASALAE_LOGIN', $this->data['Connecteur']['identifiant_versant']);
                $content = $this->_replaceValue($content, 'ASALAE_PWD', $this->data['Connecteur']['mot_de_passe']);
                break;
            case 'ldap' :
                $protocol = strtoupper($this->data['Connecteur']['ldap_protocol']);
                $content = $this->_replaceValue($content, 'LDAP', $protocol);
                $content = $this->_replaceValue($content, 'USE_LDAP', $this->data['Connecteur']['use_ldap']);
                $content = $this->_replaceValue($content, 'LDAP_HOST', $this->data['Connecteur']['ldap_host']);
                $content = $this->_replaceValue($content, 'LDAP_PORT', $this->data['Connecteur']['ldap_port']);
                $content = $this->_replaceValue($content, 'LDAP_LOGIN', $this->data['Connecteur']['ldap_login']);
                $content = $this->_replaceValue($content, 'LDAP_PASSWD', $this->data['Connecteur']['ldap_password']);
                $content = $this->_replaceValue($content, 'LDAP_UID', $this->data['Connecteur']['ldap_uid']);
                $content = $this->_replaceValue($content, 'LDAP_BASE_DN', $this->data['Connecteur']['ldap_basedn']);
                $content = $this->_replaceValue($content, 'LDAP_ACCOUNT_SUFFIX', $this->data['Connecteur']['ldap_suffix']);
                $content = $this->_replaceValue($content, 'LDAP_DN', $this->data['Connecteur']['ldap_dn']);
                break;
            case 'pastell' :
                $content = $this->_replaceValue($content, 'USE_PASTELL', $this->data['Connecteur']['use_pastell']);
                $content = $this->_replaceValue($content, 'PASTELL_HOST', $this->data['Connecteur']['host']);
                $content = $this->_replaceValue($content, 'PASTELL_LOGIN', $this->data['Connecteur']['login']);
                $content = $this->_replaceValue($content, 'PASTELL_PWD', $this->data['Connecteur']['pwd']);
                $content = $this->_replaceValue($content, 'PASTELL_TYPE', $this->data['Connecteur']['type']);
                break;
            case 'debug' :
                $content = $this->_replaceValue($content, 'debug', $this->data['Connecteur']['debug']);
                break;
            case 'all' :
                $content = $this->data['Connecteur']['all'];
                break;
            default :
                $this->Session->setFlash('Ce connecteur n\'est pas valide', 'growl', array('type' => 'erreur'));
                return $this->redirect(array('action' => 'index'));
        }
        if (!$file->writable()) {
            $this->Session->setFlash('Impossible de modifier le fichier de configuration, veuillez donner les droits sur fichier webdelib.inc', 'growl', array('type' => 'erreur'));
        } else {
            //TODO : php_check_syntax
            $success = $file->open('w+');
            $success &= $file->append($content);
            $success &= $file->close();
            if ($success)
                $this->Session->setFlash('La configuration du module &quot;' . $type . '&quot; a été enregistrée', 'growl');
            else
                $this->Session->setFlash('Un problème est survenu lors de la modification du fichier de configuration webdelib.inc', 'growl', array('type' => 'erreur'));
        }

        return $this->redirect(array('action' => 'index'));
    }

}
