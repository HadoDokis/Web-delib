<?php
class UsersController extends AppController {
    public $uses = array('User', 'Collectivite', 'Service', 'Cakeflow.Circuit', 'Profil', 'Typeacte','Aro','Aco');
    public $components = array('Menu', 
        'Auth' => array(
                'mapActions' => array(
                    'read' => array(    'login',
                                        'logout',
                                        'getAdresse',
                                        'getCP',
                                        'getNom',
                                        'getPrenom',
                                        'getVille',
                                        'view',
                                        'changeFormat',
                                        'changeUserMdp',
                                        'changeTheme',
                                        'admin_index'),
                    'create' => array('admin_add','admin_changeMdp'),
                    'update' => array('admin_edit'),
                    'delete' => array('admin_delete'),
                                    ),
        ),
        'AuthManager.AclManager', 'Filtre', 'Paginator');

    //FIXME -- optimisation
    function admin_index()
    {
        $this->view = 'index';
        
        $this->Filtre->initialisation($this->name.':'.$this->request->action, $this->request->data);
        $conditions =  $this->Filtre->conditions();
        if (!$this->Filtre->critereExists()) {
            //Définition d'un champ virtuel pour affichage complet des informations
            $this->User->virtualFields['name'] = 'User.prenom || \' \' || User.nom || \' (\' || User.username || \')\'';
            $users = $this->User->find('list', array(
                'recursive' => -1,
                'fields'=> array('id', 'name'),
                'order' => 'username'
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
            //'conditions' => $conditions,
            'fields' => array('DISTINCT User.id', 'User.username', 'User.nom', 'User.prenom', 'User.telfixe', 'User.telmobile'),
            'limit' => 20,
            'contain' => array(
                'Profil.name',
                'Service.libelle',
                'Aro' => array('conditions' => array('Aro.model' => 'User'))
            ),
            'order' => array('User.login' => 'asc')));

        $users = $this->Paginator->paginate('User');

        //Chercher les droits (types d'acte et supprimable?)
        $i=1;
        foreach ($users as &$user) {
            /*foreach ($user['Aro'] as $aro){
                $aros_ados = $this->ArosAdo->find('all', array(
                    'conditions' => array(
                        'ArosAdo.aro_id' => $aro['id'],
                        'ArosAdo._create' => 1
                    ),
                    'contain' => array('Ado.alias'),
                    'fields' => array('Ado.id')
                ));
                foreach ($aros_ados as $aros_ado)
                    $user['Natures'][] = substr($aros_ado['Ado']['alias'], strlen('Typeacte:'), strlen($aros_ado['Ado']['alias']));
            }*/
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
            $this->set('canEdit', $this->Droits->check($this->user_id, 'Users:edit'));
        }
    }

    function admin_add() {
		// Initialisation
		$sortie = false;

		if (empty($this->data)){
		    // Initialisation des données
		    $this->request->data['User']['accept_notif'] = 0;
		    $this->set('natures', $this->Typeacte->find('all', array('recursive' => -1) ));
		} else {
            //Transformation type de donnée
            if (!empty($this->request->data['Service']['Service']))
                $this->request->data['Service']['Service'] = explode(',', $this->request->data['Service']['Service']);
            if ($this->User->save($this->data)) {
                // Ajout de l'utilisateur dans la table aros
                $user_id = $this->User->id;
                //$this->request->data['Droits'] = $this->Dbdroits->litCruDroits(array('model' => 'Profil', 'foreign_key' => $this->data['User']['profil_id']));
                /*$aro = $this->Aro->find('first', array('conditions' => array('model' => 'User', 'foreign_key' => $user_id),
                    'fields' => array('id'),
                    'recursive' => -1));


                foreach ($this->data['Nature'] as $nature_id => $can) {
                    $nature_id = substr($nature_id, 3, strlen($nature_id));
                    $ado = $this->Ado->find('first', array('conditions' => array('Ado.model' => 'Typeacte',
                        'Ado.foreign_key' => $nature_id),
                        'fields' => array('Ado.id'),
                        'recursive' => -1));

                    if ($can)
                        $this->ArosAdo->allow($aro['Aro']['id'], $ado['Ado']['id']);
                    else
                        $this->ArosAdo->deny($aro['Aro']['id'], $ado['Ado']['id']);
                }*/
                $this->Session->setFlash('L\'utilisateur \'' . $this->data['User']['username'] . '\' a été ajouté', 'growl');
                $sortie = true;
            } else
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl');
        }
        if ($sortie)
            $this->redirect($this->previous);
        else {
            $this->set('selectedCircuits', 0);
            $this->set('services', $this->User->Service->find('threaded', array(
                'recursive' => -1,
                'order' => 'libelle ASC',
                'conditions' => array('actif' => 1),
                'fields' => array('id', 'libelle', 'parent_id')
            )));
            $this->set('selectedServices', null);
            $this->set('profils', $this->User->Profil->find('list'));
            $this->set('notif', array('1' => 'oui', '0' => 'non'));
            $this->set('circuits', $this->Circuit->getList());
            $natures = $this->Typeacte->find('all', array('recursive' => -1));
            foreach ($natures as &$nature)
                $nature['Nature']['check'] = null;
            $this->set('natures', $natures);
            $this->render('edit');
        }
    }

    function admin_edit($id = null)
    {
        if (empty($this->data)) {
            
            $this->request->data = $this->User->find('first', array(
                                            'conditions' => array('User.id' => $id)));
        
            if (empty($this->data)) {
                    $this->Session->setFlash('Invalide id pour l\'utilisateur', 'growl');
                    $this->redirect($this->previous);
            }

            $this->set('selectedServices', $this->_selectedArray($this->data['Service']));

            $this->AclManager->permissionsTypeacte($id);
            $this->AclManager->permissionsProfil($id);
            $this->AclManager->permissionsUser($id);

            //$this->request->data['Droits'] = $this->Dbdroits->litCruDroits(array('model' => 'User', 'foreign_key' => $id));
            $natures = $this->Typeacte->find('all', array('recursive' => -1));
            foreach ($natures as &$nature) {
                $naturesAco = $this->Aco->find('first', array(
                    'fields' => array('Aco.id'),
                    'conditions' => array(
                        'Aco.model' => 'Typeacte', 
                        'Aco.foreign_key' => $nature['Typeacte']['id']),
                    'recursive' => -1));

              //  $nature['Nature']['check'] = $this->ArosAdo->check($aro['Aro']['id'], $ado['Ado']['id']);
            }
            $this->set('natures', $natures);
            $this->set('services', $this->User->Service->find('threaded', array(
                'recursive' => -1,
                'order' => 'libelle ASC',
                'conditions' => array('actif' => 1),
                'fields' => array('id', 'libelle', 'parent_id')
            )));
            $this->set('profils', $this->User->Profil->find('list'));
            $this->set('notif', array('1' => 'oui', '0' => 'non'));
            $this->set('circuits', $this->Circuit->getList());
             //$this->set('listeCtrlAction', $this->Menu->menuCtrlActionAffichage());
            /*$aro = $this->Aro->find('first', array(
                'conditions' => array('model' => 'User', 'foreign_key' => $id),
                'fields' => array('id'),
                'recursive' => -1));

            $natures = $this->Typeacte->find('all', array('recursive' => -1));
            foreach ($natures as &$nature) {
                $naturesAco = $this->Aco->find('first', array('conditions' => array('Aco.model' => 'Typeacte',
                    'Aco.foreign_key' => $nature['Typeacte']['id']),
                    'fields' => array('Aco.id'),
                    'recursive' => -1));
*/
                //$nature['Nature']['check'] = $this->ArosAdo->check($aro['Aro']['id'], $ado['Ado']['id']);
         //   }
            $this->set('natures', $natures);
        } 
        else {
            $userDb = $this->User->find('first', array(
                'conditions' => array('id' => $id), 
                'recursive' => -1));

            $this->AclManager->setPermissionsTypeacte('User', $id, $this->data['Aco']['Typeacte']);
            $this->AclManager->setPermissionsProfil('User', $id, $this->data['Aco']['Profil']);
            $this->AclManager->setPermissionsUser('User',$id, $this->data['Aco']['User']);
            
            $aro = $this->Aro->find('first', array(
                'conditions' => array('model' => 'User', 'foreign_key' => $id),
                'fields' => array('id'),
                'recursive' => -1));

            //Transformation type de donnée
            if (!empty($this->request->data['Service']['Service']))
                $this->request->data['Service']['Service'] = explode(',', $this->request->data['Service']['Service']);

            if(!empty($this->data['User']['accept_notif']) && $this->data['User']['accept_notif']==true)
                $this->request->data['User']['accept_notif']=false;
                    else
                     $this->request->data['User']['accept_notif']=true;
                    
            if ($this->User->save($this->data)) {
                
                /*
                if (!empty($this->data['Nature']))
                    foreach ($this->data['Nature'] as $nature_id => $can) {
                        $nature_id = substr($nature_id, 3, strlen($nature_id));
                        $ado = $this->Ado->find('first', array('conditions' => array('Ado.model' => 'Typeacte',
                            'Ado.foreign_key' => $nature_id),
                            'fields' => array('Ado.id'),
                            'recursive' => -1));

                        if ($can)
                            $this->ArosAdo->allow($aro['Aro']['id'], $ado['Ado']['id']);
                        else
                            $this->ArosAdo->deny($aro['Aro']['id'], $ado['Ado']['id']);
                    }
                if ($userDb['User']['profil_id'] != $this->data['User']['profil_id']) {
                    $this->request->data['Droits'] = $this->Dbdroits->litCruDroits(array('model' => 'Profil', 'foreign_key' => $this->data['User']['profil_id']));
                }

                $this->Dbdroits->MajCruDroits(
                    array('model' => 'User', 'foreign_key' => $id, 'alias' => $this->data['User']['login']),
                    array('model' => 'Profil', 'foreign_key' => $this->data['User']['profil_id']),
                    $this->request->data['Droits']
                );
                 * */
                // */

                $this->Session->setFlash('L\'utilisateur \'' . $this->data['User']['username'] . '\' a été modifié', 'growl');
                $this->redirect($this->previous);
            } else {
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl');
                $this->set('selectedServices', $this->data['Service']['Service']);
                $this->redirect($this->here);
            }
        }
        
        $this->render('edit');
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
                
                $aro = $this->Acl->Aro->find('first', array(
                    'conditions' => array(
                        'Aro.model' => 'User',
                        'Aro.foreign_key' => $this->Auth->user('id'),
                    ),
                    'recursive'=>-1,
                ));
                if(!empty($aro)){
                $acos = $this->Acl->Aco->children();
                foreach($acos as $aco){
                    $tmpacos = $this->Acl->Aco->getPath($aco['Aco']['id']);
                    $path = array();
                    foreach ($tmpacos as $tmpaco) {
                        $path[] = $tmpaco['Aco']['alias'];
                    }
                    $stringPath = implode('/', $path); 
/*
                $permission = $this->Acl->Aro->Permission->find('first', array(
                    'conditions' => array(
                        'Permission.aro_id' => $aro['Aro']['id'],
                        'Permission.aco_id' => $aco['Aco']['id'],
                    ),
                ));*/
                $permissions=array();
                foreach($this->AclManager->getKeys() as $key) {
                    if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Auth->user('id')), $stringPath))
                        $permissions[$key] = true; 
                }
                    $this->Session->write(
                            'Auth.Permissions.'.(!empty($aco['Aco']['model'])?$aco['Aco']['model'].'.':'User.').$stringPath,
                             $permissions
                            );
                }
                }
                /*
                $this->User->Profil->recursive=-1;
                $userGroup = $this->User->Profil->findById($this->Auth->user('profil_id'));
                
                $aro = $this->Acl->Aro->find('first', array(
                    'conditions' => array(
                        'Aro.model' => 'User',
                        'Aro.foreign_key' => $this->Auth->user('id'),
                    ),
                    'recursive'=>-1,
                ));
                if(!empty($aro)){
                $acos = $this->Acl->Aco->children();
                foreach($acos as $aco){
                $permission = $this->Acl->Aro->Permission->find('first', array(
                    'conditions' => array(
                        'Permission.aro_id' => $aro['Aro']['id'],
                        'Permission.aco_id' => $aco['Aco']['id'],
                    ),
                ));
                
                    if(isset($permission['Permission']['id'])){
                        if ($permission['Permission']['_create'] == 1 ||
                            $permission['Permission']['_read'] == 1 ||
                            $permission['Permission']['_update'] == 1 ||
                            $permission['Permission']['_delete'] == 1) {
                                $this->Session->write(
                                    'Auth.Permissions.'.$permission['Aco']['alias'],
                                     true
                                );
                                if(!empty($permission['Aco']['parent_id'])){
                                        $parentAco = $this->Acl->Aco->find('first', array(
                                        'conditions' => array(
                                            'id' => $permission['Aco']['parent_id']
                                        )	
                                    ));
                                        $this->Session->write(
                                        'Auth.Permissions.'.$permission['Aco']['alias']
                                        .'.'.$parentAco['Aco']['alias'], 
                                        true
                                    );
                                }
                            }
                        }   
                } 
                
                }*/
                include(ROOT . DS . APP_DIR . DS . 'Config' . DS . 'menu.ini.php');
                $this->_purgeMenu($navbar);
                $this->Session->write('Auth.Navbar', $navbar);
                
                $this->Session->setFlash('Bienvenue sur Webdelib', 'growl');
                
                return $this->redirect($this->Auth->redirectUrl());
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
        if (!empty($this->data)) {
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

            if ($isAuthentif) {
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

                // Chargement du menu dans la session
                $this->Session->write('menuPrincipal', $this->Menu->load('webDelib', $user['User']['id']));
                
                if (!empty($this->previous)) {
                    $this->redirect($this->previous);
                } else {
                    $this->redirect(array('controller' => 'pages', 'action' => 'home'));
                }
            } else {
                //sinon on prépare le message d'erreur a afficher dans la vue
                $this->set('errorMsg', 'Mauvais identifiant ou  mot de passe.Veuillez recommencer.');
                $this->layout = 'connexion';
            }
        } else {
            $this->layout = 'connexion';
        }
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

    function changeFormat($id) {
        $this->Session->delete('user.format.sortie');
        $this->Session->write('user.format.sortie', $id);
        //redirection sur la page où on était avant de changer de service
        $this->redirect($this->previous);
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
            $this->User->id = $this->user_id;
            $this->request->data['User']['theme'] = $this->User->field('theme');
            if (empty($this->request->data['User']['theme']))
                $this->request->data['User']['theme'] = 'Normal';
        } else {
            $this->User->id = $this->user_id;
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
    
    public function beforeFilter() {
        parent::beforeFilter();
        /*$this->Auth->mapActions(array(
            'create' => array('add'),
            'view' => array('index', 'view')
        ));*/
        
         // Permet aux utilisateurs de se déconnecter
       $this->Auth->allow();
    }
    
    public function isAuthorized ($user) {
        return true;
        if (in_array($this->action, $this->userAllowed)) {
            return true;
        }

        parent::isAuthorized ();
    }
    
    private function _purgeSubMenu(&$subMenu)
    {
        foreach ($subMenu as $key => $Menu) {
            $unset=false;
            switch ($Menu['html']) {
                case 'link':
                    $checkDroit=$Menu['check'][0]; 
                    $checkPermission= !empty($Menu['check'][1])?$Menu['check'][1]:'*';
                    if($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Auth->user('id')), $checkDroit, $checkPermission)){ 
                        continue;
                    }
                    $unset=true;
                    break;
                case 'divider':
                    if(!empty($subMenu[$key-1]['html']) && $subMenu[$key-1]['html']=='divider'){
                        $unset=true;  
                    }
                    break;

                case 'subMenu':
                    $this->_purgeMenu($Menu['content']);
                break;
            
                default:
                   // throw new Exception();
                    break;
            }
            if($unset){
                unset($subMenu[$key]);
            }
        }
    }
    private function _purgeMenu(&$navbar)
    {
        //Purge menu
        foreach ($navbar as $key => $menu) {
            if(!empty($menu['subMenu'])){
                $this->_purgeSubMenu($menu['subMenu']);
            }
            if(empty($menu['subMenu'])){
                unset($navbar[$key]);
            }
        }
        
    }

}