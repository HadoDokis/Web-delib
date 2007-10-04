<?php
class UsersController extends AppController {

	var $name = 'Users';
	var $helpers = array('Html', 'Form', 'Html2' );
	var $uses = array('Circuit', 'User', 'Service', 'UsersService');
	var $components = array('Utils');
	
	function index() {
		//$this->User->recursive = 0;
		$this->params['data']= $this->User->findAll();
		$data=$this->params['data'];
		for ($i=0; $i<count($data); $i++) {
			$data[$i]['User']['created']=$this->Utils->mysql_DateTime($data[$i]['User']['created']);
			$data[$i]['User']['modified']=$this->Utils->mysql_DateTime($data[$i]['User']['modified']);
		}
		//debug($data);
		$this->set('users', $data);
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour l\'utilisateur.');
			$this->redirect('/users/index');
		}
		$this->set('user', $this->User->read(null, $id));
	}

	function add() {
	
		if (empty($this->data)) {
			$this->set('services', $this->User->Service->generateList());
			$this->set('selectedServices', null);
			$this->set('circuits', $this->User->Circuit->generateList());
			$this->set('selectedCircuits', null);
			$this->set('profils', $this->User->Profil->generateList());
			$this->set('selectedProfils', null);
			$this->set('notif',array('1'=>'oui','0'=>'non'));

		} else {
			$this->data['User']['password']=md5($this->data['User']['password']);
			$this->cleanUpFields('User');
	
			if ($this->User->isUnique('login', $this->data['User']['login'],$user_id='null') && $this->User->save($this->data)) {
				$this->Session->setFlash('L\'utilisateur a &eacute;t&eacute; sauvegard&eacute;');
				$this->redirect('/users/index');
			} else {
			
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
				//$this->set('statut',array('0'=>'agent', '1'=>'elu'));
				$this->set('services', $this->User->Service->generateList());
				if (empty($this->data['Service']['Service'])) { 
				    $this->data['Service']['Service'] = null;
				}
				$this->set('selectedServices', $this->data['Service']['Service']);
				$this->set('circuits', $this->User->Circuit->generateList());
				if (empty($this->data['Circuit']['Circuit'])) { 
				    $this->data['Circuit']['Circuit'] = null; 
				}
				$this->set('selectedCircuits', $this->data['Circuit']['Circuit']);
				$this->set('profils', $this->User->Profil->generateList());
				if (empty($this->data['Profil']['Profil'])) {
					$this->data['Profil']['Profil'] = null;
				}
				$this->set('selectedProfils', $this->data['Profil']['Profil']);
				$this->data['User']['password']=null;
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalide id pour l\'utilisateur');
				$this->redirect('/users/index');
			}
			$this->data = $this->User->read(null, $id);
			$this->set('notif',array('0'=>'non', '1'=>'oui'));
			$this->set('services', $this->User->Service->generateList());
			if (empty($this->data['Service'])) { 
				$this->data['Service'] = null;
				$this->set('selectedServices', $this->data['ServiceElu']['id']);
			}else{
				$this->data['ServiceElu'] = null;
				$this->set('selectedServices', $this->_selectedArray($this->data['Service']));
			}
			$this->set('circuits', $this->User->Circuit->generateList());
			if (empty($this->data['Circuit'])) { 
				$this->data['Circuit'] = null; 
			}
			$this->set('selectedCircuits', $this->_selectedArray($this->data['Circuit']));
			$this->set('profils', $this->User->Profil->generateList());
			if (empty($this->data['Profil'])) { 
				$this->data['Profil'] = null; 
			}
			$this->set('selectedProfils', $this->_selectedArray($this->data['Profil']));
			
		} else {
			if ($this->data['User']['statut']=='0'){					
				$this->data['User']['service_id']=null;
			}else{
				$this->data['Service']['Service']=array();			
			}
			$this->data['User']['password']=md5($this->data['User']['password']);
			$this->cleanUpFields('User');
			//debug($this->data);
			
			if ($this->User->isUnique('login', $this->data['User']['login'],$id) && $this->User->save($this->data)) {
				$this->Session->setFlash('L\'utilisateur a &eacute;t&eacute; modifi&eacute;');
				$this->redirect('/users/index');
			} else {
				//debug($data);
				$this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
				//$this->set('statut',array('0'=>'agent', '1'=>'elu'));
				$this->set('services', $this->User->Service->generateList());
				if (empty($this->data['Service']['Service'])) { $this->data['Service']['Service'] = null; }
				$this->set('selectedServices', $this->data['Service']['Service']);
				$this->set('circuits', $this->User->Circuit->generateList());
				if (empty($this->data['Circuit']['Circuit'])) { $this->data['Circuit']['Circuit'] = null; }
				$this->set('selectedCircuits', $this->data['Circuit']['Circuit']);
				$this->set('profils', $this->User->Profil->generateList());
				if (empty($this->data['Profil']['Profil'])) { $this->data['Profil']['Profil'] = null; }
				$this->set('selectedProfils', $this->data['Profil']['Profil']);
			}
		}	
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour l\'utilisateur');
			$this->redirect('/users/index');
		}
		if ($this->User->del($id)) {
			$this->Session->setFlash('L\'utilisateur a &eacute;t&eacute; supprim&eacute;');
			$this->redirect('/users/index');
		}
	}

	
    function getNom ($id) {
		$condition = "User.id = $id";
	    $fields = "nom";
	    $dataValeur = $this->User->findAll($condition, $fields);
	   	return $dataValeur['0'] ['User']['nom'];
	}
	
    function getPrenom ($id) {
		$condition = "User.id = $id";
	    $fields = "prenom";
	    $dataValeur = $this->User->findAll($condition, $fields);
	   	return $dataValeur['0'] ['User']['prenom'];
	}
	
function login()
	{
		//pas de message d'erreur
		$this->set('errorMsg',"");
		
		//si le formulaire d'authentification a été soumis
		if (!empty($this->data))
		{
			//cherche si utilisateur enregistré possede ce login
			$user = $this->User->findByLogin($this->data['User']['login']);
			
			//si le mdp n'est pas vide et correspond a celui de la bdd
			if (!empty($user['User']['password']) && ($user['User']['password'] == md5($this->data['User']['password']))) 
			{



				//on stocke l'utilisateur en session
				$this->Session->write('user',$user);
				//debug($this->Session->read());

				//services auquels appartient l'agent
				if(empty ($user['Service'])){
				$this->Session->write('user.User.service', $user['ServiceElu']['id']);
				}else{
    			$services = $this->Utils->simplifyArray($user['Service']);
    			$this->Session->write('user.Service',$services);
    			$this->Session->write('user.User.service', key($services));
				}
    			//debug($this->Session->read());
				//exit;
				$this->redirect('/');
 			}
			else
			{
				//sinon on prépare le message d'erreur a afficher dans la vue
				$this->set('errorMsg','Mauvais identifiant ou  mot de passe.Veuillez recommencer.');
				$this->layout='connection';
			}
		}
		else
		{
			$this->layout='connection';
		}
	}
	
	function logout()
	{
		//on supprime les infos utilisateur de la session
		$this->Session->delete('user');
		$this->redirect('/users/login');
		
	}
}
?>