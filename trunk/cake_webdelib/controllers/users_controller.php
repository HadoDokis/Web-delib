<?php
class UsersController extends AppController {

	var $name = 'Users';
	var $helpers = array('Form', 'Html', 'Html2', 'Session');
	var $uses = array( 'User', 'Service', 'Cakeflow.Circuit', 'Profil', 'Deliberation', 'Nature', 'ArosAdo');
	var $components = array('Utils', 'Acl', 'Menu', 'Dbdroits');
	
	var $paginate = array(
		'User' => array(
			'fields' => array('DISTINCT User.id', 'User.login', 'User.nom', 'User.prenom', 'User.telfixe', 'User.telmobile', 'Profil.libelle'),
			'limit' => 20,
			'joins' => array(
				array(
					'table' => 'users_services',
					'alias' => 'UsersServices',
					'type' => 'LEFT',
					'conditions'=> array(
						'User.id = UsersServices.user_id'
					)
				),
				array(
					'table' => 'services',
					'alias' => 'Service',
					'type' => 'LEFT',
					'conditions'=> array(
						'Service.id = UsersServices.service_id'
					)
				)
			),
			'order' => array(
				'User.login' => 'asc'
			)
		)
	);

	// Gestion des droits
	var $aucunDroit = array(
		'login',
		'logout',
		'getAdresse',
		'getCP',
		'getNom',
		'getPrenom',
		'getVille',
		'view',
		'changeFormat',
		'changeUserMdp',
	);
	
	var $commeDroit = array(
		'add'=>'Users:index',
		'delete'=>'Users:index',
		'edit'=>'Users:index',
		'changeMdp'=>'Users:index'
	);

	function index() {
		/*$users = $this->paginate('User');
		for ($i=0;$i<count($users);$i++) {
			if (isset($users[$i])) {
				for ($j=$i+1;$j<count($users);$j++) {
					if ((isset($users[$j])) && ($users[$i]['User']['id']==$users[$j]['User']['id']))
						$users=Set::remove($users,$j);
				}
			}
		}*/
		//$this->set('users', $users);
		$this->set('users', $this->paginate('User'));
		$this->set('Users', $this);
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

		if (empty($this->data)){
                    // Initialisation des données
                    $this->data['User']['accept_notif'] = 0;
                    $this->set('natures', $this->Nature->find('all'));
                }
		else {
			if ($this->User->save($this->data)) {
				// Ajout de l'utilisateur dans la table aros
				$user_id = $this->User->id;
                                foreach ($this->data['Nature'] as $nature_id => $can) {
                                    $nature_id = substr($nature_id, 3, strlen($nature_id));
                                    if ($can)
                                        $this->ArosAdo->allow($user_id, $nature_id);
                                    else
                                        $this->ArosAdo->deny($user_id, $nature_id);
                                }

				$Profil=$this->Profil->find('first',array('conditions'=>array('id'=>$this->data['User']['profil_id']),'recursive'=>-1));
				$this->data['Droits'] = $this->Dbdroits->litCruDroits(array('model'=>'Profil','foreign_key'=>$this->data['User']['profil_id']));
            	$this->Dbdroits->MajCruDroits(
					array('model'=>'Utilisateur','foreign_key'=>$user_id,'alias'=>$this->data['User']['login']),
					array('model'=>'Profil','foreign_key'=>$this->data['User']['profil_id']),
					$this->data['Droits']
				);
				//$this->_setNewPermissions( $this->data['User']['profil_id'], $user_id, $this->data['User']['login'] );
				$this->Session->setFlash('L\'utilisateur \''.$this->data['User']['login'].'\' a &eacute;t&eacute; ajout&eacute;');
				$sortie = true;
			} else
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
		}
		if ($sortie)
			$this->redirect('/users/index');
		else {
			$this->set('services', $this->User->Service->generatetreelist(array('Service.actif' => 1), null, null, '&nbsp;&nbsp;&nbsp;&nbsp;'));
			$this->set('selectedServices', null);
			$this->set('profils', $this->User->Profil->find('list'));
			$this->set('notif', array('1'=>'oui','0'=>'non'));
			$this->set('circuits', $this->Circuit->getList());
                        $this->set('natures', $this->Nature->find('all'));

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
			} else {
			    $this->set('selectedServices', $this->_selectedArray($this->data['Service']));
			    $this->data['Droits'] = $this->Dbdroits->litCruDroits(array('model'=>'Utilisateur','foreign_key'=>$id));
                            $natures = $this->Nature->find('all');
                            foreach ($natures as &$nature)
                                $nature['Nature']['check'] = $this->ArosAdo->check($id, $nature['Nature']['id']);
                            $this->set('natures', $natures); 
			}
		} else {
			$userDb = $this->User->find('first', array('conditions'=>array('id'=>$id), 'recursive'=>-1));
			if ($this->User->save($this->data)) {
		                foreach ($this->data['Nature'] as $nature_id => $can) {
                                    $nature_id = substr($nature_id, 3, strlen($nature_id));
                                    if ($can)
                                        $this->ArosAdo->allow($id, $nature_id);
                                    else
                                        $this->ArosAdo->deny($id, $nature_id);
                                }
				if ($userDb['User']['profil_id']!=$this->data['User']['profil_id']) {
					$this->data['Droits'] = $this->Dbdroits->litCruDroits(array('model'=>'Profil','foreign_key'=>$this->data['User']['profil_id']));
				}
				
				$this->Dbdroits->MajCruDroits(
					array('model'=>'Utilisateur', 'foreign_key'=>$this->data['User']['id'], 'alias'=>$this->data['User']['login']),
					array('model'=>'Profil','foreign_key'=>$this->data['User']['profil_id']),
					$this->data['Droits']
				);
				
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
			$this->set('services', $this->User->Service->generatetreelist(array('Service.actif' => 1), null, null, '&nbsp;&nbsp;&nbsp;&nbsp;'));
			$this->set('profils', $this->User->Profil->find('list'));
			$this->set('notif',array('1'=>'oui','0'=>'non'));
			$this->set('circuits', $this->Circuit->getList());
			$this->set('listeCtrlAction', $this->Menu->menuCtrlActionAffichage());
                        $natures = $this->Nature->find('all');
                        foreach ($natures as &$nature)
                            $nature['Nature']['check'] = $this->ArosAdo->check($id, $nature['Nature']['id']);
                        $this->set('natures', $natures);

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
			$aro_id = $aro->find('first',array('conditions'=>array('model'=>'Utilisateur', 'foreign_key'=>$id),'fields'=>array('id')));
			$aro->delete($aro_id['Aro']['id']);
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
                    //exit;
                }

                if ($user['User']['id']==1){
                    $isAuthentif =  ($user['User']['password'] == md5($this->data['User']['password']));
                }
                else {
                    if (Configure::read('USE_AD')){
                        include ("vendors/adLDAP.php");
                        $ldap=new adLDAP();
                        $isAuthentif = $ldap->authenticate($this->data['User']['login'], $this->data['User']['password']);
                    }
                    elseif (Configure::read('USE_OPENLDAP'))
                        $isAuthentif = $this->_checkLDAP($this->data['User']['login'], $this->data['User']['password']);
                    else
                        $isAuthentif =  ($user['User']['password'] == md5($this->data['User']['password']));

                }

                if ($isAuthentif) {
 
                    //on stocke l'utilisateur en session
		    $this->Session->write('user',$user);
                    // On stock les natures qu'il peut traiter
                    $natures = array();
                    $droits = $this->ArosAdo->find('all', array('conditions'=> array('aro_id'=>$user['User']['id'], '_read'=>1)));
                    foreach ($droits as $droit){
                        $natures[$droit['Ado']['foreign_key']] = substr($droit['Ado']['alias'], 7, strlen($droit['Ado']['alias']));
                    }
                    $this->Session->write('user.Nature', $natures);

                    //services auquels appartient l'agent
                    $services = array();
                    foreach ($user['Service'] as $service)
                        $services[$service['id']] = $this->Service->doList($service['id']);

                    $this->Session->write('user.Service', $services);
                    $this->Session->write('user.User.service', key($services));

                    // Chargement du menu dans la session
                    $this->Session->write('menuPrincipal', $this->Menu->load('webDelib', $user['User']['id']));
		    		$this->Session->setFlash('Bienvenue '.$user['User']['prenom'], 'growl');
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
                $this->Session->destroy();
		$this->redirect('/users/login');
	}

	function changeMdp($id) {
		if (empty($this->data)) {
			$this->data = $this->User->read(null, $id);
			if (empty($this->data)) {
				$this->Session->setFlash('Invalide id pour l\'utilisateur');
				$this->redirect('/users/index');
			}
			else
				$this->data['User']['password'] = '';
		} else {
			if ($this->User->validatesPassword($this->data)) {
				$user = $this->User->read(null, $id);
				if ($this->User->saveField('password', $this->data['User']['password'])) {
					$this->Session->setFlash('Le mot de passe de l\'utilisateur \''.$user['User']['login'].'\' a &eacute;t&eacute; modifi&eacute;');
					$this->redirect('/users/index');
				}
				else
					$this->Session->setFlash('Erreur lors de la saisie des mots de passe.');
			}
			else
				$this->Session->setFlash('Erreur lors de la saisie des mots de passe.');
		}
	}

    function changeFormat($id) {
        $this->Session->del('user.format.sortie');
	    $this->Session->write('user.format.sortie', $id);
	    //redirection sur la page où on était avant de changer de service
	    $this->redirect($this->Session->read('user.User.lasturl'));
	}

    function _checkLDAP($login, $password) {
          //  $DN = Configure::read('UNIQUE_ID')."=$login, ".BASE_DN;
            $conn=ldap_connect(LDAP_Configure::read('HOST'), LDAP_PORT) or  die("connexion impossible au serveur LDAP");
            @ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
            @ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0); // required for AD


        $bind_attr = 'dn';
        $search_filter = "(" .Configure::read('UNIQUE_ID')."=" . $login . ")";
        $result = @ldap_search($conn, Configure::read('BASE_DN') , $search_filter, array("dn", $bind_attr));

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

	function changeUserMdp() {
		if (empty($this->data)) {
			$this->data = $this->User->read(null, $this->Session->read('user.User.id'));
			if (empty($this->data)) {
				$this->Session->setFlash('Invalide id pour l\'utilisateur');
				$this->redirect('/');
			}
			else
				$this->data['User']['password'] = '';
		} else {
			if (($this->User->validatesPassword($this->data)) && ($this->User->validOldPassword($this->data))) {
				$user = $this->User->read(null, $this->Session->read('user.User.id'));
				if ($this->User->saveField('password', $this->data['User']['password'])) {
					$this->Session->setFlash('Votre mot de passe a &eacute;t&eacute; modifi&eacute;');
					$this->redirect('/');
				}
				else
					$this->Session->setFlash('Erreur lors de la saisie des mots de passe.');
			}
			else
				$this->Session->setFlash('Erreur lors de la saisie des mots de passe.');
		}
	}

}
?>
