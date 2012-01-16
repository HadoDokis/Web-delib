<?php
    App::import(array('Model', 'AppModel', 'File'));
    App::import(array('Model', 'Deliberation', 'File'));

    class PastellShell extends Shell{
	
        var $uses = array('Deliberation', 'Collectivite');

        function startup() {
        }

	function main() {
            $collectivite = $this->Collectivite->find('first', array('conditions'=> array('id'=>1)));
            $id_e =  $collectivite['Collectivite']['id_entity'];
            // Controle de l'avancement des délibérations dans le parapheur
            $delibs = $this->Deliberation->find('all',
						array('conditions' => array('Deliberation.pastell_id !=' => null )));
	    if (!empty($delibs)){
                 App::import('Component','Pastell');
                 $this->Pastell = new PastellComponent();

//		foreach ($delibs as $delib) {
                    $delib_id = $delib['Deliberation']['id'];
		    //$id_d     = $delib['Deliberation']['pastell_id'];
                    $id_d     = "NOhEp91";
		    echo ("$delib_id -> $id_d\n");
		    $infos = $this->Pastell->getInfosDocument($id_e, $id_d);
                    debug($infos);
//              }
            }
        }
        

        
    }
?>
