<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * CakePHP HistoryComponent
 * @author Sébastien PLAZA
 */
class HistoryComponent extends Component {

    /**
    * Other components utilized by AuthComponent
    *
    * @var array
    */
    public $components = array('Session', 'RequestHandler');
    
    /**
    * The session key name where the record of the current user is stored. Default
    * key is "Auth.User". If you are using only stateless authenticators set this
    * to false to ensure session is not started.
    *
    * @var string
    */
    public static $sessionKey = 'User.History';

    /**
    * Link for which user History is not required.
    *
    * @var array
    * @see HistoryComponent::deny()
    */
    var $unAuthorize = array();
    
    /**
    * Controller actions for which user History is not required.
    *
    * @var array
    * @see HistoryComponent::deny()
    */
    public $unAllowedActions = array();
        

    /**
     * A URL (defined as a string or array) to the controller action that handles
     * logins. Defaults to `/users/login`.
     *
     * @var mixed
     */
    public $loginAction = array(
        'controller' => 'users',
        'action' => 'login',
        'plugin' => null
    );
    
    /**
    * Request object
    *
    * @var CakeRequest
    */
    public $request;

    /**
     * Response object
     *
     * @var CakeResponse
     */
    public $response;

    /**
     * Method list for bound controller.
     *
     * @var array
     */
    protected $_methods = array();

    /**
     * Initializes AuthComponent for use in the controller.
     *
     * @param Controller $controller A reference to the instantiating controller object
     * @return void
     */

    function initialize(Controller $controller) {

        $this->request = $controller->request;
        $this->response = $controller->response;
        $this->_methods = $controller->methods;
    }

    /**
     * Called automatically after controller beforeFilter
     * @param Controller $controller A reference to the controller object
     */
    function startup(Controller $controller) {

        if($this->Session->check(self::$sessionKey))
        {
            $session = $this->Session->read(self::$sessionKey);
        }
        $list = !empty($session['data'])?$session['data']:array();
        $controller->here = !empty($session['here'])?$session['here']:array();
        $controller->previous = !empty($session['previous'])?$session['previous']:array();
        
        if (!$this->_unauthorized($controller) && !empty($controller->request->params)) {
            
            $route=$this->_purgeRequest($controller->request);
            //Ajoute l'url courante au début du tableau
            if (empty($list) || $list[0] != $route) {
                //Insère la route courante en début de tableau (indice 0)
                array_unshift($list, $route);
            }
            
            if (count($list) > 2 && $list[0] == $list[2]) {
                array_shift($list);
                array_shift($list);
            }
             
            //Ne garder que 6 éléments dans l'historique
            if (count($list) > 6) {
                array_pop($list);
            }
            
            $controller->here = $list[0];
            $controller->previous = count($list) == 1 ? $list[0] : $list[1];
            
            $this->Session->write(self::$sessionKey, array(
                                                            'data' => $list,
                                                            'here' => $controller->here,
                                                            'previous' => $controller->previous
                                                            ));
        }
/*
        if ($this->RequestHandler->isSSL()) {
                $base='https://';
        }
        else
        {
            $base='http://';
        }
        $base.=$controller->request->host().'/';
        
  */      
        
        if(!empty($controller->here)){
            $controller->set('here', $controller->here);
        }
        
        if(!empty($controller->previous)){
            $controller->set('previous', $controller->previous);
        }
        
        
    }

    function reset() {
        if ($this->Session->check(self::$sessionKey)) {
            $this->Session->delete(self::$sessionKey);
        }
    }

    /**
     * Handle unauthorized access attempt
     *
     * @param Controller $controller A reference to the controller object
     * @return boolean Returns false
     */
    protected function _unauthorized(Controller $controller) {
        if (!empty($controller->request->params['requested'])) {
            return true;
        }

        if ($this->RequestHandler->isAjax()) {
            return true;
        }
        
        array_unshift($this->unAuthorize, Router::normalize($this->loginAction));
        foreach ($this->unAuthorize as $value) {
            if (stripos(Router::normalize($controller->request->url), Router::url($value))!==false) {
                return true;
            }
        }

        return $this->_isNotAllowed($controller);
    }
    
    /**
    * Takes a list of actions in the current controller for which History is not required, or
    * no parameters to allow all actions.
    *
    * You can use allow with either an array, or var args.
    *
    * `$this->History->deny(array('edit', 'add'));` or
    * `$this->History->deny('edit', 'add');` or
    * `$this->History->deny();` to allow all actions
    *
    * @param string|array $action Controller action name or array of actions
    * @return void
    */
    public function deny($action = null) {
        $args = func_get_args();
        if (empty($args) || $action === null) {
                $this->unAllowedActions = $this->_methods;
                return;
        }
        if (isset($args[0]) && is_array($args[0])) {
                $args = $args[0];
        }
        $this->unAllowedActions = array_merge($this->unAllowedActions, $args);
    }
    
    /**
    * Checks whether current action is accessible without authentication.
    *
    * @param Controller $controller A reference to the instantiating controller object
    * @return boolean True if action is accessible without authentication else false
    */
    protected function _isNotAllowed(Controller $controller) {
        $action = strtolower($controller->request->params['action']);
        if (in_array($action, array_map('strtolower', $this->unAllowedActions))) {
                return true;
        }
        return false;
    }
    /*
    function back($index = -1, $return = false) {
        $index = abs($index);
    }*/
    
    /* Purge du Request pour l'histoique des liens
     * @param Controller $controller A reference to the instantiating controller object
     * @return array tableau de paramêtre de l'action
     */
    function _purgeRequest(CakeRequest $params)
    {
        if (!($params instanceof CakeRequest)) {
            throw Exception(__('this request is not instance of CakeRequest'), 500);
        }
        $params = $params->params;
        
        $pass = isset($params['pass']) ? $params['pass'] : array();
        $named = isset($params['named']) ? $params['named'] : array();

        unset(
            $params['pass'], $params['named'], $params['paging'], $params['models'], $params['url'],
            $params['autoRender'], $params['bare'], $params['requested'], $params['return'],
            $params['_Token']
        );
        
        return array_merge($params, $pass, $named);
    }
}
