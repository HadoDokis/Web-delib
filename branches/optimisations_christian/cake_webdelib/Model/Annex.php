<?php
class Annex extends AppModel {

	var $name = 'Annex';

	var $displayField="titre";

        var $validate = array('joindre_ctrl_legalite' => array(
                                                                'rule' => 'checkFileControlLegalite',
                                                                'message' => 'Le format de fichier est invalide pour joindre au contrôle de légalité'),
                              'joindre_fusion' => array(
                                                        'rule' => 'checkFileFusion',
                                                        'message' => 'Le format de fichier est invalide pour le joindre à la fusion'),
                              'filename' => array(
                                                    'rule' => array('maxLength', 70),
                                                    'message' => 'Le nom de fichier trop long (70 caract&eageave;res maximum)','growl'));

	var $belongsTo = array(
		'Deliberation' => array(
			'foreignKey' => 'foreign_key'
		)
	);

        function checkFileControlLegalite() {
            //$formats = array('application/pdf', 'image/png', 'image/jpg', 'image/jpeg', 'application/vnd.oasis.opendocument.text', 'application/vnd.oasis.opendocument.spreadsheet');

            if ($this->data['Annex']['joindre_ctrl_legalite'] == 1)
            {
                $DOC_TYPE = Configure::read('DOC_TYPE');

                if (!empty($this->data['Annex']['filename'])) {
                    $file = new File(TMP.time().'.'.$DOC_TYPE[$this->data["Annex"]['filetype']]['extention'] , true);
                   $file->append($this->data["Annex"]['data']);
                }
                else {
                    $annex = $this->find('first', array('conditions' => array('Annex.id' => $this->data['Annex']['id']),
                                                        'recursive'  => -1,
                                                        'fields'     => array('Annex.filename', 'Annex.filetype', 'Annex.data')));

                    $file = new File(TMP.time().'.'.$annex["Annex"]['filetype'] , true);
                    $file->append($annex["Annex"]['data']);

                }

                if(array_key_exists($file->mime(), $DOC_TYPE))
                      if (isset($DOC_TYPE[$file->mime()]['joindre_ctrl_legalite'])
                              && $DOC_TYPE[$file->mime()]['joindre_ctrl_legalite']==true)
                        $return=true;

                $file->delete();
                $file->close();

                return isset($return) && $return==true?$return:false;
            }

            return true;
        }

        function checkFileFusion() {


            //$formats = array('application/pdf', 'image/png', 'image/jpg', 'image/jpeg', 'application/vnd.oasis.opendocument.text', 'application/vnd.oasis.opendocument.spreadsheet');

            if ($this->data['Annex']['joindre_fusion'] == 1)
            {
                $DOC_TYPE = Configure::read('DOC_TYPE');

                if (!empty($this->data['Annex']['filename'])) {
                    $file = new File(TMP.time().'.'.$DOC_TYPE[$this->data["Annex"]['filetype']]['extention'] , true);
                   $file->append($this->data["Annex"]['data']);
                }
                else {
                    $annex = $this->find('first', array('conditions' => array('Annex.id' => $this->data['Annex']['id']),
                                                        'recursive'  => -1,
                                                        'fields'     => array('Annex.filename', 'Annex.filetype', 'Annex.data')));

                    $file = new File(TMP.time().'.'.$annex["Annex"]['filetype'] , true);
                    $file->append($annex["Annex"]['data']);

                }

                if(array_key_exists($file->mime(), $DOC_TYPE))
                      if (isset($DOC_TYPE[$file->mime()]['joindre_fusion'])
                              && $DOC_TYPE[$file->mime()]['joindre_fusion']==true)
                        $return=true;

                $file->delete();
                $file->close();

                return isset($return) && $return==true?$return:false;
            }

            return true;
        }

	function getAnnexesFromDelibId($delib_id, $to_send = 0, $to_merge = 0) {
            $conditions['Annex.foreign_key'] = $delib_id;
            //$conditions['Annex.model'] = 'Deliberation';
            if ($to_send == 1)
		$conditions['Annex.joindre_ctrl_legalite'] = true;
            if ($to_merge == 1)
		$conditions['Annex.joindre_fusion'] = true;

	    $annexes = $this->find('all', array('conditions' => $conditions,
                                                'recursive'  => -1,
                                                'order'      => array('Annex.id' => 'ASC'),
						'fields'     => array('id', 'model')));
	    $delib = $this->Deliberation->find('first', array('conditions' => array('Deliberation.id' => $delib_id),
                                                              'recursive'  => -1,
                                                              'fields'     => array('id', 'parent_id')));

	    if (isset($delib['Deliberation']['parent_id'])) {
		$tab = $this->getAnnexesFromDelibId( $delib['Deliberation']['parent_id'] );
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

        function getAnnexesToSend($delib_id) {
            $conditions = array('foreign_key' => $delib_id);
            $conditions['joindre_ctrl_legalite'] = 1;
	    return ($this->find('all', array('conditions' => $conditions,
                                             'fields' => array('filename', 'filetype', 'data'))));
	}

        function getContentToTdT($annex_id){
            $DOC_TYPE = Configure::read('DOC_TYPE');

	    $annex = $this->find('first', array('conditions' => array('Annex.id'     => $annex_id),
                                                'recursive'  => -1,
						'fields'     => array('filetype', 'data', 'data_pdf')));

            if ($annex['Annex']['filetype'] === 'application/pdf')
                return array( 'type'=>$DOC_TYPE[$annex['Annex']['filetype']]['extention'],
                                'data'=>$annex['Annex']['data_pdf']);

            $pos = strpos($annex['Annex']['filetype'], 'application/vnd.oasis.opendocument');
            if ($pos !== false)
                return array( 'type'=>'pdf',
                                'data'=>$annex['Annex']['data_pdf']);

            return array( 'type'=>$DOC_TYPE[$annex['Annex']['filetype']]['extention'],
                                'data'=>$annex['Annex']['data']);
        }

        // ---------------------------------------------------------------------

        /**
         *
         * @todo Annex.id = 120 -> getAnnexesFromDelibId2( 276, 0, 1 )
         *
         * @param integer $delib_id
         * @param integer $to_send
         * @param integer $to_merge
         * @return array
         */
        public function getAnnexesFromDelibId2( $delib_id, $to_send = 0, $to_merge = 0 ) {
            $deliberations_ids = Hash::extract( $this->Deliberation->postgresFindParents( $delib_id, array( 'id', 'parent_id' ) ), '{n}.Deliberation.id' );

            $querydata = array(
                'fields' => array(
                    'Annex.id',
                    'Annex.titre',
                    'Annex.filename',
                    'Annex.model',
                    'Annex.data',
                    'Annex.data_pdf',
                    'Annex.filetype',
                ),
                'conditions' => array(
                    'Annex.foreign_key' => $deliberations_ids,
                    'Annex.model' => array( 'Projet', 'Deliberation' ),
                ),
                'recursive' => -1,
                'order' => array( 'Annex.id' => 'ASC' ),
            );

            if ($to_send == 1) {
                $querydata['conditions']['Annex.joindre_ctrl_legalite'] = true;
            }

            if ($to_merge == 1) {
                $querydata['conditions']['Annex.joindre_fusion'] = true;
            }

            return $this->find( 'all', $querydata );
        }

}
?>
