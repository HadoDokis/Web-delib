<?php
class TreeHelper extends AppHelper {

	var $tab = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	var $hasChildren = false;

    /**
     * Génére une liste html pour affichage d'arbre (dé)pliant
     * @see plugin jsTree http://www.jstree.com/
     *
     * @param $threaded données récupérées par find('threaded')
     * @param $modelname nom du modèle
     * @param array $selected données sélectionnées
     * @param string $displayfield attribut du modèle à afficher
     * @param string $idfield attribut du modèle servant d'identifiant
     * @param int $level niveau d'inclusion
     * @return string liste html sous forme d'arbre
     */
    public function generateList($threaded, $modelname, $selected = array(), $displayfield = 'libelle', $idfield = 'id', $level = 0){
        $treelist = '<ul>';
        foreach ($threaded as $thread){
            $id = $thread[$modelname][$idfield];
            $name = $thread[$modelname][$displayfield];
            $type = 'level'.$level;
            $class = in_array($id, $selected) ? ' node-selected' : '';
            if (in_array($id, $selected)){
                $treelist .= '<li id="'.$modelname.'_'.$id.'" data-id="'.$id.'" class="'.$class.'" data-jstree=\'{ "selected" : true, "type" : "'.$type.'"}\'>';
            }else{
                $treelist .= '<li id="'.$modelname.'_'.$id.'" data-id="'.$id.'" class="'.$class.'" data-jstree=\'{ "type" : "'.$type.'"}\'>';
            }
            $treelist .= $name;
            if (!empty($thread['children'])){
                $treelist .= $this->generateList($thread['children'], $modelname, $selected, $displayfield, $idfield, $level+1);
            }
            $treelist .= '</li>';
        }
        $treelist .= '</ul>';
        return ($treelist);
    }

    /**
     * @param $threaded
     * @param $modelname
     * @param array $fields
     * @param int $level
     * @return string
     */
    public function generateIndex($threaded, $modelname, $fields = array(), $level = 0){
        if (empty($fields['id'])) $fields['id'] = 'id';
        if (empty($fields['display'])) $fields['display'] = 'libelle';
        $treelist = '<ul>';
        foreach ($threaded as $thread){
            $id = $thread[$modelname][$fields['id']];
            $name = $thread[$modelname][$fields['display']];
            $type = 'level'.$level;
            $class = !empty($thread[$modelname]['deletable']) ? 'deletable' : '';
            $treelist .= '<li id="'.$modelname.'_'.$id.'" class="'.$class.'" data-id="'.$id.'" data-jstree=\'{ "type" : "'.$type.'"}\'>';
            if (!empty($fields['order']) && !empty($thread[$modelname][$fields['order']]))
                $treelist .= "[{$thread[$modelname][$fields['order']]}] ";
            $treelist .= $name;
            if (!empty($thread['children'])){
                $treelist .= $this->generateIndex($thread['children'], $modelname, $fields, $level+1);
            }
            $treelist .= '</li>';
        }
        $treelist .= '</ul>';
        return ($treelist);
    }


	function showTree($modelName,$fieldName,$data,$level,$baseUrl,$actions, $order = null) {
		$tabs = str_repeat($this->tab, $level);
  		$output = "";
  	
	  	foreach ($data as $key=>$val) {
            if ($val[$modelName]['actif'] == 0) continue;
            $lesActions = $actions;
            if ((isset($val[$modelName]['deletable'])) && ($val[$modelName]['deletable'] != 1)) $lesActions = Set::remove($lesActions,'Supprimer');
	  		$hasChildren = isset($val['children'][0]) && $val['children'][0][$modelName]['actif'];
	  		$output .= $tabs."<span class=\"profil\">";
            if ($order != null) 
                $output .= '<i>['.$val[$modelName][$order].']  </i>';
            $output .= $val[$modelName][$fieldName];
            $output .= "</span> ".$this->buildActions($baseUrl, $lesActions, $modelName, $val[$modelName]['id'], $hasChildren)."<br/>\n";
	  		if($hasChildren) {
	  			$output .= $this->showTree($modelName, $fieldName, $val['children'], $level+1, $baseUrl, $actions, $order);
	  		}
	  	}
	  	return $output;
	}
  
	function buildActions($baseUrl, $actions, $modelName, $id, $hasChildren) {
  		$urls = "&nbsp;&nbsp;[";
  		
  		foreach($actions as $key=>$value) {
			$controllerName = Inflector::pluralize(strtolower($modelName));
			
  			if($value != "delete") {
				$urlToAdd = "<a href=\"".$baseUrl."/".$controllerName."/".$value."/".$id."\" >".$key."</a> | ";
			}
			if($value == "delete" && !$hasChildren) {
				$urlToAdd = "<a href=\"".$baseUrl."/".$controllerName."/".$value."/".$id."\" onclick=\"return confirm(&#039;Voulez-vous supprimer ce ".strtolower($modelName)." ?&#039;);\" >".$key."</a> | ";
			}
			elseif( $value == "delete" && $hasChildren) {
				$urlToAdd = "";
			}
			$urls .= $urlToAdd;
  		}
		return substr($urls,0,-3)."]";
	}
}