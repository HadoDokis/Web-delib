<?php
class TreeHelper extends AppHelper {

	var $tab = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	var $hasChildren = false;
 	
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
?> 
