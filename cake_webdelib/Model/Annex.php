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
                'rule' => '/^[a-zA-Z0-9-_.& ]{5,}$/i',
                'message' => 'Seulement les lettres, les entiers et les caractères spéciaux (-_.& ) sont autorisés dans le nom du fichier. Minimum de 5 caractères', 'growl'
            )
        ),
        'titre' => array(
            'rule' => array('maxLength', 200),
            'message' => 'Le titre du fichier est trop long (200 caract&egrave;res maximum)', 'growl')
    );
    
    
        
    /**
     * En cas de suppresion ou de modifcation d'un annexe 
     * on remet l'ordre de chaque position à jour,
     * pour ne pas perturber l'affichage
     * @delibId Integer : Numero de la deliberation
     */
    public function _reorderAnnex($delibId=NULL) {
        if (!empty($delibId)) {
            $annexes = $this->find('all', array(
                'recursive' => -1,
                'fields' => array('id'),
                'conditions' => array('foreign_key' => $delibId),
                'order' => array('Annex.position' => 'ASC')
            ));
            foreach($annexes as $key=>$annexe) {            
                $this->id = $annexe['Annex']['id'];
                $this->saveField('position', ++$key);
            }
        }
    }
    
    public function afterSave($created, $options) {
        if(!empty($options['delibId']))
        $this->__reorderAnnex($options['delibId']);
    }
    
    /**
     * Regarde dans le fichier formats.inc si le document peut être envoyé au controle de légalité
     * @return bool
     */
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

    /**
     * Regarde dans le fichier formats.inc si le document peut être joint à la fusion
     * @return bool
     */
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
            return !empty($DOC_TYPE[$mime]['joindre_fusion']);
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

        $annexes = $this->find('all', array(
            'conditions' => $conditions,
            'order' => array('Annex.id' => 'ASC'),
            'recursive' => -1,
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

    /**
     * Récupère toutes les annexes (associées à 0, 1 ou plusieurs délibs) ayant la propriété joindre_fusion à false
     * @param null|int|array $delib_id
     * @return mixed tableau d'annexes
     */
    public function getAnnexesWithoutFusion($delib_id = null){
        $conditions = array('joindre_fusion' => false);

        if (!empty($delib_id)) $conditions['foreign_key'] = $delib_id;

        $annexes = $this->find('all', array(
            'conditions' => $conditions,
            'fields' => array('id', 'filetype', 'filename', 'data', 'titre'),
            'order' => array('id' => 'ASC'),
            'recursive' => -1
        ));

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

    /**
     * fonction d'initialisation des variables de fusion pour les avis en séance
     * les bibliothèques Gedooo doivent être inclues par avance
     * génère une exception en cas d'erreur
     * @param object_by_ref $oMainPart variable Gedooo de type maintPart du document à fusionner
     * @param object_by_ref $modelOdtInfos objet PhpOdtApi du fichier odt du modèle d'édition
     * @param string $modelName nom du model lié
     * @param integer $foreignKey id du model lié
     */
    function setVariablesFusion(&$aData, &$modelOdtInfos, $modelName, $foreignKey) {
        // liste des variables de fusion utilisées dans le template
        $fields = array();
        if ($modelOdtInfos->hasUserFieldDeclared('titre_annexe'))
            $fields[] = 'titre';
        if ($modelOdtInfos->hasUserFieldDeclared('nom_fichier'))
            $fields[] = 'filename';
        if ($modelOdtInfos->hasUserField('fichier')) {
            if (!in_array('filename', $fields)) $fields[] = 'filename';
            $fields[] = 'edition_data';
            //$fields[] = 'edition_data_typemime';
        }
        if ($modelOdtInfos->hasUserFieldDeclared('nombre_annexe'))
            $fields[] = 'id';
        
        if (empty($fields)) return;

        // lecture en base de données
        $annexes = $this->find('all', array(
            'recursive' => -1,
            'fields' => $fields,
            'conditions' => array (
                //'model' => $modelName, FIX Problème multidélibération
                'foreign_key' => $foreignKey,
                'joindre_fusion' => true),
            'order' => array('id ASC')));

        // nombre d'annexes
        if ($modelOdtInfos->hasUserFieldDeclared('nombre_annexe'))
            $aData['nombre_annexe']= count($annexes);//, 'text'));
        if (empty($annexes)){
            if ($modelOdtInfos->hasUserFieldDeclared('titre_annexe'))
                 $aAnnexe['titre_annexe'] = array('value'=> '', 'type'=>'text');
            if ($modelOdtInfos->hasUserFieldDeclared('nom_fichier'))
                $aAnnexe['nom_fichier'] = array('value'=> '', 'type'=>'text');
            if ($modelOdtInfos->hasUserFieldDeclared('fichier'))
                 $aAnnexe['nom_fichier'] = array('value'=> file_get_contents(APP.DS.'Config'.DS.'OdtVide.odt'), 'type'=>'content');
            
            $aData['Annexes'][]=$aAnnexe;
            return;
        }

        // fusion des variables pour chaque annexe
        foreach($annexes as $annexe) {
            $aAnnexe=array();
            if (!empty($annexe['Annex']['titre']))
                $aAnnexe['titre_annexe']= array('value'=> $annexe['Annex']['titre'], 'type'=>'text');
            if (!empty($annexe['Annex']['filename']))
                $aAnnexe['nom_fichier']= array('value'=> $annexe['Annex']['filename'], 'type'=>'text');
            if (!empty($annexe['Annex']['edition_data']))
                $aAnnexe['fileodt.fichier']=array('value'=> $annexe['Annex']['edition_data'], 'type'=>'file');
            
            $aData['Annexes'][]=$aAnnexe;
        }
    }

    function getContentToGed($annex_id) {
        $DOC_TYPE = Configure::read('DOC_TYPE');

        $annex = $this->find('first', array('conditions' => array('Annex.id' => $annex_id),
            'recursive' => -1,
            'fields' => array('filetype', 'data', 'data_pdf', 'filename', 'titre', 'joindre_ctrl_legalite')));

        if ($annex['Annex']['filetype'] === 'application/pdf')
            return array(
                'type' => $DOC_TYPE[$annex['Annex']['filetype']]['extension'],
                'filetype' => 'application/pdf',
                'name' => AppTools::getNameFile($annex['Annex']['filename']) . '.pdf',
                'filename' => $annex_id. '.pdf',
                'titre' => $annex['Annex']['titre'],
                'joindre_ctrl_legalite' => $annex['Annex']['joindre_ctrl_legalite'],
                'data' => $annex['Annex']['data']);

        if ($annex['Annex']['joindre_ctrl_legalite'] && !empty($annex['Annex']['data_pdf']))
            return array(
                'type' => 'pdf',
                'filetype' => 'application/pdf',
                'name' => AppTools::getNameFile($annex['Annex']['filename']).'.pdf',
                'filename' => $annex_id.'.pdf',
                'titre' => $annex['Annex']['titre'],
                'joindre_ctrl_legalite' => $annex['Annex']['joindre_ctrl_legalite'],
                'data' => $annex['Annex']['data_pdf']
            );

        return array('type' => $DOC_TYPE[$annex['Annex']['filetype']]['extension'],
            'filetype' => $annex['Annex']['filetype'],
            'name' => $annex['Annex']['filename'],
            'filename' => $annex_id.'.'.$DOC_TYPE[$annex['Annex']['filetype']]['extension'],
            'titre' => $annex['Annex']['titre'],
            'joindre_ctrl_legalite' => $annex['Annex']['joindre_ctrl_legalite'],
            'data' => $annex['Annex']['data']);
    }

}
