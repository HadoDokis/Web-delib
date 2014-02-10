<?php

/**
 * Code source de la classe Annex.
 *
 * PHP 5.3
 *
 * @package app.Model.Annex
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/**
 * Classe Annex.
 *
 * @package app.Model.Annex
 * 
 */

class Annex extends AppModel {

    public $name = 'Annex';
    public $displayField = 'titre';
    public $belongsTo = array(
        'Deliberation' => array(
            'foreignKey' => 'foreign_key'
        )
    );
    public $validate = array(
        'joindre_ctrl_legalite' => array(
            'rule' => 'checkFormatControlLegalite',
            'message' => 'Le format de fichier est invalide pour joindre au contrôle de légalité'),
        'joindre_fusion' => array(
            'rule' => 'checkFormatFusion',
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

    public function checkFormatControlLegalite() {
        if ($this->data['Annex']['joindre_ctrl_legalite'] == 1) {
            $DOC_TYPE = Configure::read('DOC_TYPE');
            if (!empty($this->data['Annex']['filename'])) {
                $mime = $this->data['Annex']['filetype'];
            } else {
                $annex = $this->find('first', array(
                    'conditions' => array('Annex.id' => $this->data['Annex']['id']),
                    'recursive' => -1,
                    'fields' => array('Annex.filetype')));
                $mime = $annex['Annex']['filetype'];
            }
            return !empty($DOC_TYPE[$mime]['joindre_ctrl_legalite']);
        }
        return true;
    }

    public function checkFormatFusion() {
        if ($this->data['Annex']['joindre_fusion'] == 1) {
            $DOC_TYPE = Configure::read('DOC_TYPE');
            if (!empty($this->data['Annex']['filename'])) {
                $mime = $this->data['Annex']['filetype'];
            } else {
                $annex = $this->find('first', array('conditions' => array('Annex.id' => $this->data['Annex']['id']),
                    'recursive' => -1,
                    'fields' => array('Annex.filetype')));
                $mime = $annex['Annex']['filetype'];
            }
            return !empty($DOC_TYPE[$mime]['joindre_ctrl_legalite']);
        }
        return true;
    }

    /**
     * Cherche les annexes d'une Deliberation
     *
     * @param string $delib_id identifiant de la Deliberation
     * @param bool $to_send ne chercher que les annexes avec la propriété joindre_ctrl_legalite
     * @param bool $to_merge ne chercher que les annexes avec la propriété joindre_fusion
     * @param bool $joindreParent inclure les Annexe de la Deliberation parente
     * @return array
     */
    public function getAnnexesFromDelibId($delib_id, $to_send = false, $to_merge = false, $joindreParent = false) {
        $conditions['Annex.foreign_key'] = $delib_id;
        //$conditions['Annex.model'] = 'Deliberation';
        if ($to_send)
            $conditions['Annex.joindre_ctrl_legalite'] = true;
        if ($to_merge)
            $conditions['Annex.joindre_fusion'] = true;

        $annexes = $this->find('all', array('conditions' => $conditions,
            'recursive' => -1,
            'order' => array('Annex.id' => 'ASC'),
        ));
        
        if ($joindreParent){
            $this->Deliberation->id = $delib_id;
            $parent_id = $this->Deliberation->field('parent_id');
            if (!empty($parent_id)) {
                $parent_annexes = $this->getAnnexesFromDelibId($parent_id, $to_send, $to_merge, $joindreParent);
                if (!empty($tab)) {
                    foreach ($parent_annexes as $i => $parent_annexe){
                        if ($parent_annexe['Annex']['model'] == 'Deliberation')
                            unset($parent_annexes[$i]);
                    }
                    $annexes = array_merge($annexes, $tab);
                }
            }
        }
        return $annexes;
    }

    public function getContentToTdT($annex_id)
    {
        $annex = $this->find('first', array(
            'conditions' => array('Annex.id' => $annex_id),
            'recursive' => -1,
            'fields' => array('data_pdf')
        ));

        return array(
            'type' => 'pdf',
            'data' => $annex['Annex']['data_pdf']
        );
    }
}
