<?php
/**
 * CakePHP ApplistComponent
 * @author Florian Ajir
 */
class ApplistComponent extends Component {

    public $components = array();
    
    public function get($type) {
        return App::objects($type);
    }
    
    public function getPluginControllers($plugin) {
        return App::objects('Controller', App::pluginPath($plugin).'Controller'. DS, false);
    }
    
    public function getControllerMethods($controller, $plugin=null) {
        // Load the controller
        if ($plugin == null)
            App::import('Controller', str_replace('Controller', '', $controller));
        else
            App::import('Controller', $plugin.'.'.str_replace('Controller', '', $controller));
        
        // Load its methods / actions
        $aMethods = get_class_methods($controller);

        foreach ($aMethods as $idx => $method) {

            if ($method{0} == '_') {
                unset($aMethods[$idx]);
            }
        }
        // Load the ApplicationController (if there is one)
        App::import('Controller', 'AppController');
        $parentActions = get_class_methods('AppController');

        $methods = array_diff($aMethods, $parentActions);

        return $methods;
    }
    
    public function construireArbre(){
        $plugin_ctrl_method = array();
        foreach ($this->get('plugins') as $plugin){
            foreach ($this->getPluginControllers($plugin) as $ctrl){
                $plugin_ctrl_method[$plugin][$ctrl] = $this->getControllerMethods($ctrl, $plugin);
            }
        }
        foreach ($this->get('controller') as $ctrl){
            $plugin_ctrl_method[""][$ctrl] = $this->getControllerMethods($ctrl);
        }
        return $plugin_ctrl_method;
    }

}