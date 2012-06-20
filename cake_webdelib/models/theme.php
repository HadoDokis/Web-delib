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

    function makeBalise(&$oMainPart, $theme_id) { 
        $tab = array();
        $theme = $this->find('first', array('conditions' => array('Theme.id' => $theme_id),
                                            'recursive'  => -1,
                                            'fields'     => array('libelle', 'order', 'parent_id')));

        $arbre = $this->getLibelleParent($theme_id);
       // $level = $this->getLevel($theme_id);
        $level = count($arbre);
        for ($i=$level+1; $i <= 10; $i++) {
            $oMainPart->addElement(new GDO_FieldType("T".$i."_theme", $tab[$i], 'text'));
        } 
        foreach ($arbre as $index => $libelle) {
            $tab[$level] = utf8_encode($libelle);
            $level--; 
        }
        for ($i =1; $i <= count($tab); $i++) {       
            $oMainPart->addElement(new GDO_FieldType("T".$i."_theme", $tab[$i], 'text'));
        }
        $oMainPart->addElement(new GDO_FieldType('theme_projet',    utf8_encode($theme['Theme']['libelle']),         'text'));
        $oMainPart->addElement(new GDO_FieldType('critere-trie_theme',          utf8_encode($theme['Theme']['order']),         'text'));
    }

}
?>
