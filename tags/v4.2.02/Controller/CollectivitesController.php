<?php

/*
 * Created on 24 sept. 07
 */

class CollectivitesController extends AppController {

    public $uses = array('Collectivite', 'User');
    public $components = array();
    // Gestion des droits
    public $aucunDroit = array(
        'synchronize',
        'setMails'
    );
    public $commeDroit = array(
        'edit' => 'Collectivites:index',
        'setLogo' => 'Collectivites:index'
        //FIXME: ajout gd mais à vérifier
        , 'view' => 'Collectivites:index'
        , 'add' => 'Collectivites:index'
        , 'delete' => 'Collectivites:index'
    );

    function index() {
        $collectivite = $this->Collectivite->find('first', array('conditions' => array('Collectivite.id' => 1),
            'recursive' => -1));
        $this->set('collectivite', $collectivite);
        $logo_path = null;
        if (file_exists(APP . WEBROOT_DIR . DS . 'files' . DS . 'image' . DS . 'logo'))
            $logo_path = FULL_BASE_URL . $this->base . "/files/image/logo";

        $this->set('logo_path', $logo_path);
    }

    function edit() {
        if (Configure::read('USE_PASTELL')) {
            $this->Pastell = $this->Components->load('Pastell');
        }
        if (!empty($this->data)) {
            $this->Collectivite->id = 1;
            if (Configure::read('USE_PASTELL')) {
                $entities = $this->Pastell->listEntities();
                $this->request->data['Collectivite']['nom'] = $entities[$this->data['Collectivite']['id_entity']];
            }
            if ($this->Collectivite->save($this->data)){
                $this->Session->setFlash('Informations de la collectivité modifiées', 'growl');
                return $this->redirect($this->previous);
            }
            $this->Session->setFlash('Erreur durant la sauvegarde', 'growl');
        }
        $this->request->data = $this->Collectivite->read(null, 1);
        if (Configure::read('USE_PASTELL')) {
            $this->set('entities', $this->Pastell->listEntities());
            $this->set('selected', $this->data['Collectivite']['id_entity']);
        }
    }

    function setLogo() {
        if (!empty($this->data)) {
           if(!empty($this->data['Collectivite']['logo']['tmp_name'])){
                $image = file_get_contents($this->data['Collectivite']['logo']['tmp_name']);
                $this->Collectivite->id = 1;
                if($this->Collectivite->saveField('logo', $image, true))
                {
                    App::uses('File', 'Utility');
                    $file = new File(WWW_ROOT . 'files' . DS . 'image' . DS . 'logo', true);
                    $file->write($image);
                    $file->close();
                    $this->Session->setFlash('Le Logo a été ajouté', 'growl', array('type' => 'information'));
                    return $this->redirect($this->previous);
                }
            }
            $this->Session->setFlash('Merci de soumettre une image.', 'growl', array('type' => 'error'));
        }
    }

    function setMails() {
        $path = CONFIG_PATH . 'emails/';
        $this->set('email_path', $path);

        if (!empty($this->data)) {
            $cpt = 1;
            foreach ($this->data['Mail'] as $mail) {
                if ($cpt == 1) {
                    if ($mail['name'] == '')
                        continue;
                    $name_file = 'refus';
                    $tmp_file = $mail['tmp_name'];
                    if (!move_uploaded_file($tmp_file, $path . $name_file))
                        exit("Impossible de copier le fichier");
                }
                if ($cpt == 2) {
                    if ($mail['name'] == '')
                        continue;
                    $name_file = 'traiter';
                    $tmp_file = $mail['tmp_name'];
                    if (!move_uploaded_file($tmp_file, $path . $name_file))
                        exit("Impossible de copier le fichier");
                }
                if ($cpt == 3) {
                    if ($mail['name'] == '')
                        continue;
                    $name_file = 'insertion';
                    $tmp_file = $mail['tmp_name'];
                    if (!move_uploaded_file($tmp_file, $path . $name_file))
                        exit("Impossible de copier le fichier");
                }
                if ($cpt == 4) {
                    if ($mail['name'] == '')
                        continue;
                    $name_file = 'convocation';
                    $tmp_file = $mail['tmp_name'];
                    if (!move_uploaded_file($tmp_file, $path . $name_file))
                        exit("Impossible de copier le fichier");
                }
                $cpt++;
            }
            return $this->redirect(array('action'=>'index'));
        }
    }

    function synchronize() {
        $ldapconn = ldap_connect(LDAP_Configure::read('HOST'), PORT) or die("Impossible de se connecter au serveur LDAP {LDAP_Configure::read('HOST')}");
        if ($ldapconn) {
            // bind with appropriate dn to give update access
            $r = ldap_bind($ldapconn, MANAGER, LDAP_PASS);
            if (!$r)
                die("ldap_bind failed<br>");

            $dn = "ou=users,dc=adullact,dc=org";
            $filter = "(|(sn=*))";
            $justthese = array(MAIL, COMMON_NAME, LDAP_UID, PASSWORD_USER);
            $sr = ldap_search($ldapconn, $dn, $filter, $justthese);
            $users = ldap_get_entries($ldapconn, $sr);

            foreach ($users as $user) {
                if (isset($user[LDAP_UID][0]))
                    $login = $user[LDAP_UID][0];

                if (isset($user[PASSWORD_USER][0]))
                    $password = $user[PASSWORD_USER][0];
                else
                    unset($password);

                if (isset($login) && isset($password)) {
                    if (isset($user[MAIL][0]))
                        $mail = $user[MAIL][0];
                    else
                        $mail = "";

                    if (isset($user[COMMON_NAME][0])) {
                        $cn = $user[COMMON_NAME][0];
                        $prenom = substr($cn, 0, strpos($cn, ' '));
                        $nom = substr($cn, strpos($cn, ' '), strlen($cn));
                    }
                    else
                        $cn = "";

                    $data = $this->User->findAll("User.login = '$login'");
                    if (empty($data)) {
                        $this->User->create();
                        $this->data['User']['id'] = '';
                        $this->data['User']['nom'] = $nom;
                        $this->data['User']['prenom'] = $prenom;
                        $this->data['User']['login'] = $login;
                        $this->data['User']['mail'] = $mail;
                        $pwd = base64_decode(substr($password, 5, strlen($password)));
                        $mdp = unpack("H*", $pwd);
                        $this->data['User']['password'] = $mdp[1];
                        $this->User->save($this->data);
                    }
                }
            }
            ldap_close($ldapconn);
        } else {
            echo "Unable to connect to LDAP server";
        }
    }

}
