<?php
class Annex extends AppModel {

	var $name = 'Annex';
	var $displayField="titre";

        var $validate = array('joindre_ctrl_legalite' => array(
                              'rule' => 'checkFile',
                              'message' => 'Vous devez changer le format de fichier'));
	
	var $belongsTo = array(
		'Deliberation' => array(
			'foreignKey' => 'foreign_key'
		)
	);  
     
        function checkFile() {
            $formats = array('application/pdf', 'image/png', 'image/jpg', 'image/jpeg', 'application/vnd.oasis.opendocument.text', 'application/vnd.oasis.opendocument.spreadsheet');
            if ($this->data['Annex']['joindre_ctrl_legalite'] == 1) {
                $tmpfname = tempnam(TMP, "CHK_");
                if (!empty($this->data['Annex']['filename'])) {
                    file_put_contents($tmpfname, $this->data["Annex"]['data']);
                }
                else {
                    $annex = $this->find('first', array('conditions' => array('Annex.id' => $this->data['Annex']['id']),
                                                        'recursive'  => -1,
                                                        'fields'     => array('Annex.filename', 'Annex.filetype', 'Annex.data')));
                    
                    file_put_contents($tmpfname, $annex["Annex"]['data']);

                }
                $file_exec = Configure::read('FILE_EXEC');
                $cmd =  "LANG=fr_FR.UTF-8; $file_exec $tmpfname";
                $result = shell_exec($cmd);
                $result = trim($result);
                unlink($tmpfname);
                return (in_array($result, $formats))  ;
            }
            return true;
        }

	function getAnnexesIFromDelibId($delib_id, $to_send = 0, $to_merge = 0) {
	    $conditions = array('Annex.foreign_key' => $delib_id); 
            if ($to_send == 1) 
		$conditions['Annex.joindre_ctrl_legalite'] = 1;
            if ($to_merge == 1) 
		$conditions['Annex.joindre_fusion'] = 1;
              
	    $annexes = $this->find('all', array('conditions' => $conditions,
                                                'recursive'  => -1,
                                                'order'      => array('Annex.id' => 'ASC'),
						'fields'     => array('id', 'model')));

	    $delib = $this->Deliberation->find('first', array('conditions' => array('Deliberation.id' => $delib_id),
                                                              'recursive'  => -1,
                                                              'fields'     => array('id', 'parent_id'))); 

	    if (isset($delib['Deliberation']['parent_id'])) {
		$tab = $this->getAnnexesIFromDelibId( $delib['Deliberation']['parent_id'] );
                if (isset($tab) && !empty($tab)) {
                    for($i=0; $i< count($tab); $i ++)  {
                        if ($tab[$i]['Annex']['model'] == 'Deliberation')
		           unset($tab[$i]);
                        }

                    $annexes = array_merge ($annexes , $tab); 
                }
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
