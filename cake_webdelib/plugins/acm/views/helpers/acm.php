<?php
class AcMHelper extends Helper {

    var $helpers = array('Html','Javascript','Ajax');

	function buildListTree($data)
	{
		foreach ($data as $node){
			if (isset($node['children']) && is_array($node['children']) && count($node['children']))
			{
				$out[$node['alias']] = $this->buildListTree($node['children']);
			}
			else
			{
				$out[$node['alias']] = $node['alias'];
			}
		  }
	    return $out;
	}

	//Old Functions Below
	function guiListTree($data, $htmlAttributes = null, $nodeKey = 'id', $childKey='children', $attributeKey = 'attributes', $return = false)
	{
		$attr = "";
		if(isset($htmlAttributes) && is_array($htmlAttributes) && count($htmlAttributes))
		{
			$attr = $this->Html->_parseAttributes($htmlAttributes);
		}
		$out = '<ul '.$attr.'>';
		foreach ($data as $node)
		{

			if (isset($node[$childKey]) && is_array($node[$childKey]) && count($node[$childKey]))
			{
				$out .= '<li id="child"><a class="tiny" href="#" onclick="toggleUl(this);return(false);">' . $node[$nodeKey] .' (' . count($node[$childKey]) . ')</a>';
			    $out .= $this->guiListTree($node[$childKey], $node[$attributeKey], $nodeKey, $childKey, $attributeKey);
			}
			else
			{
				$out .= '<li id="nochild">' . $node[$nodeKey] ;
			}
			$out .= '</li>';
		  }
		  $out .= '</ul>';
	    return $out;
	}

	function guiSelectTree($data, $htmlAttributes = null, $nodeKey = 'id', $childKey='children', $attributeKey = 'attributes', $return = false)
	{
		$attr = "";
		if(isset($htmlAttributes) && is_array($htmlAttributes) && count($htmlAttributes))
		{
			$attr = $this->Html->_parseAttributes($htmlAttributes);
		}
		$out = '<ul '.$attr.'>';
		foreach ($data as $node)
		{

			if (isset($node[$childKey]) && is_array($node[$childKey]) && count($node[$childKey]))
			{
				$out .= '<li id="child">' . $node[$nodeKey] .' (' . count($node[$childKey]) . ')<a class="tiny" href="#" onclick="blur(this);toggleUl(this);return(false);"> [+] </a>';
			    $out .= $this->guiSelectTree($node[$childKey], $node[$attributeKey], $nodeKey, $childKey, $attributeKey);
			}
			else
			{
				$out .= '<li id="nochild">' . $node[$nodeKey] ;
			}
			$out .= '</li>';
		}
		$out .= '</ul>';
	    return $out;
	}
	function guiCheckTree($data, $htmlAttributes = null, $nodeKey = 'id', $childKey='children', $attributeKey = 'attributes', $return = false)
	{

		$attr = "";
		if(isset($htmlAttributes) && is_array($htmlAttributes) && count($htmlAttributes))
		{
			$attr = $this->Html->_parseAttributes($htmlAttributes);
		}
		$out = '<ul '.$attr.'>';
		foreach ($data as $node)
		{

			$cbox = '<a href="#" onclick="togglePerm(\''.$node[$nodeKey].'\');/>';

			if (isset($node[$childKey]) && is_array($node[$childKey]) && count($node[$childKey]))
			{
				$out .= '<li id="'.$node[$nodeKey].'">' .$cbox. ' ' . $node[$nodeKey] .' (' . count($node[$childKey]) . ')</a> <a class="tiny" href="#" onclick="blur(this);toggleUl(this);return(false);"> [+] </a> ';
			    $out .= $this->guiCheckTree($node[$childKey], $node[$attributeKey], $nodeKey, $childKey, $attributeKey);
			}
			else
			{
				$out .= '<li id="'.$node[$nodeKey].'">' . $cbox. ' ' . $node[$nodeKey] . ' </a>';
			}
			$out .= '</li>';
		  }
		  $out .= '</ul>';
	    return $out;
	}

	function guiDataGrid($aros, $acos, $nodeKey = 'id', $childKey='children')
	{
		$out = '<div id="grid">';

		$i = 0;
		foreach ($aros as $perms)
		{
			$out .= '<dl>';
			$out .= '<dt><a href="#" onclick="Element.toggle(\'parent_'.$perms['id'].'\')">' . $perms[$nodeKey] . '</a></dt>';
			$out .= '<dl id="parent_'.$perms['alias'].'">';
			$out .= $this->_acoRow($acos, $nodeKey, $childKey, $perms['alias']);
			$out .= '</dl>';

			if(!empty($perms[$childKey]))
			{
				$out .= '<dl>';
				$out .= $this->guiDataGrid($perms[$childKey], $acos, $nodeKey, $childKey);
				$out .= '</dl>';
			}
			$out .= '</dl>';
		$i++;
		}
		$out .= '</div>';
	    return $out;
	}

	function _acoRow($acos, $nodeKey = 'id', $childKey='children',$parent=null)
	{
		$group = $parent;
		$out = null;
		if (!empty($acos))
		{
			$i = 0;
			foreach ($acos as $node)
			{
				$out .= '<dt><a href="#" onclick="Element.toggle(\''.$node['id'].'\')">' . $node[$nodeKey] . '</a></dt>';
				if (!empty($node[$childKey]))
				{
					$parent = $node['alias'];
					$node[$childKey] = array_reverse($node[$childKey]);
					$out .='<dd id="'.$node['id'].'">';
					foreach ($node[$childKey] as $node)
					{
						$out .= '<span class="checkbox">'.$this->checkbox($node[$nodeKey].'/'.$group.'::'.$parent.'::'.$node['alias'], 1, array('url'=>'/acm/allow_deny/','update'=>'test_div')).' '.$node[$nodeKey].'</span>';
					}
					$out .='</dd>';
				}
			$i++;
			}
			return $out;
		}
		return false;
	}
	function _aroRow($acos, $node, $nodeKey, $childKey, $attributeKey = 'attributes')
	{
		$aRow = "";
		foreach ($acos as $node)
		{
			$out .= '<div class="row">
						<span class="title">' . $node[$nodeKey] . '</span>';

			if (!empty($node[$childKey]))
			{
				$cData = array();
				$cData['aros'] = $node[$childKey];
				$cData['acos'] = $node;
				$this->checkbox($node.'/'.$perms[$nodeKey],1, array('url'=>'/acm/allow_deny/','update'=>'test_div'));
			}
			else
			{

			}
			$out .= '</div>';
		}
		return $aRow;
	}
	function checkbox2($fieldName, $title, $options = array(), $confirm = null)
    {
        if (isset($confirm))
        {
            $options['confirm'] = $confirm;
            unset($confirm);
        }

        $htmlOptions = $this->Ajax->__getHtmlOptions($options);
        if (empty($options['fallback']) || !isset($options['fallback']))
        {
            $options['fallback'] = $options['url'];
        }
        if (isset($options['id']))
        {
            $htmlOptions['onclick'] = ' return false;';
            return $this->Html->checkbox($fieldName, $title, $htmlOptions) . $this->Javascript->event("$('{$options['id']}')", "click", $this->Ajax->remoteFunction($options));
        }
        else
        {
            $htmlOptions['onclick'] = $this->Ajax->remoteFunction($options) . '; return false;';
            return $this->Html->checkbox($fieldName, $title, $htmlOptions);
        }
    }

	function checkbox($fieldName, $title, $options = array(), $confirm=null)
    {
		if (isset($confirm))
        {
            $options['confirm'] = $confirm;
            unset($confirm);
        }

        $htmlOptions = $this->Ajax->__getHtmlOptions($options);
        if (!isset($htmlOptions['type']))
        {
            //$htmlOptions['type'] = 'submit';
        }
        $htmlOptions['value'] = $title;

        if (!isset($options['with']))
        {
            $options['with'] = 'Form.serialize(Event.element(event).form)';
        }

        if(!isset($id))
        {
			$field = split('/',$fieldName);
            $id = 'tag_'.$field[1];
        }

        $htmlOptions['onclick'] = "return false;";
        return $this->Html->checkbox($fieldName, $title, $htmlOptions) .
        	$this->Javascript->event('$("'.$id.'")', 'click', $this->Ajax->remoteFunction($options));
    }

}
?>