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
    public $theme = "Webdelib";
    
    public $components = array(
        'Acl', 
        'Auth' => array(
            'loginAction' => array('controller' => 'users', 'action' => 'login', 'admin'=>false),
            'logoutRedirect' => array('controller' => 'users', 'action' => 'login'),
            'loginRedirect' => array('controller' => 'pages', 'action' => 'display', 'home'),
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
            'authorize'=> array('Actions' => array('actionPath' => 'controllers/'),
                                'Controller')
        ),
        /*'Droits',*/ 
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
    public $aucunDroit = array('Pages:format', 'Pages:service');
    public $previous;
    public $user_id;

    function beforeFilter() {
        
        //$this->Auth->allow(); //Ne pas mettre
        
        // Désactivation du cache du navigateur: (quand on revient en arrière dans l'historique de
        // navigation, la page n'est pas cachée du côté du navigateur, donc il ré-exécute la demande)
        //CakeResponse::disableCache();
        
        return;
        
        $this->set('Droits', $this->Droits);
        $this->set('name', $this->name);
        $this->set('Droit', $this->Droits);
        
        $this->set('agentServices', $this->Session->read('user.Service'));
        $this->set('lienDeconnexion', true);
        $this->set('session_service_id', $this->Session->read('user.User.service'));
        $this->set('session_menuPrincipal', $this->Session->read('menuPrincipal'));

        
        // ????
        //if (CRON_DISPATCHER) return true;
        // Exception pour le bon déroulement de cron
        $action_accepted = array('runCrons', 'majTraitementsParapheur', 'traiterDelegationsPassees', 'generer');

        if (in_array($this->action, $action_accepted)) return true;

        if (substr($_SERVER['REQUEST_URI'], strlen($this->base)) != '/users/login'
            && $this->action != 'writeSession'
            && substr(substr($_SERVER['REQUEST_URI'], strlen($this->base)), 0, strlen('/cakeflow/traitements/traiter_mail')) != '/cakeflow/traitements/traiter_mail'
        ) {
            //si il n'y a pas d'utilisateur connecte en session
            if (!$this->Session->Check('user')) {
                return $this->redirect(array('controller' => 'users', 'action' => 'login', 'plugin' => ''));
            } else {
                // Contrôle des droits
                $Pages = array('Pages:format', 'Pages:service');
                $controllerAction = $this->name . ':' . ($this->name == 'Pages' ? $this->params['pass'][0] : $this->action);
                if (in_array($controllerAction, $Pages)) {
                    return true;
                } elseif ($controllerAction != 'Deliberations:delete') {
                    if (!$this->Droits->check($this->user_id, $controllerAction)) {
                        $this->Session->setFlash("Vous n'avez pas les droits nécessaires pour accéder à : $controllerAction", 'growl', array('type' => 'erreur'));
                        return $this->redirect($this->previous);
                    } else
                        $this->log("{$this->user_id} => $controllerAction", 'trace');
                }
            }
        }
    }

    function afterFilter() {
    }

    function beforeRender() {
        //Si utilisateur connecté
        if ($this->Session->check('User')) {
            $this->set('infoUser', $this->Session->read('Auth.User.prenom') . ' ' . $this->Session->read('Auth.User.nom'));
            $this->set('collectivite', array('nom'=> $this->Session->read('Collective.nom')));
            $this->user_id = $this->Session->read('User.id');
            $this->set('user_id', $this->user_id);
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

    public function isAuthorized($user) {
        return true;
        //parent::isAuthorized($user);
                /*
        // Admin peut accéder à toute action
        if (isset($user['profil_id']) && $user['profil_id'] === 1) {
            return true;
        }

        // Refus par défaut pour tous
        return false;*/
    }
}
