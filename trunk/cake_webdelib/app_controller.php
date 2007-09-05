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
	var $components=array('Utils');
	var $beforeFilter = array('checkSession');
	var $helpers = array('Html', 'Form' , 'Javascript');
	

	var $infoUser = "";
	var $lienAccueil = "";
	var $lienDeconnexion = "";
	var $agentServices = null;
	
	function checkSession()
	{
		$this->infoUser = "<span class=\"user\">".$this->Session->read('user.User.prenom')." ".$this->Session->read('user.User.nom')."</span> ";
   		$this->agentServices = $this->Session->read('user.Service');
		$this->lienAccueil = " | <span class=\"accueil\"><a href=\"".$this->base."/\">Accueil</a></span> ";
 	    $this->lienDeconnexion = " | <span class=\"deconnexion\"><a href=\"".$this->base."/users/logout\"> Deconnexion</a></span>";  
 	    	  
		if(substr($_SERVER['REQUEST_URI'], strlen($this->base)) != '/users/login')
		{
			//s'il n'y a pas d'utilisateur connecté en session
			if (!$this->Session->Check('user'))
			{
				//le forcer a se connecter
				$this->redirect('users/login');
				exit();
			}
		}
		
	}
	
}
?>