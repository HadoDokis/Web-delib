<?php
class Annex extends AppModel {

	var $name = 'Annex';
	var $displayField="titre";
	
	var $belongsTo = array(
		'Deliberation' => array(
			'foreignKey' => 'foreign_key'
		)
	);  


	function getAnnexesIFromDelibId($delib_id, $to_send = 0, $to_merge = 0) {
	    $conditions = array('Annex.foreign_key' => $delib_id); 
            if ($to_send == 1) 
		$conditions['Annex.joindre_ctrl_legalite'] = 1;
            if ($to_merge == 1) 
		$conditions['Annex.joindre_fusion'] = 1;
              
	    $annexes = $this->find('all', array('conditions' => $conditions,
                                                'recursive'  => -1,
						'fields'     => array('id', 'Model')));

	    $delib = $this->Deliberation->find('first', array('conditions' => array('Deliberation.id' => $delib_id),
                                                              'recursive'  => -1,
                                                              'fields'     => array('id', 'parent_id'))); 

	    if (isset($delib['Deliberation']['parent_id'])) {
		$tab = $this->getAnnexesIFromDelibId( $delib['Deliberation']['parent_id'] );
                
                for($i=0; $i< count($tab); $i ++) 
                    if ($tab[$i]['Annex']['Model'] == 'Deliberation')
		       unset($tab[$i]);

                $annexes = array_merge ($annexes , $tab); 
            }     
            
            return $annexes;
	}

        function getContent($annex_id) {
	    $annex = $this->find('first', array('conditions' => array('Annex.id'     => $annex_id),
                                                'recursive'  => -1,
						'fields'     => array('filetype', 'data', 'data_pdf')));

            $pos = strpos($annex['Annex']['filetype'], 'vnd.oasis.opendocument');
	    if ($pos === false)
		return $annex['Annex']['data'];
            else
                return $annex['Annex']['data_pdf'];
        }

}
?>
