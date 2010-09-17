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
	                 $userInfo = $this->Circuit->query("SELECT nom, prenom, login 
                                                 FROM users
                                                 WHERE id = ". $user['users_circuits']['user_id']);

                     $this->Etape->create();
                     $etape['Etape']['circuit_id'] = $circuit_id;
                     $etape['Etape']['nom'] = $userInfo[0]['users']['prenom'].' '.$userInfo[0]['users']['nom'] ;
                     $etape['Etape']['ordre'] = $user['users_circuits']['position'];
                     $etape['Etape']['type'] = 1;
                     $this->Etape->save($etape);
                     
                     $etape_id = $this->Etape->getLastInsertID();
                     $this->Composition->create(); 
                     $compositon['Composition']['etape_id'] =  $etape_id ;
                     $compositon['Composition']['trigger_id']  = $user['users_circuits']['user_id'];
                     $compositon['Composition']['type_validation']  = 'V';
                     $this->Composition->save($compositon); 
                 } 
            }

			$this->out('Creation des circuits de traitement effectuee');
			$this->out('Vous pouvez supprimer les tables \'users_circuits\' et \'circuits\'.');
//            $this->Circuit->query("DROP TABLE circuits");
//            $this->Circuit->query("DROP TABLE users_circuits");
        }
    }

?>
