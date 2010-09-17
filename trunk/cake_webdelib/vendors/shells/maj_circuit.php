<?php
    App::import(array('Model', 'AppModel', 'File'));

    class MajCircuitShell extends Shell{
	
        var $uses = array('Cakeflow.Circuit', 
                          'Cakeflow.Etape', 
                          'Cakeflow.Composition');
		
        function main() {
            $circuits = $this->Circuit->query("SELECT * 
                                               FROM circuits");
            foreach ($circuits as $circuit){
                 $this->Circuit->create();
                 $new['Circuit']['nom'] = $circuit['circuits']['libelle'];
                 $new['Circuit']['actif'] = 1;
                 $this->Circuit->save($new);
                 $circuit_id =  $this->Circuit->getLastInsertID();
 
                 $users = $this->Circuit->query("SELECT * 
                                                 FROM users_circuits 
                                                 WHERE circuit_id = ". $circuit['circuits']['id']." 
                                                 ORDER BY position");
                 foreach ($users as $user){
                     $this->Etape->create();
                     $etape['Etape']['circuit_id'] = $circuit_id;
                     $etape['Etape']['nom'] = 'ETAPE_'.$user['users_circuits']['id'] ;
                     $etape['Etape']['ordre'] = $user['users_circuits']['position'];
                     $etape['Etape']['type'] = 1;
                     $this->Etape->save($etape);
                     
                     $etape_id = $this->Etape->getLastInsertID();
                     $this->Composition->create(); 
                     $compositon['Composition']['etape_id'] =  $etape_id ;
                     $compositon['Composition']['user_id']  = $user['users_circuits']['user_id'];
                     $compositon['Composition']['type_validation']  = 'V';
                     $this->Composition->save($compositon); 
                 } 
            }
            $this->Circuit->query("DROP circuits");
            $this->Circuit->query("DROP users_circuits");
    
        }
    }

?>
