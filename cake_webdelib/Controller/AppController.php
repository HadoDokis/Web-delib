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
    public $components = array('Acl', 'Droits', 'Session',
        'History' => array(
                'unAuthorize' =>  array('/components',
                                        '/download',
                                        '/genereToken',
                                        '/files',
                )
            )/*,'DebugKit.Toolbar'*/);
    public $helpers = array('Html', 'Form', 'Session', 'Html2','Bs','BsForm');
    public $aucunDroit = array('Pages:format', 'Pages:service');
    public $previous;
    public $user_id;

    function beforeFilter() {
        // Désactivation du cache du navigateur: (quand on revient en arrière dans l'historique de
        // navigation, la page n'est pas cachée du côté du navigateur, donc il ré-exécute la demande)
        CakeResponse::disableCache();
        
        $this->set('Droits', $this->Droits);
        $this->set('name', $this->name);
        $this->set('Droit', $this->Droits);
        $this->set('agentServices', $this->Session->read('user.Service'));
        $this->set('lienDeconnexion', true);
        $this->set('session_service_id', $this->Session->read('user.User.service'));
        $this->set('session_menuPrincipal', $this->Session->read('menuPrincipal'));

        //Si utilisateur connecté
        if ($this->Session->check('user')) {
            $this->set('infoUser', $this->Session->read('user.User.prenom') . ' ' . $this->Session->read('user.User.nom'));
            $this->set('Collectivite', array('nom'=> $this->Session->read('user.collective.nom')));
            $this->user_id = $this->Session->read('user.User.id');
            $this->set('user_id', $this->user_id);
            
            if ($this->Session->check('user.User.theme')) {
                $this->theme = $this->Session->read('user.User.theme');
            }
        }  
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

    /**
     *    Send the headers necessary to ensure the page is
     *    reloaded on every request. Otherwise you could be
     *    scratching your head over out of date test data.
     * @access public
     * @static
     */
    public function sendNoCacheHeaders() {
        if (!headers_sent()) {
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
        }
    }
}
