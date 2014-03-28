<?php
class UsersController extends AppController {
	public $uses = array('User', 'Collectivite', 'Service', 'Cakeflow.Circuit', 'Profil', 'Typeacte', 'ArosAdo', 'Aro', 'Ado');
    public $components = array('Menu', 'Dbdroits', 'Filtre', 'Paginator');

    // Gestion des droits
    public $aucunDroit = array(
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
        'changeTheme'
    );

    public $commeDroit = array(
        'add' => 'Users:index',
        'delete' => 'Users:index',
        'edit' => 'Users:index',
        'changeMdp' => 'Users:index'
    );

    //FIXME -- optimisation
    function index()
    {
        $this->Filtre->initialisation($this->name.':'.$this->request->action, $this->request->data);
        $conditions =  $this->Filtre->conditions();
        if (!$this->Filtre->critereExists()) {
            //Définition d'un champ virtuel pour affichage complet des informations
            $this->User->virtualFields['name'] = 'User.prenom || \' \' || User.nom || \' (\' || User.login || \')\'';
            $users = $this->User->find('list', array(
                'recursive' => -1,
                'fields'=> array('id', 'name'),
                'order' => 'login'
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
            $profils = $this->Profil->find('list', array('fields'=>array('id','libelle')));
            $this->Filtre->addCritere('Profil', array(
                'field' => 'User.profil_id',
                'retourLigne' => true,
                'inputOptions' => array(
                    'label' =>__('Profil', true),
                    'title' => __('Profil', true)." du(des) utilisateur(s) recherché(s)",
                    'options' => $profils),
                'classeDiv' => 'spacer'));
            $this->Filtre->addCritere('Login', array(
                'field' => 'User.login',
                'inputOptions' => array(
                    'label' => __('Login', true),
                    'type'  => 'text',
                    'title' => 'Filtre sur les logins des utilisateurs'),
                'classeDiv' => 'tiers'));
            $this->Filtre->addCritere('Nom', array(
                'field' => 'User.nom',
                'inputOptions' => array(
                    'label' => __('Nom', true),
                    'type'  => 'text',
                    'title' => 'Filtre sur les noms des utilisateurs'),
                'classeDiv' => 'tiers'));
            $this->Filtre->addCritere('Prenom', array(
                'field' => 'User.prenom',
                'retourLigne' => true,
                'inputOptions' => array(
                    'label' => __('Prénom', true),
                    'type'  => 'text',
                    'title' => 'Filtre sur les prénoms des utilisateurs'),
                'classeDiv' => 'tiers'));
        }
        $this->User->Behaviors->attach('Containable');
        $this->ArosAdo->Behaviors->attach('Containable');
        $this->paginate = array('User' => array(
            'conditions' => $conditions,
            'fields' => array('DISTINCT User.id', 'User.login', 'User.nom', 'User.prenom', 'User.telfixe', 'User.telmobile'),
            'limit' => 20,
            'contain' => array(
                'Profil.libelle',
                'Service.libelle',
                'Aro' => array('conditions' => array('Aro.model' => 'User'))
            ),
            'order' => array('User.login' => 'asc')));

        $users = $this->Paginator->paginate('User');

        //Chercher les droits (types d'acte et supprimable?)
        foreach ($users as &$user) {
            foreach ($user['Aro'] as $aro){
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
            }
            /*
            // FIXME Optimiser pour diminuer le nombre de requêtes quand grosse bdd!!
            $user['User']['is_deletable'] = $this->_isDeletable($user, $message);
            */
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

    function add() {
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
                $this->request->data['Droits'] = $this->Dbdroits->litCruDroits(array('model' => 'Profil', 'foreign_key' => $this->data['User']['profil_id']));
                $this->Dbdroits->MajCruDroits(
                    array('model' => 'User', 'foreign_key' => $user_id, 'alias' => $this->data['User']['login']),
                    array('model' => 'Profil', 'foreign_key' => $this->data['User']['profil_id']),
                    $this->data['Droits']
                );
                $aro = $this->Aro->find('first', array('conditions' => array('model' => 'User', 'foreign_key' => $user_id),
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
                }
                $this->Session->setFlash('L\'utilisateur \'' . $this->data['User']['login'] . '\' a été ajouté', 'growl');
                $sortie = true;
            } else
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl');
        }
        if ($sortie)
            $this->redirect(array('action'=>'index'));
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

    function edit($id = null)
    {
        $sortie = false;
        if (empty($this->data)) {
            $this->request->data = $this->User->find('first', array('conditions' => array('User.id' => $id)));
            if (empty($this->data)) {
                $this->Session->setFlash('Invalide id pour l\'utilisateur', 'growl');
                $sortie = true;
            } else {
                $aro = $this->Aro->find('first', array(
                    'conditions' => array('model' => 'User', 'foreign_key' => $id),
                    'fields' => array('id'),
                    'recursive' => -1));

                $this->set('selectedServices', $this->_selectedArray($this->data['Service']));
                $this->request->data['Droits'] = $this->Dbdroits->litCruDroits(array('model' => 'User', 'foreign_key' => $id));
                $natures = $this->Typeacte->find('all', array('recursive' => -1));

                foreach ($natures as &$nature) {
                    $ado = $this->Ado->find('first', array('conditions' => array('Ado.model' => 'Typeacte',
                        'Ado.foreign_key' => $nature['Typeacte']['id']),
                        'fields' => array('Ado.id'),
                        'recursive' => -1));

                    $nature['Nature']['check'] = $this->ArosAdo->check($aro['Aro']['id'], $ado['Ado']['id']);
                }
                $this->set('natures', $natures);
            }
        } else {
            $userDb = $this->User->find('first', array('conditions' => array('id' => $id), 'recursive' => -1));
            $aro = $this->Aro->find('first', array('conditions' => array('model' => 'User', 'foreign_key' => $id),
                'fields' => array('id'),
                'recursive' => -1));

            //Transformation type de donnée
            if (!empty($this->request->data['Service']['Service']))
                $this->request->data['Service']['Service'] = explode(',', $this->request->data['Service']['Service']);

            if ($this->User->save($this->data)) {
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

                $this->Session->setFlash('L\'utilisateur \'' . $this->data['User']['login'] . '\' a été modifié', 'growl');
                $sortie = true;
            } else {
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.', 'growl');
                $this->set('selectedServices', $this->data['Service']['Service']);
            }
        }
        if ($sortie)
            $this->redirect(array('action'=>'index'));
        else {
            $this->set('services', $this->User->Service->find('threaded', array(
                'recursive' => -1,
                'order' => 'libelle ASC',
                'condition' => array('actif' => 1),
                'fields' => array('id', 'libelle', 'parent_id')
            )));
            $this->set('profils', $this->User->Profil->find('list'));
            $this->set('notif', array('1' => 'oui', '0' => 'non'));
            $this->set('circuits', $this->Circuit->getList());
            $this->set('listeCtrlAction', $this->Menu->menuCtrlActionAffichage());
            $aro = $this->Aro->find('first', array(
                'conditions' => array('model' => 'User', 'foreign_key' => $id),
                'fields' => array('id'),
                'recursive' => -1));

            $natures = $this->Typeacte->find('all', array('recursive' => -1));
            foreach ($natures as &$nature) {
                $ado = $this->Ado->find('first', array('conditions' => array('Ado.model' => 'Typeacte',
                    'Ado.foreign_key' => $nature['Typeacte']['id']),
                    'fields' => array('Ado.id'),
                    'recursive' => -1));

                $nature['Nature']['check'] = $this->ArosAdo->check($aro['Aro']['id'], $ado['Ado']['id']);
            }
            $this->set('natures', $natures);
        }
    }

    /* dans le controleur car utilisé dans la vue index pour l'affichage */
    function _isDeletable($user, &$message)
    {
        $this->loadModel('Deliberation');
        if ($user['User']['id'] == 1) {
            $message = 'L\'utilisateur \'' . $user['User']['login'] . '\' ne peut pas être supprimé car il est protégé';
            return false;
        } elseif ($user['User']['id'] == $this->Session->read('user.User.id')) {
            $message = 'L\'utilisateur courant \'' . $user['User']['login'] . '\' ne peut pas être supprimé';
            return false;
        } elseif ($this->Deliberation->find('count', array(
            'conditions' => array('Deliberation.redacteur_id' => $user['User']['id']),
            'recursive' => -1))
        ) {
            $message = 'L\'utilisateur \'' . $user['User']['login'] . '\' ne peut pas être supprimé car il est l\'auteur de délibérations';
            return false;
        } else { //Si l'utilisateur à des projets A traiter, ne pas permettre la suppression
            $this->loadModel('Cakeflow.Traitement');
            //A traiter
            $conditions = array();
            $conditions['Deliberation.id'] = $this->Traitement->listeTargetId(
                $user['User']['id'],
                array('etat' => 'NONTRAITE',
                    'traitement' => 'AFAIRE'));
            $conditions['Deliberation.etat'] = 1;
            $conditions['Deliberation.parent_id'] = NULL;
            $nbProjetsATraiter = $this->Deliberation->find('count', array('conditions' => $conditions, 'recursive' => -1));
            //En cours de validation
            $conditions = array();
            $conditions['Deliberation.etat'] = 1;
            $conditions['Deliberation.parent_id'] = NULL;
            $conditions['OR']['Deliberation.id'] = $this->Traitement->listeTargetId(
                $user['User']['id'],
                array(
                    'etat' => 'NONTRAITE',
                    'traitement' => 'NONAFAIRE'));
            $conditions['OR']['Deliberation.redacteur_id'] = $user['User']['id'];
            $nbProjetsValidation = $this->Deliberation->find('count', array('conditions' => $conditions, 'recursive' => -1));
            $nbProjets = $nbProjetsATraiter + $nbProjetsValidation;

            if ($nbProjets > 0) {
                $message = 'L\'utilisateur \'' . $user['User']['login'] . '\' ne peut pas être supprimé car il a des projets en cours';
                return false;
            }
        }
        return true;
    }

    function delete($id = null) {
		$messageErreur = '';
		$user = $this->User->find('first' , array(
            'conditions' => array('User.id' => $id),
            'fields'     => array('id', 'login'),
            'recursive'  => -1));
		if (empty($user))
			$this->Session->setFlash('Invalide id pour l\'utilisateur', 'growl');
		elseif (!$this->_isDeletable($user, $messageErreur)) {
			$this->Session->setFlash($messageErreur, 'growl', array('type'=>'erreur'));
		} elseif ($this->User->delete($id)) {
			$aro = new Aro();
			$aro_id = $aro->find('first',array('conditions'=>array('model'=>'User', 'foreign_key'=>$id),'fields'=>array('id')));
			$aro->delete($aro_id['Aro']['id']);
			$this->Session->setFlash('L\'utilisateur \''.$user['User']['login'].'\' a été supprimé', 'growl');
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
        //pas de message d'erreur
        $this->set('errorMsg', '');
        $collective = $this->Collectivite->read(array('logo','nom'), 1);
        App::uses('File', 'Utility');
        $file = new File(WEBROOT_PATH . DS . 'files' . DS . 'image' . DS . 'logo.jpg', false);

        if (empty($collective['Collectivite']['logo']))
            $this->set('logo_path',  $this->base . "/files/image/adullact.png");
        else {
            if (!$file->exists())
                $file->write($collective['Collectivite']['logo']);

            $file->close();
            $this->set('logo_path',  $this->base . "/files/image/logo.jpg");
        }

        //si le formulaire d'authentification a été soumis
        if (!empty($this->data)) {
            //cherche si utilisateur enregistré possede ce login
            $user = $this->User->findByLogin($this->data['User']['login']);
            unset($user['Historique']);
            if (empty($user)) {
                $this->set('errorMsg', "L'utilisateur " . $this->data['User']['login'] . " n'existe pas dans l'application.");
                $this->layout = 'connexion';
                return $this->render();
            }
            if ($user['User']['id'] == 1) {
                $isAuthentif = ($user['User']['password'] == md5($this->data['User']['password']));
            } else {
                if (Configure::read('USE_AD')) {
                    include(ROOT . DS . APP_DIR . DS . "Vendor/adLDAP.php");
                    $ldap = new adLDAP();
                    $isAuthentif = $ldap->authenticate($this->data['User']['login'], $this->data['User']['password']);
                } elseif (Configure::read('USE_OPENLDAP'))
                    $isAuthentif = $this->_checkLDAP($this->data['User']['login'], $this->data['User']['password']);
                else
                    $isAuthentif = ($user['User']['password'] == md5($this->data['User']['password']));
            }

            if ($isAuthentif) {
                //on stocke l'utilisateur en session
                $this->Session->write('user', $user);
                $this->Session->write('user.collective', array('nom'=>$collective['Collectivite']['nom']));
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
                $this->Session->setFlash('Bienvenue sur Webdelib', 'growl');
                if (!empty($this->previous))
                    $this->redirect($this->previous);
                else
                    $this->redirect('/');
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
        $this->Session->delete('user');
		$this->redirect(array('action' => 'login'));
	}

    function changeMdp($id)
    {
        if (empty($this->data)) {
            $this->request->data = $this->User->read(null, $id);
            if (empty($this->data)) {
                $this->Session->setFlash('Invalide id pour l\'utilisateur');
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
                    $this->Session->setFlash('Le mot de passe de l\'utilisateur \'' . $user['User']['login'] . '\' a été modifié');
                    $this->redirect(array('action' => 'index'));
                } else
                    $this->Session->setFlash('Erreur lors de la saisie des mots de passe.');
            } else
                $this->Session->setFlash('Erreur lors de la saisie des mots de passe.');
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
				$this->Session->setFlash('Invalide id pour l\'utilisateur');
				$this->redirect(array('action'=>'index'));
			}
			else
				$this->request->data['User']['password'] = '';
		} else {
			if ($this->User->validatesPassword($this->data) && $this->User->validOldPassword($this->data)) {
                $this->User->id = $this->Session->read('user.User.id');
				if ($this->User->saveField('password', $this->data['User']['password'])) {
					$this->Session->setFlash('Le mot de passe a été modifié');
                    $this->redirect(array('action'=>'index'));
				}
				else
					$this->Session->setFlash('Erreur lors de la saisie des mots de passe.');
			}
			else
				$this->Session->setFlash('Erreur lors de la saisie des mots de passe.');
		}
	}

    /**
     * Changement de thême utilisateur
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
                $this->Session->setFlash('Le thême a été modifié', 'growl');
                $this->redirect($this->previous);
            } else
                $this->Session->setFlash('Erreur lors du changement de thême.', 'growl');
        }
        App::uses('Folder', 'Utility');
        $Themed = new Folder(APP . 'View' . DS . 'Themed');
        $dossiers = $Themed->read();

        $this->set('themes', array_combine($dossiers[0],$dossiers[0]));
    }

}