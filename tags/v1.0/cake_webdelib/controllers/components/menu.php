<?php
/*
 */

class MenuComponent extends Object
{
	var $components = array('Acl');

    function load($varNameMenu, $aro=null)
    {
		// lecture du menu du fichier menu.ini.php
    	include_once(CONFIGS . 'menu.ini.php');
    	$menuRet = ${$varNameMenu};

    	// chargement des droits
		if (!empty($aro)) $this->_filtreMenuDroits($menuRet, $aro);

    	return $menuRet;
    }

    function _filtreMenuDroits(&$menu, $aro) {

		$items = $menu['items'];
		foreach($items as $title => $menuItem)
		{
			// Calcul du aco en fonction du lien
			$aco = $this->_calcAction($menuItem['link']);

			// Vérifie les droits
			if ($this->Acl->Check($aro, $aco, "*")) {
				// sous-menu
				if (array_key_exists('subMenu', $menuItem) and
					is_array($menuItem['subMenu']) and count($menuItem['subMenu'])>0) {
					$this->_filtreMenuDroits($menu['items'][$title]['subMenu'], $aro);
				};
			} else unset($menu['items'][$title]);

		};

    }

/* retourne le couple Controler:action pour le lien passé en paramètre */
	function _calcAction($lien) {

		// Traitement du lien
		if (empty($lien) or $lien == '/') return 'Pages:home';
		else {
			if ($lien[0] == '/') $lien = substr($lien, 1);
			else $lien = 'pages/' . $lien;
			$tabAction = explode('/', $lien);
			$tabAction[0] = ucwords($tabAction[0]);
			if (count($tabAction) == 1) $tabAction[] = 'index';
			return $tabAction[0] . ':' . $tabAction[1];
		};
	}


/**
 * Retourne la valeur $key du menu $menu si elle existe et si elle et non null,
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
