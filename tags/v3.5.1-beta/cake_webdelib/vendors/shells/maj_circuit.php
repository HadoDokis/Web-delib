<?php
    App::import(array('Model', 'AppModel', 'File'));

    class MajCircuitShell extends Shell{
	
        var $uses = array('Cakeflow.Circuit', 
                          'Cakeflow.Etape', 
                          'Cakeflow.Traitement', 
                          'Cakeflow.Composition', 
                          'Cakeflow.Visa', 
                          'Deliberation');
		
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
                 // on update les traitements
                 $this->updateCircuit($circuit['circuits']['id'],  $circuit_id );
            }

	    $this->out('Creation des circuits de traitement effectuee');
            $this->out('Vous pouvez supprimer les tables \'users_circuits\' et \'circuits\'.');
        }

        function updateCircuit($oldCircuit_id, $newCircuit_id) {
            // on ne traite que les projets de délibs en cours de validation
            $projets = $this->Deliberation->find('all', array('conditions'=>
                                                        array('Deliberation.circuit_id'=> $oldCircuit_id )));
            foreach ($projets as $projet) {
                $this->Deliberation->id = $projet['Deliberation']['id'];
                $projet['Deliberation']['circuit_id'] = $newCircuit_id;
                $this->Deliberation->save($projet);
                if ( $projet['Deliberation']['etat']==1)
                    $this->updateTraitement($oldCircuit_id, $newCircuit_id, $projet['Deliberation']['id']);
            }

        }
           
        function updateTraitement($oldCircuit_id, $circuit_id, $delib_id){
            $this->Circuit->insertDansCircuit($circuit_id, $delib_id);
            $traitements = $this->Circuit->query("SELECT * 
                                                  FROM traitements
                                                  WHERE circuit_id = $oldCircuit_id
                                                  AND   delib_id   = $delib_id
                                                  AND   date_traitement != '0000-00-00 00:00:00'
                                                  ORDER BY traitements.position");
            foreach ($traitements as $traitement) {
                $user = $this->Circuit->query("SELECT * 
                                               FROM users_circuits
                                               WHERE circuit_id = $oldCircuit_id
                                               AND   users_circuits.position   = ". $traitement['traitements']['position']);
                $this->Traitement->execute('OK', $user[0]['users_circuits']['user_id'], $delib_id, array(), $traitement['traitements']['date_traitement']);
            }
        }
        
    }


?>
