<?php
class NavigationHelper extends HtmlHelper
{
    var $tab = "\t";
     
	function buildMenu($links = array(), $ulId = "", $ulClass = "displayed", $level = 0)
    {   
    	$ulTab = str_repeat($this->tab, $level);
    	$liTab = str_repeat($this->tab, $level+1);
		$cpt = 0;
		$tmp = "";

		$out = $ulTab."<ul id=\"$ulId\" class=\"$ulClass\">\n";
        foreach ($links as $title => $data)
        {	
        	if(($this->url($data['link']) == $this->here) || (!empty($data['submenu']) && $this->submenu_array_contains($data['submenu'], $this->here)))
			{
				$liClass = "here";
				$ulClass = "displayed";
			}
			else
			{
				$liClass = "";
				$ulClass = "hidden";
			}

        	$tmp .= $liTab."<li class=\"$liClass\">".$this->link($title,$data['link']);
			if(empty($data['submenu']))
			{
				$tmp .= "</li>\n";
			}
			else
			{
				$tmp .= "\n".$this->buildMenu($data['submenu'],"subnav-$cpt", $ulClass, $level+2);
				$tmp .= $liTab."</li>\n";
				
			}
			$cpt++;
        }
        
        $out .= $tmp;
        $out .= $ulTab."</ul>\n";
        return $out;
    }
    
    function submenu_array_contains($submenu_array, $value)
    {
        $links = array_values($submenu_array);
        $contains = false;
        foreach($links as $link) 
        {
            if($this->url($link['link']) == $value)
            {
                $contains = true;
            }
        }
        return $contains;
    }    
}
?>