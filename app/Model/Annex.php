<?php

class Annex extends AppModel {

    var $name = 'Annex';
    var $displayField = "titre";
    var $belongsTo = array(
        'Deliberation' => array(
            'foreignKey' => 'foreign_key'
        )
    );
    var $validate = array(
        'joindre_ctrl_legalite' => array(
            'rule' => 'checkFileControlLegalite',
            'message' => 'Le format de fichier est invalide pour joindre au contrôle de légalité'),
        'joindre_fusion' => array(
            'rule' => 'checkFileFusion',
            'message' => 'Le format de fichier est invalide pour le joindre à la fusion'),
        'filename' => array(
            'regleFilename-1' => array(
                'rule' => array('maxLength', 100),
                'message' => 'Le nom du fichier est trop long (100 caract&egrave;res maximum)', 'growl'
            ),
            'regleFilename-2' => array(
                'rule' => '/^[a-zA-Z0-9-_.& ]{6,}$/i',
                'message' => 'Seulement les lettres, les entiers et les caractères spéciaux (-_.& ) sont autorisés dans le nom du fichier. Minimum de 6 caractères', 'growl'
            )
        ),
        'titre' => array(
            'rule' => array('maxLength', 200),
            'message' => 'Le titre du fichier est trop long (200 caract&egrave;res maximum)', 'growl')
    );

    function checkFileControlLegalite() {
        //$formats = array('application/pdf', 'image/png', 'image/jpg', 'image/jpeg', 'application/vnd.oasis.opendocument.text', 'application/vnd.oasis.opendocument.spreadsheet');

        if ($this->data['Annex']['joindre_ctrl_legalite'] == 1) {
            $DOC_TYPE = Configure::read('DOC_TYPE');

            if (!empty($this->data['Annex']['filename'])) {
                $file = new File(TMP . time() . '.' . $DOC_TYPE[$this->data["Annex"]['filetype']]['extention'], true);
                $file->append($this->data["Annex"]['data']);
            } else {
                $annex = $this->find('first', array('conditions' => array('Annex.id' => $this->data['Annex']['id']),
                    'recursive' => -1,
                    'fields' => array('Annex.filename', 'Annex.filetype', 'Annex.data')));

                $file = new File(TMP . time() . '.' . $annex["Annex"]['filetype'], true);
                $file->append($annex["Annex"]['data']);
            }

            if (array_key_exists($file->mime(), $DOC_TYPE))
                if (isset($DOC_TYPE[$file->mime()]['joindre_ctrl_legalite']) && $DOC_TYPE[$file->mime()]['joindre_ctrl_legalite'] == true)
                    $return = true;

            $file->delete();
            $file->close();

            return isset($return) && $return == true ? $return : false;
        }

        return true;
    }

    function checkFileFusion() {
        //$formats = array('application/pdf', 'image/png', 'image/jpg', 'image/jpeg', 'application/vnd.oasis.opendocument.text', 'application/vnd.oasis.opendocument.spreadsheet');

        if ($this->data['Annex']['joindre_fusion'] == 1) {
            $DOC_TYPE = Configure::read('DOC_TYPE');

            if (!empty($this->data['Annex']['filename'])) {
                $file = new File(TMP . time() . '.' . $DOC_TYPE[$this->data["Annex"]['filetype']]['extention'], true);
                $file->append($this->data["Annex"]['data']);
            } else {
                $annex = $this->find('first', array('conditions' => array('Annex.id' => $this->data['Annex']['id']),
                    'recursive' => -1,
                    'fields' => array('Annex.filename', 'Annex.filetype', 'Annex.data')));

                $file = new File(TMP . time() . '.' . $annex["Annex"]['filetype'], true);
                $file->append($annex["Annex"]['data']);
            }

            if (array_key_exists($file->mime(), $DOC_TYPE))
                if (isset($DOC_TYPE[$file->mime()]['joindre_fusion']) && $DOC_TYPE[$file->mime()]['joindre_fusion'] == true)
                    $return = true;

            $file->delete();
            $file->close();
            return isset($return) && $return == true ? $return : false;
        }
        return true;
    }

    function getAnnexesFromDelibId($delib_id, $to_send = 0, $to_merge = 0, $joindreParent = false) {
        $conditions['Annex.foreign_key'] = $delib_id;
        //$conditions['Annex.model'] = 'Deliberation';
        if ($to_send == 1)
            $conditions['Annex.joindre_ctrl_legalite'] = true;
        if ($to_merge == 1)
            $conditions['Annex.joindre_fusion'] = true;

        $annexes = $this->find('all', array('conditions' => $conditions,
            'recursive' => -1,
            'order' => array('Annex.id' => 'ASC'),
            'fields' => array('id', 'model')));
        
        if ($joindreParent){
            $delib = $this->Deliberation->find('first', array('conditions' => array('Deliberation.id' => $delib_id),
                'recursive' => -1,
                'fields' => array('id', 'parent_id')));
            if (isset($delib['Deliberation']['parent_id'])) {
                $tab = $this->getAnnexesFromDelibId($delib['Deliberation']['parent_id']);
                if (isset($tab) && !empty($tab)) {
                    for ($i = 0; $i < count($tab); $i++) {
                        if ($tab[$i]['Annex']['model'] == 'Deliberation')
                            unset($tab[$i]);
                    }
                    $annexes = array_merge($annexes, $tab);
                }
            }
        }
        return $annexes;
    }

    function getAnnexesToSend($delib_id) {
        $conditions = array('foreign_key' => $delib_id);
        $conditions['joindre_ctrl_legalite'] = 1;
        return $this->find('all', array(
                    'conditions' => $conditions,
                    'fields' => array('filename', 'filetype', 'data'
        )));
    }

    function getContentToTdT($annex_id) {
        $DOC_TYPE = Configure::read('DOC_TYPE');

        $annex = $this->find('first', array('conditions' => array('Annex.id' => $annex_id),
            'recursive' => -1,
            'fields' => array('filetype', 'data', 'data_pdf','filename','filename_pdf')));

        if ($annex['Annex']['filetype'] === 'application/pdf')
            return array('type' => $DOC_TYPE[$annex['Annex']['filetype']]['extention'],
                'filetype' => 'application/pdf',
                'filename' => $annex['Annex']['filename_pdf'],
                'data' => $annex['Annex']['data_pdf']);

        $pos = strpos($annex['Annex']['filetype'], 'application/vnd.oasis.opendocument');
        if ($pos !== false)
            return array('type' => 'pdf',
                'filetype' => 'application/pdf',
                'filename' => $annex['Annex']['filename_pdf'],
                'data' => $annex['Annex']['data_pdf']
                );
        
        return array('type' => $DOC_TYPE[$annex['Annex']['filetype']]['extention'],
            'filetype' => $annex['Annex']['filetype'],
            'filename' => $annex['Annex']['filename'],
            'data' => $annex['Annex']['data']);
    }

}

?>
