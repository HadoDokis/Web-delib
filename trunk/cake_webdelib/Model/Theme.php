<?php
class Theme extends AppModel {

	var $name = 'Theme';
	
	var $displayField = "libelle";
	
	var $actsAs = array('Tree');
	
	var $validate = array(
		'libelle' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrer le libellé.'
			)
		)
	);

    function getLevel($theme_id, $level=1) { 
        $theme = $this->find('first', array('conditions' => array('Theme.id' => $theme_id),
                                            'recursive'  => -1,
                                            'fields'     => array('id', 'parent_id')));

        if($theme['Theme']['parent_id']==0){
            return ($level);
        }
        else {
            $level = $level +1;
            return ($this->getLevel($theme['Theme']['parent_id'], $level));
        }
    }

    function getLibelleParent($theme_id, $level =1, $tableau = array() ) {
        $theme = $this->find('first', array('conditions' => array('Theme.id' => $theme_id),
                                            'recursive'  => -1,
                                            'fields'     => array('id', 'parent_id', 'libelle')));
        $tableau[$level] = $theme['Theme']['libelle'];
        if($theme['Theme']['parent_id']==0){
            return ($tableau);
        }
        else {
            $level = $level +1;
            return ($this->getLibelleParent($theme['Theme']['parent_id'], $level,  $tableau));
        }


    }

    /**
     * fonction d'initialisation des variables de fusion pour le thème d'un projet
     * les bibliothèques Gedooo doivent être inclues par avance
     * génère une exception en cas d'erreur
     * @param object_by_ref $oMainPart variable Gedooo de type maintPart du document à fusionner
     * @param object_by_ref $modelOdtInfos objet PhpOdtApi du fichier odt du modèle d'édition
     * @param integer $id id du modèle lié
     */
    function setVariablesFusion(&$aData, &$modelOdtInfos, $id) {
        $aTheme=array();
        $theme = $this->find('first', array(
            'recursive'  => -1,
            'fields'     => array('libelle', 'order', 'lft', 'rght'),
            'conditions' => array('Theme.id' => $id)));

        if ($modelOdtInfos->hasUserFieldDeclared('theme_projet'))
            $aData['theme_projet']=$theme['Theme']['libelle'];//,  'text'));
        if ($modelOdtInfos->hasUserFieldDeclared('critere_trie_theme'))
            $aData['critere_trie_theme']=$theme['Theme']['order'];//, 'text'));

        // arborescence des thèmes jusqu'au 10eme niveau
        $libelleThemesLevel = array_fill(1, 10, '');
        $themes = $this->find('all', array(
            'recursive' => -1,
            'fields' => array('libelle'),
            'conditions' => array('lft <='=>$theme['Theme']['lft'], 'rght >='=>$theme['Theme']['rght']),
            'order' => array('lft')));
        foreach($themes as $i=>$theme) 
            $libelleThemesLevel[$i+1] = $theme['Theme']['libelle'];
        foreach($libelleThemesLevel as $level=>$libelleThemeLevel)
            if(!empty($libelleThemeLevel))
                $aData["T".$level."_theme"]=$libelleThemeLevel;//, 'text'));
        
    }

}
?>
