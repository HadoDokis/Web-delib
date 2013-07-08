<?php
App::uses('File', 'Utility');


class ConnecteursController extends AppController
{
    var $name = 'Connecteurs';

	// Gestion des droits : identiques aux droits des compteurs
    var $commeDroit = array( 'edit'     => 'Connecteurs:index', 
                             'makeconf' => 'Connecteurs:index' );

    function index() {
        $connecteurs = array(-1 => 'Fichier webdelib.inc',
                             0 => 'Génération des documents',
                             1 => 'S2LOW', 
                             2 => 'IParapheur', 
                             3 => 'Pastell', 
                             4 => 'CMIS Alfresco', 
                             5 => 'AS@LAE',
                             6 => 'SMTP',
//                             7 => 'I-Délibre',
                             8 => 'Mode debug');

        $this->set('connecteurs', $connecteurs);
        return true;
    }

    function edit($id) {
        switch ($id) {
            case 0:
                // Connecteur ODFGEDOOo et CLOUDOOo
                $this->render('conversion');
                break;
            case 1:
                // Connecteur S2LOW
                $this->render('s2low');
                break;
            case 2:
                // Connecteur IParapheur
                $this->render('iparapheur');
                break;
            case 3:
                // Connecteur Pastell
                $this->render('pastell');
                break;
            case 4:
                // Connecteur CMIS 
                $this->render('cmis');
                break;
            case 5:
                // Connecteur AS@LAE
                $this->render('asalae');
                break;
            case 6:
                // Connecteur AS@LAE
                $this->render('mail');
                break;
            case 7:
                // Connecteur AS@LAE
                $this->render('idelibre');
                break;
            case 8:
                // Mode Debug
                $this->render('debug');
                break;
            case '-1':
                // Mode Debug
                $this->set('content', file_get_contents(Configure::read('WEBDELIB_PATH').DS.'Config'.DS.'webdelib.inc'));
                $this->render('all');
                break;
            default: 
                $this->Session->setFlash('Ce connecteur n\'est pas valide', 'growl', array('type'=>'erreur') );
                $this->redirect('/connecteurs/index');
       }
    }

    function _replaceValue($content, $param,  $new_value) {
        
        $host_b = "Configure::write('$param', '".Configure::read($param)."');";
        $host_a = "Configure::write('$param', '$new_value');";
        $return=str_replace( $host_b,  $host_a , $content, $count);
        
        if($count===0){
            $host_b = "Configure::write('$param', ".Configure::read($param).");";
            $host_a = "Configure::write('$param', $new_value);";
            
            $return=str_replace( $host_b,  $host_a , $content);
        }
        
        return $return;
    }

    function makeconf($type) {
        $certs = array();
        $file = new File(Configure::read('WEBDELIB_PATH').DS.'Config'.DS.'webdelib.inc' , true);
        $content = $file->read();
        switch($type) {
            case 's2low' :
                $content = $this->_replaceValue($content, 'USE_S2LOW',        $this->data['Connecteur']['use_s2low']);
                $content = $this->_replaceValue($content, 'HOST',        $this->data['Connecteur']['hostname']);
                $content = $this->_replaceValue($content, 'PASSWORD',   $this->data['Connecteur']['password']);
                $content = $this->_replaceValue($content, 'USE_PROXY',   $this->data['Connecteur']['use_proxy']);
                $content = $this->_replaceValue($content, 'HOST_PROXY',   $this->data['Connecteur']['proxy_host']);
                $content = $this->_replaceValue($content, 'USE_MAIL_SECURISE',   $this->data['Connecteur']['use_mails']);
                $content = $this->_replaceValue($content, 'PASSWORD_MAIL_SECURISE',   $this->data['Connecteur']['mails_password']);
                if (file_exists($this->data['Connecteur']['certificat']['tmp_name'])) {
                    $path_dir_s2low = Configure::read('WEBDELIB_PATH').DS.'Config'.DS.'cert_s2low'.DS;
                    $pkcs12 = file_get_contents($this->data['Connecteur']['certificat']['tmp_name']);
                    openssl_pkcs12_read($pkcs12, $certs, $this->data['Connecteur']['password']);
                    file_put_contents($path_dir_s2low.'key.pem', $certs['pkey']);
                    file_put_contents($path_dir_s2low.'client.pem', $certs['cert']);
                    file_put_contents($path_dir_s2low.'ca.pem', $certs['extracerts'][0]);
                // a tester : file_put_contents($path_dir_s2low.'bundle.pem', $certs['extracerts'][1]);
                }
                break;
            case 'iparapheur' : 
                $content = $this->_replaceValue($content, 'WSACTION',   $this->data['Connecteur']['wsaction']);
                $content = $this->_replaceValue($content, 'WSTO',       $this->data['Connecteur']['wsto']);
                $content = $this->_replaceValue($content, 'USE_PARAPH', $this->data['Connecteur']['use_paraph']);
                $content = $this->_replaceValue($content, 'PASSPHRASE', $this->data['Connecteur']['passphrase']);
                $content = $this->_replaceValue($content, 'HTTPAUTH',   $this->data['Connecteur']['login']);
                $content = $this->_replaceValue($content, 'HTTPPASSWD', $this->data['Connecteur']['pass']);
                $content = $this->_replaceValue($content, 'TYPETECH',   $this->data['Connecteur']['typetech']);
                if (file_exists($this->data['Connecteur']['certificat']['tmp_name'])) {
                    $certs = array();
                    $path_dir_parapheur = Configure::read('WEBDELIB_PATH').DS.'Config'.DS.'cert_parapheur'.DS;
                    $pkcs12 = file_get_contents($this->data['Connecteur']['certificat']['tmp_name']);
                    openssl_pkcs12_read($pkcs12, $certs, $this->data['Connecteur']['passphrase']);
                    file_put_contents($path_dir_parapheur.'cert.pem', $certs['pkey'].$certs['cert']);
                    file_put_contents($path_dir_parapheur.'ac.pem', $certs['extracerts'][0]);
                } 
                break;
            case 'conversion' :
                $content = $this->_replaceValue($content, 'GEDOOO_WSDL',   $this->data['Connecteur']['gedooo_url']);
                $content = $this->_replaceValue($content, 'CLOUDOOO_HOST', $this->data['Connecteur']['cloudooo_url']);
                $content = $this->_replaceValue($content, 'CLOUDOOO_PORT', $this->data['Connecteur']['cloudooo_port']);
                break;
            case 'pastell' :
                $content = $this->_replaceValue($content, 'USE_PASTELL',   $this->data['Connecteur']['use_pastell']);
                $content = $this->_replaceValue($content, 'PASTELL_HOST', $this->data['Connecteur']['pastell_host']);
                $content = $this->_replaceValue($content, 'PASTELL_LOGIN', $this->data['Connecteur']['pastell_login']);
                $content = $this->_replaceValue($content, 'PASTELL_PWD', $this->data['Connecteur']['pastell_pwd']);
                $content = $this->_replaceValue($content, 'PASTELL_TYPE', $this->data['Connecteur']['pastell_type']);
                break;
           case 'cmis' :
                $content = $this->_replaceValue($content, 'USE_GED',   $this->data['Connecteur']['use_ged']);
                $content = $this->_replaceValue($content, 'GED_URL', $this->data['Connecteur']['ged_url']);
                $content = $this->_replaceValue($content, 'GED_LOGIN', $this->data['Connecteur']['ged_login']);
                $content = $this->_replaceValue($content, 'GED_PASSWD', $this->data['Connecteur']['ged_passwd']);
                $content = $this->_replaceValue($content, 'GED_REPO', $this->data['Connecteur']['ged_repo']);
                break;
            case 'mail' :
                $content = $this->_replaceValue($content, 'SMTP_USE',     $this->data['Connecteur']['smtp_use']);
                $content = $this->_replaceValue($content, 'MAIL_FROM',    $this->data['Connecteur']['mail_from']);
                $content = $this->_replaceValue($content, 'SMTP_HOST',    $this->data['Connecteur']['smtp_host']);
                $content = $this->_replaceValue($content, 'SMTP_PORT',    $this->data['Connecteur']['smtp_port']);
                $content = $this->_replaceValue($content, 'SMTP_TIMEOUT', $this->data['Connecteur']['smtp_timeout']);
                $content = $this->_replaceValue($content, 'SMTP_USERNAME',$this->data['Connecteur']['smtp_username']);
                $content = $this->_replaceValue($content, 'SMTP_PASSWORD',$this->data['Connecteur']['smtp_password']);
                break;
            case 'asalae' :
                $content = $this->_replaceValue($content, 'USE_ASALAE',     $this->data['Connecteur']['use_asalae']);
                $content = $this->_replaceValue($content, 'USE_GED',   $this->data['Connecteur']['use_ged']);
                $content = $this->_replaceValue($content, 'SIREN_ARCHIVE',   $this->data['Connecteur']['siren_archive']);
                $content = $this->_replaceValue($content, 'NUMERO_AGREMENT', $this->data['Connecteur']['numero_agrement']);
                $content = $this->_replaceValue($content, 'IDENTIFIANT_VERSANT', $this->data['Connecteur']['identifiant_versant']);
                $content = $this->_replaceValue($content, 'MOT_DE_PASSE',     $this->data['Connecteur']['mot_de_passe']);
            case 'debug' :
                $content = $this->_replaceValue($content, 'debug',   $this->data['Connecteur']['debug']);
                break;
            case 'all' :
                $content = $this->data['Connecteur']['all'];
                break;
            case 'idelibre' :
                $content = $this->_replaceValue($content, 'IDELIBRE_HOST',  $this->data['Connecteur']['idelibre_host']);
                break;
            default :
                $this->Session->setFlash('Ce connecteur n\'est pas valide', 'growl', array('type'=>'erreur') );
                $this->redirect('/connecteurs/index');
        } 
        $file->open('w+');
        $file->append($content);
        $file->close();
        
        $this->redirect('/connecteurs/index');
    }

}
?>
