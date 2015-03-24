<?php
class UsersController extends AppController {
    
    public $uses = array(
        'User', 
        'Collectivite', 
        'Service', 
        'Cakeflow.Circuit', 
        'Profil', 
        'Typeacte',
        'Aro',
        'Aco'
        );
    
    public $components = array( 
        'Auth' => array(
                'mapActions' => array(
                    'read' => array(   
                                        'getAdresse',
                                        'getCP',
                                        'getNom',
                                        'getPrenom',
                                        'getVille',
                                        'view',
                                        'admin_index','manager_index'),
                    'changeformatSortie',
                    'changeUserMdp',
                    'changeTheme',
                    'changeServiceEmetteur',
                    'create' => array('admin_add','admin_changeMdp','manager_add','manager_changeMdp'),
                    'update' => array('admin_edit','manager_edit'),
                    'remove' => array('admin_delete','manager_delete'),
                    'allow' => array('login', 'logout')
                                    )
        ),
        'AuthManager.AclManager', 'Filtre', 'Paginator');

    function admin_index() {
        $this->_index();
    }
    
    function manager_index() {
        $this->_index(array('Service.id'));
        
        $this->render('admin_index');
    }
    //FIXME -- optimisation 
    function _index($allow = null)
    {
        $this->Filtre->initialisation($this->name.':'.$this->request->action, $this->request->data);
        $conditions =  $this->Filtre->conditions();
        if (!$this->Filtre->critereExists()) {
            //Définition d'un champ virtuel pour affichage complet des informations
            $this->User->virtualFields['name'] = 'User.prenom || \' \' || User.nom || \' (\' || User.username || \')\'';
            $users = $this->User->find('list', array(
                'fields'=> array('id', 'name'),
                'order' => 'username',
                'recursive' => -1,
                'allow'=> $allow
            ));
            //FIXME -- optimisation : ne pas fournir les options mais utiliser ajax pour alléger le navigateur (chargement page)
            $this->Filtre->addCritere('Utilisateur', array(
                'field' => 'User.id',
                'retourLigne' => true,
                'inputOptions' => array(
                    'empty' => true,
                    'label' =>__('Nom complet', true),
                    'title' => 'Recherche sur par nom, prénom et login',
                    'class' => 'select2',
                    'data-placeholder' => 'Filtre désactivé',
                    'options' => $users)));
            $profils = $this->Profil->find('list', array('fields'=>array('id','name')));
            $this->Filtre->addCritere('Profil', array(
                'field' => 'User.profil_id',
                'retourLigne' => true,
                'inputOptions' => array(
                    'label' =>__('Profil', true),
                    'title' => __('Profil', true)." du(des) utilisateur(s) recherché(s)",
                    'options' => $profils)));
            $this->Filtre->addCritere('Login', array(
                'field' => 'User.username',
                'inputOptions' => array(
                    'label' => __('Login', true),
                    'type'  => 'text',
                    'title' => 'Filtre sur les logins des utilisateurs'),
                'column' => 3));
            $this->Filtre->addCritere('Nom', array(
                'field' => 'User.nom',
                'inputOptions' => array(
                    'label' => __('Nom', true),
                    'type'  => 'text',
                    'title' => 'Filtre sur les noms des utilisateurs'),
                'column' => 3));
            $this->Filtre->addCritere('Prenom', array(
                'field' => 'User.prenom',
                'retourLigne' => true,
                'inputOptions' => array(
                    'label' => __('Prénom', true),
                    'type'  => 'text',
                    'title' => 'Filtre sur les prénoms des utilisateurs'),
                'column' => 3));
        }
        
        $this->paginate = array('User' => array(
            'conditions' => $conditions,
            'fields' => array(
                'DISTINCT User.id', 
                'User.username', 
                'User.nom', 
                'User.prenom', 
                'User.telfixe', 
                'User.telmobile'),
            
            'contain' => array(
                'Profil.name',
                'Service.name',
            ),
            'order' => array('User.login' => 'asc'),
            'limit' => 20,
            'recursive'=>-1,
            'allow'=> $allow
        ));

        $users = $this->Paginator->paginate('User');

        $i=1;
        foreach ($users as &$user) {
            $user['Typeacte'] = $this->User->getTypeActes($user['User']['id']);
            // FIXME Optimiser pour diminuer le nombre de requêtes quand grosse bdd!!
            $user['User']['is_deletable'] = $this->_isDeletable($user, $message);
            
            if($i==20)
                break;
            
            $i++;
        }

        $this->set('users', $users);
    }

    function view($id = null) {
        $user = $this->User->read(null, $id);
        if (!$user) {
            $this->Session->setFlash('Utilisateur introuvable', 'growl');
            $this->redirect($this->referer());
        } else {
            $this->set('user', $user);
            $this->set('circuitDefautLibelle', $this->User->circuitDefaut($id, 'nom'));
            $this->set('canEdit', $this->Droits->check($this->Auth->user('id'), 'Users:edit'));
        }
    }
    
    function admin_add() {
        $this->_add_edit(null);
        
        $this->render('admin_edit');
    }
    
    function manager_add() {
        $this->_add_edit(null, array('Service.id'));
        
        $this->render('admin_edit');
    }

    function admin_edit($id = null) {
        $this->_add_edit($id);
    }
    
    function manager_edit($id = null) {
        $this->_add_edit($id, array('Service.id'));
    }
    
    function _add_edit($id = null, $allow=null)
    {
        // Vérification de l'utilisateur
        if(!empty($id)){
            if (!$this->User->exists($id)) {
                $this->Session->setFlash('Invalide id pour l\'utilisateur', 'growl');
                $this->redirect($this->previous);
            }
        }
        
        $admin=false;
        if($this->request->param('prefix')=='admin'){
          $admin=true;
          
        }
        
        if($this->request->is('Post')) {
            if(!empty($id)){
                $this->User->id=$id;
            }else{
                $this->User->create(); 
            }
            //Transformation type de donnée
            if (!empty($this->request->data['Service']['Service']))
                $this->request->data['Service']['Service'] = explode(',', $this->request->data['Service']['Service']);

            if (!empty($this->data['User']['accept_notif']) && $this->data['User']['accept_notif'] == true) {
                $this->request->data['User']['accept_notif'] = false;
            } else {
                $this->request->data['User']['accept_notif'] = true;
            }

            if ($this->User->save($this->data)) {
                if(!empty($id)){
                    $this->User->id=$this->User->getInsertID();
                }
                $this->AclManager->setPermissionsUser('User', $this->User->id, $this->request->data['Aco']['User']);
                if($admin){
                     $this->AclManager->setPermissionsService('User', $this->User->id, $this->request->data['Aco']['Service']);
                }
                $this->AclManager->setPermissionsTypeacte('User', $this->User->id, $this->request->data['Aco']['Typeacte']);
                $this->AclManager->setPermissionsCircuit('User', $this->User->id, $this->request->data['Aco']['Circuit']);
                
                $this->Session->setFlash('L\'utilisateur \'' . $this->data['User']['username'] . '\' a été modifié', 'growl');
                
                $this->redirect($this->previous);
            } else {
                
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl');
                $this->set('selectedServices', $this->data['Service']['Service']);
                
                $this->redirect($this->here);
            }
        }
        
        $this->request->data = $this->User->find('first', array(
                                        'conditions' => array('User.id' => $id)));

        if(!empty($this->data['Service'])){
            $this->set('selectedServices', $this->_selectedArray($this->data['Service']));
        }
        
        $crud=array();
        if($admin){
            $this->AclManager->permissionsService($id, null, array('create','update','delete','manager')); 
            $this->AclManager->permissionsCircuit($id, 'Cakeflow', array('create','update','delete','manager'));
            $crud[]='manager';
        }
        $this->AclManager->permissionsTypeacte($id, null, array_merge(array('read'), $crud));
        $this->AclManager->permissionsUser($id, null, array_merge(array('read','create','update','delete'), $crud));

        if($admin){
            $this->set('profils', $this->User->Profil->find('list'));
        }
        else {
            $this->set('profils', $this->User->Profil->find('list', array('conditions' => array('role_id'=>1))));
        }
        $this->set('services', $this->User->Service->find('threaded', array(
            'recursive' => -1,
            'order' => 'name ASC',
            'conditions' => array('actif' => 1),
            'fields' => array('id', 'name', 'parent_id')
        )));
        $this->set('notif', array('1' => 'oui', '0' => 'non'));
        $this->set('circuits', $this->Circuit->getList());
    }

    /* dans le controleur car utilisé dans la vue index pour l'affichage */
    function _isDeletable($user, &$message)
    {
        $this->loadModel('Deliberation');
        if ($user['User']['id'] == 1) {
            $message = 'L\'utilisateur \'' . $user['User']['username'] . '\' ne peut pas être supprimé car il est protégé';
            return false;
        } elseif ($user['User']['id'] == $this->Session->read('user.User.id')) {
            $message = 'L\'utilisateur courant \'' . $user['User']['username'] . '\' ne peut pas être supprimé';
            return false;
        } elseif ($this->Deliberation->find('count', array(
            'conditions' => array('Deliberation.redacteur_id' => $user['User']['id']),
            'recursive' => -1))
        ) {
            $message = 'L\'utilisateur \'' . $user['User']['username'] . '\' ne peut pas être supprimé car il est l\'auteur de délibérations';
            return false;
        } else { //Si l'utilisateur à des projets A traiter, ne pas permettre la suppression
            $this->loadModel('Cakeflow.Traitement');
            //A traiter
            $conditions = array();
            $nbProjetsATraiter = count($this->Traitement->listeTargetId(
                $user['User']['id'],
                array('etat' => 'NONTRAITE',
                    'traitement' => 'AFAIRE')));
            //En cours de validation
            $nbProjetsValidation = count($this->Traitement->listeTargetId(
                $user['User']['id'],
                array(
                    'etat' => 'NONTRAITE',
                    'traitement' => 'NONAFAIRE')));
            
            $nbProjets = $nbProjetsATraiter + $nbProjetsValidation;

            if ($nbProjets > 0) {
                $message = 'L\'utilisateur \'' . $user['User']['username'] . '\' ne peut pas être supprimé car il a des projets en cours';
                return false;
            }
        }
        return true;
    }

    function admin_delete($id = null) {
		$messageErreur = '';
		$user = $this->User->find('first' , array(
            'conditions' => array('User.id' => $id),
            'fields'     => array('id', 'username'),
            'recursive'  => -1));
		if (empty($user))
			$this->Session->setFlash('Invalide id pour l\'utilisateur', 'growl');
		elseif (!$this->_isDeletable($user, $messageErreur)) {
			$this->Session->setFlash($messageErreur, 'growl', array('type'=>'erreur'));
		} elseif ($this->User->delete($id)) {
			$this->Session->setFlash('L\'utilisateur \''.$user['User']['username'].'\' a été supprimé', 'growl');
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

    function login()
    {
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                
                $this->User->Profil->recursive=-1;
                $userGroup = $this->User->Profil->findById($this->Auth->user('profil_id'));
                $this->Session->write('User.role', $userGroup['Profil']['role_id']);
                
                $this->User->Service->recursive=-1;
                $userServices = $this->User->UserService->findAllByUserId($this->Auth->user('id'));
                 //$user = $this->User->findByLogin($this->data['User']['username']);
                //services auquels appartient l'agent
                $services = array();
                foreach ($userServices as $service)
                    $services[$service['UserService']['service_id']] = $this->Service->doList($service['UserService']['service_id']);

                $this->Session->write('Auth.User.formatSortie', 0);
                $this->Session->write('Auth.User.Service', $services);
                $this->Session->write('Auth.User.ServiceEmetteur.id', key($services));
                $this->Session->write('Auth.User.ServiceEmetteur.name', $services[key($services)]);
                
                include(ROOT . DS . APP_DIR . DS . 'Config' . DS . 'menu.ini.php');
                $this->_purgeMenuDroit($navbar);
                $this->_purgeMenuHtml($navbar);
                $this->Session->write('Auth.Navbar', $navbar);
                
                $this->Session->setFlash('Bienvenue sur Webdelib', 'growl');
                
                //$this->Auth->redirectUrl()
                return $this->redirect(array('admin'=>false,'prefix'=>false,'controller'=>'pages','action'=>'home'));
            } else {
                $this->Session->setFlash(__("Nom d'user ou mot de passe invalide, réessayer"), 'growl');
            }
        }
        
        $collective = $this->Collectivite->read(array('logo','nom'), 1);
        $this->Session->write('Collective.nom', $collective['Collectivite']['nom']);
        
        App::uses('File', 'Utility');
        $file = new File(WEBROOT_DIR . DS . 'files' . DS . 'image' . DS . 'logo');
        
        if (empty($collective['Collectivite']['logo']))
            $this->set('logo_path',  $this->base . "/files/image/adullact.png");
        else {
            if (!$file->exists()){
                $file->create();
                $file->write($collective['Collectivite']['logo']);
            }
            $file->close();
            $this->set('logo_path',  $this->base . "/files/image/logo");
        }
        
        $this->layout = 'connexion';
        //$this->History->reset();
        
        return;
        //pas de message d'erreur
        $this->set('errorMsg', '');
        //si le formulaire d'authentification a été soumis
        //if (!empty($this->data)) {
            //cherche si utilisateur enregistré possede ce login
            $user = $this->User->findByLogin($this->data['User']['username']);
            unset($user['Historique']);
            if (empty($user)) {
                $this->set('errorMsg', "L'utilisateur " . $this->data['User']['username'] . " n'existe pas dans l'application.");
                $this->layout = 'connexion';
                return $this->render();
            }
            if ($user['User']['id'] == 1) {
                $isAuthentif = ($user['User']['password'] == md5($this->data['User']['password']));
            } else {
                if (Configure::read('USE_LDAP') && Configure::read('LDAP')=='AD') {
                    //Mise en place des defines pour la librairie adLDAP
                    define('LDAP_HOST', Configure::read('LDAP_HOST'));
                    define('LDAP_PORT', Configure::read('LDAP_PORT'));
                    define('LDAP_LOGIN', Configure::read('LDAP_LOGIN'));
                    define('LDAP_PASSWD', Configure::read('LDAP_PASSWD'));
                    define('LDAP_UID', Configure::read('LDAP_UID'));
                    define('LDAP_BASE_DN', Configure::read('LDAP_BASE_DN'));
                    define('ACCOUNT_SUFFIX', Configure::read('LDAP_ACCOUNT_SUFFIX'));
                    define('LDAP_DN', Configure::read('LDAP_DN'));
                    include(ROOT . DS . APP_DIR . DS . 'Vendor/adLDAP.php');
                    $ldap = new adLDAP();
                    $isAuthentif = $ldap->authenticate($this->data['User']['username'], $this->data['User']['password']);
                } elseif (Configure::read('USE_LDAP') && Configure::read('LDAP')=='OPENLDAP')
                    $isAuthentif = $this->_checkLDAP($this->data['User']['username'], $this->data['User']['password']);
                else
                    $isAuthentif = ($user['User']['password'] == md5($this->data['User']['password']));
            }

            ($isAuthentif);
                //on stocke l'utilisateur en session
                $this->Session->write('user', $user);
                // On stock la collectivite de l'utilisateur en cas de PASTELL
                if (Configure::read('USE_PASTELL')) {
                    $coll = $this->Collectivite->find('first', array(
                        'recursive' => -1,
                        'conditions' => array('Collectivite.id' => 1),
                        'fields' => array('id_entity')));
                    $this->Session->write('user.Collectivite', $coll);
                }
                // On stock les natures qu'il peut traiter
                $aro = $this->Aro->find('first', array(
                    'conditions' => array(
                        'Aro.model' => 'User',
                        'Aro.foreign_key' => $user['User']['id']
                    ),
                    'recursive' => -1,
                    'fields' => array('Aro.id')));
                $natures = array();
                $droits = $this->ArosAdo->find('all', array('conditions' => array('aro_id' => $aro['Aro']['id'], '_read' => 1)));
                foreach ($droits as $droit) {
                    if ($droit['Ado']['foreign_key'] != '')
                        $natures[$droit['Ado']['foreign_key']] = substr($droit['Ado']['alias'], 9, strlen($droit['Ado']['alias']));
                }
                $this->Session->write('user.Nature', $natures);

                //services auquels appartient l'agent
                $services = array();
                foreach ($user['Service'] as $service)
                    $services[$service['id']] = $this->Service->doList($service['id']);

                $this->Session->write('user.Service', $services);
                $this->Session->write('user.User.service', key($services));
    }

    function logout() {
        //on supprime les infos utilisateur de la session
        $this->History->reset();
        $this->Session->delete('Auth.Permissions');
        $this->Session->delete('Collectivite');
        //$this->redirect(array('action' => 'username'));
        return $this->redirect($this->Auth->logout());
    }

    function admin_changeMdp($id)
    {
        if (empty($this->data)) {
            $this->request->data = $this->User->read(null, $id);
            if (empty($this->data)) {
                $this->Session->setFlash('Invalide id pour l\'utilisateur', 'growl');
                $this->redirect(array('action' => 'index'));
            } else
                $this->request->data['User']['password'] = '';
        } else {
            if ($this->User->validatesPassword($this->data)) {
                $this->User->id = $id;
                $user = $this->User->find('first', array(
                    'conditions' => array('User.id' => $id),
                    'recursive' => -1));
                if ($this->User->saveField('password', $this->data['User']['password'])) {
                    $this->Session->setFlash('Le mot de passe de l\'utilisateur \'' . $user['User']['username'] . '\' a été modifié', 'growl');
                    $this->redirect(array('action' => 'index'));
                } else
                    $this->Session->setFlash('Erreur lors de la saisie des mots de passe.', 'growl');
            } else
                $this->Session->setFlash('Erreur lors de la saisie des mots de passe.', 'growl');
        }
    }

	function _checkLDAP($login, $password) {
		//  $DN = Configure::read('LDAP_UID')."=$login, ".LDAP_BASE_DN;
		$conn=ldap_connect(Configure::read('LDAP_HOST'), Configure::read('LDAP_PORT')) or  die("connexion impossible au serveur LDAP");
		@ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
		@ldap_set_option($conn, LDAP_OPT_REFERRALS, 0); // required for AD


		$bind_attr = 'dn';
		$search_filter = "(" .Configure::read('LDAP_UID')."=" . $login . ")";
		$result = @ldap_search($conn, Configure::read('LDAP_BASE_DN') , $search_filter, array("dn", $bind_attr));

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
			$this->request->data = $this->User->read(null, $this->Session->read('user.User.id'));
			if (empty($this->data)) {
				$this->Session->setFlash('Invalide id pour l\'utilisateur', 'growl');
				$this->redirect(array('action'=>'index'));
			}
			else
				$this->request->data['User']['password'] = '';
		} else {
			if ($this->User->validatesPassword($this->data) && $this->User->validOldPassword($this->data)) {
                $this->User->id = $this->Session->read('user.User.id');
				if ($this->User->saveField('password', $this->data['User']['password'])) {
					$this->Session->setFlash('Le mot de passe a été modifié', 'growl');
                    $this->redirect(array('action'=>'index'));
				}
				else
					$this->Session->setFlash('Erreur lors de la saisie des mots de passe.', 'growl', array('type'=>'error'));
			}
			else
				$this->Session->setFlash('Erreur lors de la saisie des mots de passe.', 'growl', array('type'=>'error'));
		}
	}

    /**
     * Changement de thême utilisateur
     * Enregistrement du nouveau thême dans les préférences utilisateur (bdd+session)
     */
    public function changeTheme() {
        if (empty($this->request->data)) {
            $this->User->id = $this->Auth->user('id');
            $this->request->data['User']['theme'] = $this->User->field('theme');
            if (empty($this->request->data['User']['theme']))
                $this->request->data['User']['theme'] = 'Normal';
        } else {
            $this->User->id = $this->Auth->user('id');
            if ($this->User->saveField('theme', $this->data['User']['theme'])) {
                $this->Session->write('user.User.theme', $this->data['User']['theme']);
                $this->Session->setFlash('Nouveau thême utilisateur : ' . $this->data['User']['theme'], 'growl');
                return $this->redirect($this->previous);
            } else {
                $this->Session->setFlash('Erreur lors du changement de thême.', 'growl');
            }
        }
        App::uses('Folder', 'Utility');
        $Themed = new Folder(APP . 'View' . DS . 'Themed');
        $dossiers = $Themed->read();

        $this->set('themes', array_combine($dossiers[0], $dossiers[0]));
    }
    
    function changeServiceEmetteur() {
        
        if($this->request->is('Post')){
            $this->Session->write('Auth.User.ServiceEmetteur.id', $this->request->data['User']['ServiceEmetteur']['id']);
            //redirection sur la page où on était avant de changer de service
            $this->redirect($this->previous);
        }
        
        $this->User->Service->recursive=-1;
        $userServices = $this->User->UserService->findAllByUserId($this->Auth->user('id'));
         //$user = $this->User->findByLogin($this->data['User']['username']);
        //services auquels appartient l'agent
        $services = array();
        foreach ($userServices as $service)
            $services[$service['UserService']['service_id']] = $this->Service->doList($service['UserService']['service_id']);

        $this->set('services', $services);
        $this->request->data['User']['ServiceEmetteur']['id'] = $this->Auth->User('ServiceEmetteur.id');
    }
    
    function changeFormatSortie() {
        
        if($this->request->is('Post')){
            $this->Session->write('Auth.User.formatSortie', $this->request->data['User']['formatSortie']);
            //redirection sur la page où on était avant de changer de service
            $this->redirect($this->previous);
        }
        
        $this->request->data['User']['formatSortie'] = $this->Auth->User('formatSortie');
    }
    
    private function _purgeSubMenuDroit(&$subMenu)
    {
        foreach ($subMenu as $key => &$Menu) {
            switch ($Menu['html']) {
                case 'link':
                    $checkDroit=$Menu['check'][0]; 
                    $checkPermission= !empty($Menu['check'][1])?$Menu['check'][1]:'*';
                    
                    //FIX
                    if(isset($Menu['url']['admin']) && $Menu['url']['admin'] && $this->Auth->user('Profil.role_id')!=2)
                    {
                        unset($subMenu[$key]);
                        break;
                    }   
                    if(isset($Menu['url']['manager']) && $Menu['url']['manager'] && $this->Auth->user('Profil.role_id')!=3)
                    {
                        unset($subMenu[$key]);
                        break;
                    }
        
                    if(!$this->Acl->check(
                            array(
                                    'model' => 'User', 
                                    'foreign_key' => $this->Auth->user('id')
                                    ), 
                            $checkDroit, 
                            $checkPermission)){ 
                        unset($subMenu[$key]);
                    }
                    break;
                case 'subMenu':
                    $this->_purgeMenuDroit($Menu['content']);
                break;
            
                default:
                    break;
            }
        }
    }
    
    private function _purgeMenuDroit(&$navbar)
    {
        //Purge menu droit
        foreach ($navbar as $key => &$menu) {
            if(!empty($menu['subMenu'])){
                $this->_purgeSubMenuDroit($menu['subMenu']);
            }
            if(empty($menu['subMenu'])){
                unset($navbar[$key]);
            }
        }
    }
    
    private function _purgeSubMenuHtml(&$subMenu)
    {
        $hasContent=false;
        foreach ($subMenu as $key => &$Menu) {
            if($Menu['html']!='divider' && !$hasContent)
            {
                $hasContent=true;                
            }
            if(!$hasContent && $Menu['html']=='divider')
            {
                unset($subMenu[$key]);
            }
            
            if(isset($previous_value) && $Menu['html']=='divider' && $previous_value=='divider'){
                unset($subMenu[$key]);
            }
            if($Menu['html']=='subMenu'){
                $this->_purgeMenuHtml($Menu['content']);
                if (empty($Menu['content'])) {
                    unset($subMenu[$key]);
                }
            }
            $previous_value= $Menu['html'];
        }
        if(isset($previous_value) && $previous_value=='divider'){
            array_pop($subMenu);
        }
        //Compter le nombre de composant html
        if(!array_key_exists('html', $subMenu)){
            unset($subMenu);
        }
    }
    
    private function _purgeMenuHtml(&$navbar)
    {
        //Purge menu droit
        foreach ($navbar as $key => &$menu) {
            if(!empty($menu['subMenu'])){
                $this->_purgeSubMenuHtml($menu['subMenu']);
            }
            if(empty($menu['subMenu'])){
                unset($navbar[$key]);
            }
        }
        
    }


}