<?php
/* SVN FILE: $Id: app_controller.php 4409 2007-02-02 13:20:59Z phpnut $ */
/**
 * Short description for file.
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2007, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2007, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.cake
 * @since			CakePHP(tm) v 0.2.9
 * @version			$Revision: 4409 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2007-02-02 07:20:59 -0600 (Fri, 02 Feb 2007) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * This is a placeholder class.
 * Create the same file in app/app_controller.php
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		cake
 * @subpackage	cake.cake
 */
class AppController extends Controller {

	var $components=array( 'Utils', 'Acl');
	var $helpers = array('Html', 'Ajax', 'Form' , 'Javascript','Navigation');

	var $beforeFilter = array('checkSession');

	var $infoUser = "";
	var $lienDeconnexion = "";
	var $agentServices = null;
	var $userProfil = null;
	var $menu = null;

	function checkSession() {
		$this->infoUser = "<span class=\"user\">".$this->Session->read('user.User.prenom')." ".$this->Session->read('user.User.nom')."</span> ";
   		$this->agentServices = $this->Session->read('user.Service');
 	    $this->lienDeconnexion = "[<span class=\"deconnexion\"><a href=\"".$this->base."/users/logout\"> Deconnexion</a></span>]";

		if(substr($_SERVER['REQUEST_URI'], strlen($this->base)) != '/users/login')
		{
			//s'il n'y a pas d'utilisateur connecte en session
			if (!$this->Session->Check('user')) {
				//le forcer a se connecter
				$this->redirect("/users/login");
				exit();
			}
			else {
				$user_id = $this->Session->read('user.User.id');
       			$aco = $this->name.':'.$this->action;
        		if ($this->Acl->check($user_id, $aco)) {
        			if ($aco != 'Services:doList') {
        		   	    $this->log($_SERVER["REMOTE_ADDR"]." : ($user_id)->".substr($this->here, 0, strlen($this->here)));
        			}
          		 	$this->menu = $this->buildNavigation($user_id);
          		    return;
        		}
                else {
                   if (DEBUG==1)
                       die("accès refusé pour $user_id (".$this->Session->read('user.User.prenom')." ".$this->Session->read('user.User.nom').") à $aco");
                    else
                        $this->redirect('/users/logout');

                  }
            }
		}
	}

	function externLogin($login = null, $password = null) {
		$user = $this->User->findByLogin($login);

		//si le mdp n'est pas vide et correspond a celui de la bdd
		if (!empty($password) && ($user['User']['password'] == md5($password)))
		{
			//on stocke l'utilisateur en session
			$this->Session->write('user',$user);
			//services auquels appartient l'agent
			if(empty ($user['Service'])){
				$this->Session->write('user.User.service', $user['ServiceElu']['id']);
			}else{
    			$services = $this->Utils->simplifyArray($user['Service']);
    			foreach ($services as $key=>$service){
    				$service = $this->requestAction("services/doList/$key");
    				$services[$key]=$service;
    			}
    			$this->Session->write('user.Service',$services);
    			$this->Session->write('user.User.service', key($services));
				}
				$this->redirect('/');
 		}
	}

	function buildNavigation ($user_id){
		// On importe les droits ?

		$menu = array( );
		// construction navigation secondaire projets
        $sub_menu1 = array ();
        $sub_menu2 = array ();
        $sub_menu3 = array ();
        $sub_menu4 = array ();

		if ($this->Acl->check($user_id, "Deliberations:add")){
			$sub_menu1['Nouveau...'] = array('link' => '/deliberations/add');
			$sub_menu1['Mes projets'] = array('link' => '/deliberations/listerMesProjets');
			$sub_menu1['A attribuer'] = array('link' => '/deliberations/listerProjetsNonAttribues');
		}

        if ($this->Acl->check($user_id, "Deliberations:listerProjetsATraiter"))
			$sub_menu1['A traiter'] = array('link' => '/deliberations/listerProjetsATraiter');

        if ($this->Acl->check($user_id, "Deliberations:listerProjetsServicesAssemblees"))
			$sub_menu1['A faire voter'] = array('link' => '/deliberations/listerProjetsServicesAssemblees');


        // construction navigation secondaire seances
        $sub_menu2 = array (
        	'Nouvelle...' => array('link' => '/seances/add'),
        	//'A venir' => array ('link' => '/seances/listerFuturesSeances'),
        	'Traitées' => array('link' => '/seances/listerAnciennesSeances'),
        	'Calendrier' => array('link' => '/seances/afficherCalendrier')
        );

        // construction navigation secondaire post-seance
        $sub_menu3 = array (
        	'Editions' => array('link' => '/postseances/index'),
        	//'Publications' => array('link' => '/seances/listerAnciennesSeances'),
        	'Contrôle de légalité' => array('link' => '/deliberations/transmit'),
        	'Export GED' => array('link' => '/pages/exportged')
        );

        // construction navigation secondaire administration
        $sub_menu4 = array (
        	'Utilisateurs' => array('link' => '/users/index'),
        	'Circuits' => array('link' => '/circuits/index'),
        	//'Profils' => array('link' => '/profils/index'),
	        'Service' => array('link' => '/services/index'),
	        'Thèmes' => array('link' => '/themes/index'),
	        'Types de séance' => array('link' => '/typeseances'),
	        'Collectivité' => array('link' => '/collectivites'),
	        'Génération' => array('link' => '/models/index'),
	        'Localisation' => array('link' => '/localisations/index'),
	        'Compteurs' => array('link' => '/compteurs/index')
        );

		// construction navigation principale
		$menu['Accueil']= array('link' => '/');

		if ($this->Acl->check($user_id, "Deliberations:index")){
			$menu['Projets']= array('link' => '/deliberations/listerMesProjets', 'submenu' => array());
			$menu['Projets']['submenu'] = $sub_menu1;
		}
		if ($this->Acl->check($user_id, "Seances:index")){
			$menu['Seances']= array('link' => '/seances/listerFuturesSeances', 'submenu' => array());
			$menu['Seances']['submenu'] = $sub_menu2;
			$menu['Post-seance']= array('link' => '/pages/postseance', 'submenu' => array());
			$menu['Post-seance']['submenu'] = $sub_menu3;
		}
		if ($this->Acl->check($user_id, "Pages:administration")){
			$menu['Administration']= array('link' => '/pages/administration', 'submenu' => array());
			$menu['Administration']['submenu'] = $sub_menu4;
		}

        return $menu;
	}
}
?>