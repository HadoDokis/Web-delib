<?php
class UsersController extends AppController {

	var $name = 'Users';
	var $helpers = array('Html', 'Form', 'Html2' );
	var $uses = array('Circuit', 'User', 'Service', 'UsersService', 'Profil', 'Deliberation');
	var $components = array('Utils', 'Acl', 'Menu');

	// Gestion des droits
	var $aucunDroit = array('login', 'logout', 'getAdresse', 'getCP', 'getNom', 'getPrenom', 'getVille', 'view', 'changeFormat');
	var $commeDroit = array('add'=>'Users:index', 'delete'=>'Users:index', 'edit'=>'Users:index', 'changeMdp'=>'Users:index');

	function index() {
		$this->set('users', $this->User->findAll());
	}

	function view($id = null) {
		$user = $this->User->read(null, $id);
		if (!$user) {
			$this->Session->setFlash('Invalide id pour l\'utilisateur.');
			$this->redirect('/users/index');
		} else {
			$this->set('user', $user);
			$this->set('circuitDefautLibelle', $this->User->circuitDefaut($id, 'libelle'));
		}
	}

	function add() {
		// Initialisation
		$sortie = false;

		if (empty($this->data))
			// Initialisation des données
			$this->data['User']['accept_notif'] = 0;
		else {
			$this->cleanUpFields();
			if ($this->User->save($this->data)) {
				// Ajout de l'utilisateur dans la table aros
				$user_id = $this->User->getLastInsertId();
				$aro = new Aro();
				$aro->create( $user_id, null, 'Utilisateur:'.$this->data['User']['login']);
				// Rattachement au profil
				if(!empty($this->data['User']['profil_id'])) {
					$profilLibelle = $this->Profil->field('libelle', 'Profil.id = '.$this->data['User']['profil_id']);
					$aro->setParent('Profil:'.$profilLibelle, $user_id);
				}
				$this->Session->setFlash('L\'utilisateur \''.$this->data['User']['login'].'\' a &eacute;t&eacute; ajout&eacute;');
				$sortie = true;
			} else
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
		}
		if ($sortie)
			$this->redirect('/users/index');
		else {
			$this->set('services', $this->User->Service->generateList());
			$this->set('selectedServices', null);
			$this->set('profils', $this->User->Profil->generateList());
			$this->set('notif', array('1'=>'oui','0'=>'non'));
			$this->set('circuits', $this->Circuit->generateList());
			$this->render('edit');
		}
	}

	function edit($id = null) {
		$sortie = false;
		if (empty($this->data)) {
			$this->data = $this->User->read(null, $id);
			if (empty($this->data)) {
				$this->Session->setFlash('Invalide id pour l\'utilisateur');
				$sortie = true;
			} else
				$this->set('selectedServices', $this->_selectedArray($this->data['Service']));
		} else {
			$this->cleanUpFields();
			if ($this->User->save($this->data)) {
				// Traitement du profil
				$aro = new Aro();
				if(!empty($this->data['User']['profil_id'])) {
					$profilLibelle = $this->Profil->field('libelle', 'Profil.id = '.$this->data['User']['profil_id']);
					$aro->setParent('Profil:'.$profilLibelle, $id);
				} else {
					$aro->setParent('', $id);
				}

				$this->Session->setFlash('L\'utilisateur \''.$this->data['User']['login'].'\' a &eacute;t&eacute; modifi&eacute;');
				$sortie = true;
			} else {
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
				$this->set('selectedServices', $this->data['Service']['Service']);
			}
		}
		if ($sortie)
			$this->redirect('/users/index');
		else {
			$this->set('services', $this->User->Service->generateList());
			$this->set('profils', $this->User->Profil->generateList());
			$this->set('notif',array('1'=>'oui','0'=>'non'));
			$this->set('circuits', $this->Circuit->generateList());
		}
	}

/* dans le controleur car utilisé dans la vue index pour l'affichage */
	function _isDeletable($user, &$message) {
		if ($user['User']['id'] == 1) {
			$message = 'L\'utilisateur \''.$user['User']['login'].'\' ne peut pas être supprimé car il est protégé';
			return false;
		} elseif ($user['User']['id'] == $this->Session->read('user.User.id')) {
			$message = 'L\'utilisateur courant \''.$user['User']['login'].'\' ne peut pas être supprimé';
			return false;
		} elseif (!empty($user['Circuit'])) {
			$message = 'L\'utilisateur \''.$user['User']['login'].'\' ne peut pas être supprimé car il est présent dans un circuit de validation';
			return false;
		} elseif ($this->Deliberation->findCount(array('redacteur_id'=>$user['User']['id']))) {
			$message = 'L\'utilisateur \''.$user['User']['login'].'\' ne peut pas être supprimé car il est l\'auteur de délibérations';
			return false;
		}
		return true;
	}

	function delete($id = null) {
		$messageErreur = '';
		$user = $this->User->read('id, login', $id);
		if (empty($user))
			$this->Session->setFlash('Invalide id pour l\'utilisateur');
		elseif (!$this->_isDeletable($user, $messageErreur)) {
			$this->Session->setFlash($messageErreur);
		} elseif ($this->User->del($id)) {
			$aro = new Aro();
			$aro->delete($id);
			$this->Session->setFlash('L\'utilisateur \''.$user['User']['login'].'\' a &eacute;t&eacute; supprim&eacute;');
		}
	    $this->redirect('/users/index');
	}

    function getNom ($id) {
		$condition = "User.id = $id";
	    $fields = "nom";
	    $dataValeur = $this->User->findAll($condition, $fields);
	   	if (isset($dataValeur['0'] ['User']['nom']))
	   	    return $dataValeur['0'] ['User']['nom'];
	   	else
	   	    return '';
	}

    function getPrenom ($id) {
		$condition = "User.id = $id";
	    $fields = "prenom";
	    $dataValeur = $this->User->findAll($condition, $fields);
	    if (isset($dataValeur['0'] ['User']['prenom']))
	   	    return $dataValeur['0'] ['User']['prenom'];
	   	else
	   	    return '';
	}

	function getAdresse ($id) {
		$condition = "User.id = $id";
	    $fields = "adresse";
	    $dataValeur = $this->User->findAll($condition, $fields);
	   	if (isset($dataValeur['0'] ['User']['adresse']))
	   	    return $dataValeur['0'] ['User']['adresse'];
	   	else
	   	    return '';
	}

	function getCP ($id) {
		$condition = "User.id = $id";
	    $fields = "CP";
	    $dataValeur = $this->User->findAll($condition, $fields);
	   	if (isset($dataValeur['0'] ['User']['CP']))
	   	    return $dataValeur['0'] ['User']['CP'];
	   	else
	   	    return '';
	}

	function getVille ($id) {
		$condition = "User.id = $id";
	    $fields = "ville";
	    $dataValeur = $this->User->findAll($condition, $fields);
	   	if (isset($dataValeur['0'] ['User']['ville']))
	   	    return $dataValeur['0'] ['User']['ville'];
	   	else
	   	    return '';
	}

        function login() {
            //pas de message d'erreur
            $this->set('errorMsg',"");
            //si le formulaire d'authentification a Ã©tÃ© soumis
            if (!empty($this->data)) {
                $isAuthentif = false;
                //cherche si utilisateur enregistrÃ© possede ce login
                $user = $this->User->findByLogin($this->data['User']['login']);
                if (empty($user)){
                    $this->set('errorMsg',"L'utilisateur ".$this->data['User']['login']." n'existe pas dans l'application.");
                    $this->layout='connection';
                    $this->render();
                    exit;
                }

                if ($user['User']['id']==1){
                    $isAuthentif =  ($user['User']['password'] == md5($this->data['User']['password']));
                }
                else {
                    if (USE_LDAP)
                        $isAuthentif = $this->_checkLDAP($this->data['User']['login'], $this->data['User']['password']);
                    else
                        $isAuthentif =  ($user['User']['password'] == md5($this->data['User']['password']));
                }

                if ($isAuthentif) {
                    //on stocke l'utilisateur en session
		    $this->Session->write('user',$user);

                    //services auquels appartient l'agent
                    $services = array();
                    foreach ($user['Service'] as $service)
                        $services[$service['id']] = $this->Service->doList($service['id']);

                    $this->Session->write('user.Service', $services);
                    $this->Session->write('user.User.service', key($services));

                    // Chargement du menu dans la session
                    $this->Session->write('menuPrincipal', $this->Menu->load('webDelib', $user['User']['id']));
                    $this->redirect('/');
                }
                else{
                    //sinon on prÃ©pare le message d'erreur a afficher dans la vue
                    $this->set('errorMsg','Mauvais identifiant ou  mot de passe.Veuillez recommencer.');
                    $this->layout='connection';
                }
            }
            else {
                $this->layout='connection';
            }
        }

 


	function logout() {
		//on supprime les infos utilisateur de la session
		$this->Session->delete('user');
        $this->Session->delete('menuPrincipal');
		$this->redirect('/users/login');
	}

	function changeMdp($id) {
		if (empty($this->data)) {
			$this->data = $this->User->read(null, $id);
			if (empty($this->data)) {
				$this->Session->setFlash('Invalide id pour l\'utilisateur');
				$this->redirect('/users/index');
			} else
				$this->data['User']['password'] = '';
		} else {
			$user = $this->User->read(null, $id);
			$user['User']['password'] = $this->data['User']['password'];
			$user['User']['password2'] = $this->data['User']['password2'];
			$this->cleanUpFields();
			if ($this->User->save($user)) {
				$this->Session->setFlash('Le mot de passe de l\'utilisateur \''.$user['User']['login'].'\' a &eacute;t&eacute; modifi&eacute;');
				$this->redirect('/users/index');
			} else
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
		}
	}

        function changeFormat($id) {
            $this->Session->del('user.format.sortie');
	    $this->Session->write('user.format.sortie', $id);
	    //redirection sur la page où on était avant de changer de service
	    $this->redirect($this->Session->read('user.User.lasturl'));
	}

        function _checkLDAP($login, $password) {
          //  $DN = UNIQUE_ID."=$login, ".BASE_DN;
            $conn=ldap_connect(LDAP_HOST, LDAP_PORT) or  die("connexion impossible au serveur LDAP");
            @ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
            @ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0); // required for AD


        $bind_attr = 'dn';
        $search_filter = "(" .UNIQUE_ID."=" . $login . ")";
        $result = @ldap_search($conn, BASE_DN , $search_filter, array("dn", $bind_attr));

        $info = ldap_get_entries($conn, $result);
        if($info['count'] == 0)
                return false;

        if ($bind_attr == "dn") {
                $found_bind_user = $info[0]['dn'];
        } else {
                $found_bind_user = $info[0][strtolower($bind_attr)][0];
        }
        if (!empty($found_bind_user)) {
            return(@ldap_bind($conn, $info[0]['dn'],  $password));
        } else {
            return false;
        }


        
     }
}
?>
