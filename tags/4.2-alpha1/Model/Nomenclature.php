<?php

class Nomenclature extends AppModel {
    var $displayField = "libelle";
    var $actsAs = array('Tree');

    function generatetreelist($conditions = null, $keyPath = null, $valuePath = null, $spacer = '_', $recursive = null) {
        $overrideRecursive = $recursive;
        if (!is_null($overrideRecursive)) {
            $recursive = $overrideRecursive;
        }

        $fields = array("id", "libelle", "lft", "rght");

        $keyPath = '{n}.Nomenclature.id';
        $valuePath = array('{0}{1}{2}', '{n}.tree_prefix', '{n}.Nomenclature.id', '{n}.Nomenclature.libelle');
        $order = 'lft asc';
        $results = $this->find('all', compact('conditions', 'fields', 'order', 'recursive'));
        $stack = array();

        foreach ($results as $i => $result) {
            while ($stack && ($stack[count($stack) - 1] < $result['Nomenclature']['rght'])) {
                array_pop($stack);
            }
            $results[$i]['tree_prefix'] = str_repeat($spacer, count($stack));
            $stack[] = $result['Nomenclature']['rght'];
        }
        if (empty($results)) {
            return array();
        }
        return Set::combine($results, $keyPath, $valuePath);
    }

    function saveArrayToTree($tab) {
        $this->query("TRUNCATE `nomenclatures`");
        foreach ($tab as $key => $value) {
            if (strlen($key) == 1) {
                $this->create();
                $nomenclature['Nomenclature']['parent_id'] = 0;
                $nomenclature['Nomenclature']['libelle'] = $value;
                $nomenclature['Nomenclature']['id'] = $key;
                $this->save($nomenclature);
            } else {
                $this->create();
                $nomenclature['Nomenclature']['parent_id'] = $this->findParent_id($key);
                $nomenclature['Nomenclature']['libelle'] = $value;
                $nomenclature['Nomenclature']['id'] = $key;
                $this->save($nomenclature);
            }
        }
    }

    function findParent_id($key) {
        $pos = strrpos($key, '.');
        $newKey = substr($key, 0, $pos);
        return $newKey;
//        $ligne = $this->find('first', array('conditions' => array('Nomenclature.id' => $newKey),
//            'fields' => array('id'),
//            'recursive' => -1));
//        return ($ligne['Nomenclature']['id']);
    }

}
