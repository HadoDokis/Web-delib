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

    function getLevel($theme_id, $level = 1) {
        $theme = $this->find('first', array('conditions' => array('Theme.id' => $theme_id),
            'recursive' => -1,
            'fields' => array('id', 'parent_id')));

        if ($theme['Theme']['parent_id'] == 0) {
            return ($level);
        } else {
            $level = $level + 1;
            return ($this->getLevel($theme['Theme']['parent_id'], $level));
        }
    }

    function getLibelleParent($theme_id, $level = 1, $tableau = array()) {
        $theme = $this->find('first', array('conditions' => array('Theme.id' => $theme_id),
            'recursive' => -1,
            'fields' => array('id', 'parent_id', 'libelle')));
        $tableau[$level] = $theme['Theme']['libelle'];
        if ($theme['Theme']['parent_id'] == 0) {
            return ($tableau);
        } else {
            $level = $level + 1;
            return ($this->getLibelleParent($theme['Theme']['parent_id'], $level, $tableau));
        }
    }

    /**
     * Données Gedooo :
     *  - theme_projet/theme.libelle/text
     *  - critere_trie_theme/theme.order/text
     * @param GDO_PartType &$oMainPart adresse de l'objet GDO_PartType à remplir
     * @param integer $theme_id, identifiant du theme en base
     */
    function makeBalise(&$oMainPart, $theme_id) {
        $tab = array();
        $theme = $this->find('first', array('conditions' => array('Theme.id' => $theme_id),
            'recursive' => -1,
            'fields' => array('libelle', 'order', 'parent_id')));

        $arbre = $this->getLibelleParent($theme_id);
        // $level = $this->getLevel($theme_id);
        $level = count($arbre);
        for ($i = $level + 1; $i <= 10; $i++) {
            $oMainPart->addElement(new GDO_FieldType("T" . $i . "_theme", '', 'text'));
        }
        foreach ($arbre as $libelle) {
            $tab[$level] = $libelle;
            $level--;
        }
        for ($i = 1; $i <= count($tab); $i++) {
            $oMainPart->addElement(new GDO_FieldType("T" . $i . "_theme", $tab[$i], 'text'));
        }
        $oMainPart->addElement(new GDO_FieldType('theme_projet', $theme['Theme']['libelle'], 'text'));
        $oMainPart->addElement(new GDO_FieldType('critere_trie_theme', $theme['Theme']['order'], 'text'));
    }

}

?>
