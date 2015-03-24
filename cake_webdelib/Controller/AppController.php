<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package        app.Controller
 * @link        http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
    public $theme = "Normal";
    
    public $components = array(
        'Acl', 
        'Auth' => array(
            'loginAction' => array('admin'=> false, 'plugin'=> null, 'controller' => 'users', 'action' => 'login'),
            'logoutRedirect' => array('admin'=> false, 'plugin'=> null,'controller' => 'users', 'action' => 'login'),
            //'unauthorizedRedirect' => array('admin'=> false, 'plugin'=> null,'controller' => 'pages', 'action' => 'display', 'home'),
            'loginRedirect' => array('admin'=> false, 'plugin'=> null,'controller' => 'pages', 'action' => 'display', 'home'),
            'authError' => 'Vous n\'étes autorisés à effectuer cette action !',
            'allowedActions'=>array('Pages','Deliberations'),
            'authenticate' => array(
                //Paramêtre a passer à tous les objets d'authentifications
                'all' => array( 
                        'userModel' => 'User',
                        'scope' => array('User.active' => true),
                ),
                'Form' => array(
                    'passwordHasher' => array(
                        'className' => 'AuthManager.SimpleNotSecuritySalt',
                        'hashType' => 'md5'
                    )
                )                
                    /*'AuthManager.Cas',
                    'AuthManager.Ldap',*/
            ),
            'authorize' => array('Controller'),
        ),
        'Session',
        'History' => array(
                'unAuthorize' =>  array('components/',
                                        'download/',
                                        'genereToken/',
                                        'files/',
                )
            ),
        'DebugKit.Toolbar'
        );
    
    public $helpers = array(
        'Html', 
        'BForm' => array(
            'className' => 'Bootstrap3.BootstrapForm'), 
        'Session', 
        'Html2',
        'Bs','BsForm',
        'AuthManager.Permissions',
        'Navbar' => array(
            'className' => 'Bootstrap3.BootstrapNavbar')
        );
    
    function beforeFilter() {
        
        //$this->Auth->allow(); //Ne pas mettre
        //Pas d'autentification pour les requesteds
        if (isset($this->params['requested'])) $this->Auth->allow($this->action);
        
        //initialisation des mapActions pour les droits CRUD
        if (isset($this->components['Auth']['mapActions'])) {
            //Mise en place des actions publiques
            if(isset($this->components['Auth']['mapActions']['allow'])){
                $this->Auth->allow($this->components['Auth']['mapActions']['allow']);
                unset($this->components['Auth']['mapActions']['allow']);
            }
            
            $this->Auth->mapActions($this->components['Auth']['mapActions']);
        }
        
        // Désactivation du cache du navigateur: (quand on revient en arrière dans l'historique de
        // navigation, la page n'est pas cachée du côté du navigateur, donc il ré-exécute la demande)
        //CakeResponse::disableCache();
        
        // passage de paramètre en utilisant 'all'
        $this->log("{$this->Auth->user('id')} => ".Router::normalize($this->params['requested']), 'trace');
        return;
        $this->set('name', $this->name);
        
        //if (CRON_DISPATCHER) return true;
        // Exception pour le bon déroulement de cron
        $action_accepted = array('runCrons', 'majTraitementsParapheur', 'traiterDelegationsPassees', 'generer');
        if (in_array($this->action, $action_accepted)) return true;
    }

    function afterFilter() {
    }

    function beforeRender() {
        //Si utilisateur connecté
        if ($this->Session->check('User')) {
            $this->set('infoUser', $this->Auth->user('prenom') . ' ' . $this->Auth->user('nom'));
            $this->set('infoServiceEmeteur', $this->Auth->user('ServiceEmetteur.name'));
            $this->set('infoCollectivite', array('nom'=> $this->Session->read('Collective.nom')));
            /*
            if ($this->Session->check('User.theme')) {
                $this->theme = $this->Session->read('User.theme');
            }*/
        } 
    }

    function _selectedArray($data, $key = 'id') {
        if (!is_array($data)) {
            $model = $data;
            if (!empty($this->data[$model][$model])) {
                return $this->data[$model][$model];
            }
            if (!empty($this->data[$model])) {
                $data = $this->data[$model];
            }
        }
        $array = array();
        if (!empty($data)) {
            foreach ($data as $var) {
                $array[$var[$key]] = $var[$key];
            }
        }
        return $array;
    }

    public function isAuthorized($user = null) {
        
        App::uses('Component', 'Controller');
        App::uses('ComponentCollection', 'Controller');
        App::uses('CrudAuthorize', 'AuthManager.Controller/Component/Auth');
        
        $collection = new ComponentCollection();
        $CrudAuthorize = new CrudAuthorize($collection);
        
        //initialisation des mapActions pour les droits CRUD
        if (isset($this->components['Auth']['mapActions'])) {
            $CrudAuthorize->mapActions($this->components['Auth']['mapActions']);
        }
        // Seulement les administrateurs peuvent accéder aux fonctions d'administration
        if (isset($this->request->params['manager']) && $user['Profil']['role_id'] !== 3) {
            return false;
        }
        
        // Seulement les administrateurs peuvent accéder aux fonctions d'administration
        if (isset($this->request->params['admin']) && $user['Profil']['role_id'] !== 2) {
            return false;
        }
        
        // Par défaut n'autorise pas
        return $CrudAuthorize->authorize(array('id' => $this->Auth->user('id')), $this->request);
    }
}
