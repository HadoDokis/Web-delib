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

    public function getPluginModels($plugin) {
        return App::objects('Model', App::pluginPath($plugin).'Model'. DS, false);
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

    public function getModelMethods($model, $plugin=null) {
        // Load the controller
        if ($plugin == null)
            App::import('Model', $model);
        else
            App::import('Model', $plugin.'.'.$model);

        // Load its methods / actions
        $aMethods = get_class_methods($model);

        foreach ($aMethods as $idx => $method) {
            if ($method{0} == '_') {
                unset($aMethods[$idx]);
            }
        }
        // Load the ApplicationController (if there is one)
        App::import('Model', 'AppModel');
        $parentActions = get_class_methods('AppModel');

        $methods = array_diff($aMethods, $parentActions);

        return $methods;
    }

    public function construireArbreController(){
        $plugin_ctrl_method = array();
        foreach ($this->get('plugins') as $plugin){
            foreach ($this->getPluginControllers($plugin) as $ctrl){
                $plugin_ctrl_method[$plugin][$ctrl] = $this->getControllerMethods($ctrl, $plugin);
            }
        }
        foreach ($this->get('controller') as $ctrl){
            $plugin_ctrl_method[''][$ctrl] = $this->getControllerMethods($ctrl);
        }
        return $plugin_ctrl_method;
    }

    public function construireArbreModel(){
        $plugin_ctrl_method = array();
        foreach ($this->get('plugins') as $plugin){
            foreach ($this->getPluginModels($plugin) as $model){
                $plugin_ctrl_method[$plugin][$model] = $this->getModelMethods($model, $plugin);
            }
        }
        foreach ($this->get('model') as $model){
            $plugin_ctrl_method[''][$model] = $this->getModelMethods($model);
        }
        return $plugin_ctrl_method;
    }

}