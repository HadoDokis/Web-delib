<?php

class MenuHelper extends Helper {

    var $helpers = array('Html');
	var $__menuHtml;


	/**
	 * Contruction d'un menu en html utilisant les balises <ul></ul>
	 *
	 * @param array		$menu données du menu
	 * @param boolean	$echo indique si la fonction affiche ou retourne le html du menu (defaut : affiche)
	 * @param boolean	$returnStr indique si la fonction retourne le html du menu sous forme d'un tableau ou d'une chaîne (defaut : chaîne)
	 * @access public
	 *
	 */

	function menu($menu=array(), $echo = true, $returnStr = true)
	{
		// Teste si le menu est non vide
		if (empty($menu) && count($menu) < 1) return null;

		// Initialisations
		$isCurrentItem = false;
		$currentItemFound = false;

		// Lecture des variables du menu
		$menuClass = $this->_getArrayValue($menu, 'menuClass', '');
		$itemTag = $this->_getArrayValue($menu, 'itemTag', 'li');
		$currentItem = $this->_getArrayValue($menu, 'currentItem', '');

		// Initialisation du tableau de chaîne html
		$this->__menuHtml[] = "<ul".ife(!empty($menuClass)," class='$menuClass'").">\n";
		$level = count($this->__menuHtml)-1;

		$items = $menu['items'];
		foreach($items as $title => $menuItem)
		{
			// Initialisation du lien
			if (!array_key_exists('link', $menuItem)) $menuItem['link']='/';
			elseif ($menuItem['link'] == '') $menuItem['link']='/';
			elseif($menuItem['link']{0} != '/' and
					substr($menuItem['link'],0,4) != 'http' and
					substr($menuItem['link'],0,3) != 'www') $menuItem['link'] = '/pages/' . $menuItem['link'];

			// Determine si on est sur l'élément courant
			if (!$currentItemFound) {
				$isCurrentItem = $this->_isCurrent($menuItem);
				$currentItemFound = $isCurrentItem;
			} else $isCurrentItem = false;

			// Construction du html
			$this->__menuHtml[$level] .= '<'.$itemTag.ife($isCurrentItem and !empty($currentItem), ' class=\''.$currentItem.'\'').'>';
			$this->__menuHtml[$level] .= $this->Html->link($title, $menuItem['link'], null, null, false);
			$this->__menuHtml[$level] .= '</'.$itemTag.'>'."\n";

			// Dans le cas du menu courant, teste si il y à un sous-menu et le traite
			if ($isCurrentItem and array_key_exists('subMenu', $menuItem) and
				is_array($menuItem['subMenu']) and count($menuItem['subMenu'])>0) {
					// Initialisation du sous-menu
					$this->_initSubMenu($menu, $menuItem['subMenu'], 'menuClass');
					$this->_initSubMenu($menu, $menuItem['subMenu'], 'itemTag');
					$this->_initSubMenu($menu, $menuItem['subMenu'], 'currentItem');
					// traitement du sous-menu
					$this->menu($menuItem['subMenu']);
				}
		}
		$this->__menuHtml[$level] .= '</ul>'."\n";

		// En fin, on traite le html du menu
		if ($level == 0) {
			if ($echo) echo implode('', $this->__menuHtml);
			elseif ($returnStr) return implode('', $this->__menuHtml);
			else return $this->__menuHtml;
		}
	}

/**
 *
 * Détermine si l'élément de menu $data est l'élément courant
 * ou bien si l'élément courant se trouve dans un de ses sous-menus.
 *
 * @return boolean
 *
 * @param array 	$data Elément de menu
 * @access private
 */
	function _isCurrent($data) {
		// test sur l'élément courant
		if ($data['link'] === substr($this->here, strlen($this->base))) return true;
		else {
			// test sur les éléments du sous-menus
			if (array_key_exists('subMenu', $data) and is_array($data['subMenu']) and count($data['subMenu'])>0) {
				foreach($data['subMenu']['items'] as $title => $menuItem)
				{
					if ($this->_isCurrent($menuItem)) return true;
				}
			}
			return false;
		}
	}

/**
 * Initialise la valeur $key du sous-menu $subMenu avec la valeur $key du menu $menu.
 *
 * @return null
 *
 * @param array &$menu Données du menu
 * @param str &$subMenu Données du sous-menu
 * @param str $key Nom de la valeur à traiter
 * @access private
 */
	function _initSubMenu(&$menu, &$subMenu, $key) {
		$val = null;
		// Sort si il n'y a rien à hériter ou si déjà définis dans le sous-menu
		if (!array_key_exists($key, $menu) or array_key_exists($key, $subMenu)) return;

		// Teste si hérite d'un tableau
		if (is_array($menu[$key])) {
			if (count($menu[$key])>1) $val = array_slice($menu[$key], 1);
			elseif (count($menu[$key])==1) $val = $menu[$key][0];
		} else $val = $menu[$key];

		if (!empty($val)) $subMenu[$key] = $val;
	}

/**
 * Retourne la valeur $key du menu $menu si elle existe et si elle est non null,
 * et retourne $default dans le cas contraire.
 *
 * @return
 *
 * @param array &$menu Données du menu
 * @param str $key Nom de la valeur à traiter
 * @param str $default Valeur par défaut
 * @access private
 */
	function _getArrayValue($menu, $key, $default=null) {
		if (!array_key_exists($key, $menu)) return $default;

		if (is_array($menu[$key])) {
			if(count($menu[$key])<1) return $default;
			return $menu[$key][0] ? $menu[$key][0] : $default;
		};

		return $menu[$key] ? $menu[$key] : $default;
	}

}

?>