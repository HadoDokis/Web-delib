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
    public $components = array('Utils', 'Acl', 'Droits', 'Session');
    public $helpers = array('Html', 'Form', 'Session', 'DatePicker', 'Html2');
    public $aucunDroit = array('Pages:format', 'Pages:service');
    public $previous;
    public $user_id;

    function beforeFilter() {
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
            $historique = $this->Session->check('user.history') ? $this->Session->read('user.history') : array();
            if (empty($this->params['requested'])
                && stripos($this->params->here, 'ajax') === false // méthode ajax
                && stripos($this->params->here, 'download') === false // téléchargement de fichier
                && stripos($this->params->here, 'genere') === false // méthode de génération
                && stripos($this->params->here, 'files/') === false // liens vers fichiers
                && stripos($this->params->here, 'sendToTdt') === false // pas de vue associée
                && stripos($this->params->here, 'deliberations/getBordereauTdt') === false // pas de vue associée
                && stripos($this->params->here, 'deliberations/getTampon') === false // pas de vue associée
                && stripos($this->params->here, 'deliberations/classification') === false // popup
            ) {
                //Ajoute l'url courante au début du tableau
                if (empty($historique) || $historique[0] != $this->params->here) {
                    //Insère l'url courant en début de tableau (indice 0)
                    array_unshift($historique, $this->params->here);
                }

                if (count($historique) > 2 && $historique[0] == $historique[2]) {
                    array_shift($historique);
                    array_shift($historique);
                }

                //Si ne garder que 6 éléments dans l'historique
                if (count($historique) > 6)
                    array_pop($historique);
            }
            $this->Session->write('user.history', $historique);
            if (count($historique) > 1) {
                $this->previous = $historique[1];
                $this->Session->write('previous_url', $this->previous);
                $this->set('previous', $this->previous);
            }elseif (count($historique) ==1)  {
                $this->previous = '/';
                $this->Session->write('previous_url', $this->previous);
                $this->set('previous', $this->previous);
            }
            if ($this->Session->check('user.User.theme'))
                $this->theme = $this->Session->read('user.User.theme');
        }
        // ????
        if (CRON_DISPATCHER) return true;
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

    function afterFilter() { //FIXME : lastUrl devrait être enregistré dans beforeFilter ?
        //Navigation (lien de retour)
        if ($this->Session->check('user.User')) {
            $this->Session->write('user.User.myUrl', $this->here);
            // Attention au cas de elements
            if ($this->here != $this->referer()
                && $this->here != '/deliberations/classification'
                && $this->here != '/seances/voter'
                && $this->here != '/deliberations/listerPresences'
            ) {
                $pos = strpos(Router::url(null, true), 'Ajax');
                if ($pos === false) {
                    $this->Session->write('user.User.lasturl', $this->referer());
                }
            }
        }
    }

    function externLogin($login = null, $password = null) {
        $user = $this->User->findByLogin($login);

        //si le mdp n'est pas vide et correspond a celui de la bdd
        if (!empty($password) && ($user['User']['password'] == md5($password))) {
            //on stocke l'utilisateur en session
            $this->Session->write('user', $user);
            //services auquels appartient l'agent
            if (empty ($user['Service'])) {
                $this->Session->write('user.User.service', $user['ServiceElu']['id']);
            } else {
                $services = $this->Utils->simplifyArray($user['Service']);
                foreach ($services as $key => $service) {
                    $service = $this->requestAction("services/doList/$key");
                    $services[$key] = $service;
                }
                $this->Session->write('user.Service', $services);
                $this->Session->write('user.User.service', key($services));
            }
            $this->redirect('/');
        }
    }

    function beforeRender() {
        if ($this->Session->check('user.User')) {
            $pos = strpos(Router::url(null, true), 'Ajax');
            if ($pos === false) {
                $this->Session->write('user.User.oldurl', Router::url(null, true));
            }
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
