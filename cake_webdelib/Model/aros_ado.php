<?php
    class ArosAdo extends AppModel {
       var $name = 'ArosAdo';
       var $useTable = "aros_ados";
       var $belongsTo = array ('Aro', 'Ado');

       function allow($aro_id, $ado_id, $actions = "*", $value = 1 ){
           $droit = $this->find('first', array('conditions' =>array( 'aro_id' => $aro_id, 'ado_id'=>$ado_id)));
           if (empty($droit)){
               $this->create();
               $droit['ArosAdo']['aro_id']  = $aro_id;
               $droit['ArosAdo']['ado_id']  = $ado_id;
           }
           if (is_array($actions)){
               foreach ($actions as &$action){
                   if ($action{0} != '_') {
                       $action = '_' . $action;
                   }
               }
           }
           else{
               if ($actions == '*')
                   $actions = array('_create', '_read', '_update', '_delete');
               else 
                   if ($actions{0} != '_') 
                       $actions = array('_'.$actions);
           }
    
            foreach($actions as $action)
                $droit['ArosAdo'][$action] = $value;

            return ($this->save($droit));
        }
   
       function check($aro_id, $ado_id) {
            $droit = $this->find('first', array(
                                 'conditions' =>array( 'aro_id' => $aro_id, 
                                                       'ado_id' => $ado_id, 
                                                       '_read' => 1)));
            return (!empty($droit));
       }
     
       function deny($aro, $aco, $action = "*") {
                return $this->allow($aro, $aco, $action, -1);
        }
 
    }
?>
