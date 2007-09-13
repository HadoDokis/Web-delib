<?php
class TreeHelper extends Helper
{
	var $tab = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	var $hasChildren = false;
 	
//	function showTree($modelName, $fieldName, $data, $baseUrl, $actions=null)
//	{
//  		$output = $this->list_element($data, $modelName, $fieldName, 0, $baseUrl, $actions);
//		return $this->output($output);
//	}
  
	//function showTree($data, $modelName, $fieldName, $level, $baseUrl, $actions)
	function showTree($modelName,$fieldName,$data,$level,$baseUrl,$actions)
	{
		$tabs = str_repeat($this->tab, $level);
  		$output = "";
  	
	  	foreach ($data as $key=>$val)
	  	{
	  		$hasChildren = isset($val['children'][0]);
	  		$output .= $tabs.$val[$modelName][$fieldName]." ".$this->buildActions($baseUrl, $actions, $modelName, $val[$modelName]['id'], $hasChildren)."<br/>\n";
	  		
	  		if($hasChildren)
	  		{
	  			$output .= $this->showTree($modelName, $fieldName, $val['children'], $level+1, $baseUrl, $actions);
	  		}
	  	}
	  	return $output;
	}
  
	function buildActions($baseUrl, $actions, $modelName, $id, $hasChildren)
	{
  		$urls = "&nbsp;&nbsp;";
  		
  		foreach($actions as $key=>$value)
  		{
			$controllerName = Inflector::pluralize(strtolower($modelName));
			
  			if($value != "delete" || ($value == "delete" && !$hasChildren))
			{
				$urlToAdd = "<a href=\"".$baseUrl."/".$controllerName."/".$value."/".$id."\" >".$key."</a> ";
			}
			else
			{
				$urlToAdd = "<span class=\"inactive\">".$key."</span>";
			}
			$urls .= $urlToAdd." ";
  		}
  		return $urls;
	}
}
?> 
