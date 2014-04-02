<?php

/**
 * Class MenuComponent
 * Composant qui permet de charger le menu, la table des droits, etc.
 */
class MenuComponent extends Component {
    var $components = array('Acl', 'Droits');

    function load($varNameMenu, $aro = null) {
        // lecture du menu du fichier menu.ini.php
        include(ROOT . DS . APP_DIR . DS . 'Config' . DS . 'menu.ini.php');
        $menuRet = ${
        $varNameMenu};

        // chargement des droits
        if (!empty($aro)) $this->_filtreMenuDroits($menuRet, $aro);

        return $menuRet;
    }

    function _filtreMenuDroits(&$menu, $aro) {

        $items = $menu['items'];
        foreach ($items as $title => $menuItem) {
            // Calcul du aco en fonction du lien
            $aco = $this->_calcAction($menuItem['link']);

            // Vérifie les droits
            if ($this->Droits->check($aro, $aco)) {

                // sous-menu
                if (array_key_exists('subMenu', $menuItem) and
                    is_array($menuItem['subMenu']) and count($menuItem['subMenu']) > 0
                ) {
                    $this->_filtreMenuDroits($menu['items'][$title]['subMenu'], $aro);
                };
            } else
                unset($menu['items'][$title]);

        };

    }

    /* retourne le couple Controler:action pour le lien passé en paramètre */
    function _calcAction($lien) {
        if (empty($lien) or $lien == '/')
            return 'Pages:home';

        // le lien n'est pas vide
        if ($lien[0] != '/') $lien = 'pages/' . $lien;
        $lienTab = array_filter(explode('/', $lien));
        $action = count($lienTab) == 1 ? 'index' : array_pop($lienTab);
        $controller = ucwords(array_pop($lienTab));
        return $controller . ':' . $action;
    }


    /**
     * Retourne la valeur $key du menu $menu si elle existe et si elle et non null,
     * et retourne $default dans le cas contraire.
     *
     * @param array $menu Données du menu
     * @param string $key Nom de la valeur à traiter
     * @param string $default Valeur par défaut
     * @access private
     * @return null
     */
    function _getArrayValue($menu, $key, $default = null) {
        if (!array_key_exists($key, $menu)) return $default;

        if (is_array($menu[$key])) {
            if (count($menu[$key]) < 1) return $default;
            return $menu[$key][0] ? $menu[$key][0] : $default;
        };

        return $menu[$key] ? $menu[$key] : $default;
    }

    function listeAliasMenuControlleur() {
        // liste des alias du menu
        $listeAliasMenu = $this->_listeAliasMenu($this->load('webDelib'));

        // Ajout des actions de tous les controllers du projet qui ne sont pas référencées par le menu
        $listeAliasCtrl = array();
        $controllerList = $this->_listClasses(APP . "Controller" . DS, '*Controller.php', array('AppController.php', 'PagesController.php'));

        $plugins = App::objects('plugin');
        foreach ($plugins as $plugin) {
            $plugin_controllers = $this->_listClasses(APP . "Plugin" . DS . $plugin . DS . "Controller" . DS, '*Controller.php', array('AppController.php', 'PagesController.php'));
            $controllerList = array_merge($controllerList, $plugin_controllers);
        }
        foreach ($controllerList as $controllerFile) {
            $ctrl_alias = 'Module:' . Inflector::camelize(str_replace('Controller.php', '', $controllerFile));
            $controllerName = Inflector::camelize(str_replace('Controller.php', '', $controllerFile)) . 'Controller';
            if (substr($controllerName, strlen($controllerName) - strlen('Controller'), strlen($controllerName)) == 'Controller')
                $controllerName = substr($controllerName, 0, strlen($controllerName) - strlen('Controller'));

            $listeActions = $this->Droits->listeActionsControleur($controllerName);
            // Liste des actions de ce controlleur qui ne sont pas dans le menu
            $actionPlus = array();
            foreach ($listeActions as $action) {
                $trouve = false;

                $aliasAction = Inflector::camelize(str_replace('Controller.php', '', $controllerFile)) . ':' . $action;
                foreach ($listeAliasMenu as $aliasMenu)
                    if ($aliasAction === $aliasMenu['alias']) {
                        $trouve = true;
                        break;
                    }
                if (!$trouve) $actionPlus[] = array('alias' => $aliasAction, 'parent_alias' => $ctrl_alias);
            }
            if (!empty($actionPlus)) {
                $listeAliasCtrl[] = array('alias' => $ctrl_alias, 'parent_alias' => '');
                $listeAliasCtrl = array_merge($listeAliasCtrl, $actionPlus);
            }
        }
        return array_merge($listeAliasMenu, $listeAliasCtrl);
    }

    /**
     * fonction récursive du parcours des entrées du menu
     */
    function _listeAliasMenu($menu, $parentAlias = '') {
        // Initialisation
        $ret = array();

        $items = $menu['items'];
        foreach ($items as $title => $menuItem) {
            // Calcul de l'alias aco en fonction du lien
            $alias = $this->_calcAction($menuItem['link']);
            $ret[] = array('alias' => $alias, 'parent_alias' => $parentAlias);
            // sous-menu
            if (array_key_exists('subMenu', $menuItem) and
                is_array($menuItem['subMenu']) and
                count($menuItem['subMenu']) > 0
            )
                $ret = array_merge($ret, $this->_listeAliasMenu($menu['items'][$title]['subMenu'], $alias));
        }
        return $ret;
    }

    /**
     * Returns an array of filenames of PHP files in given directory.
     *
     * @param  string $path Path to scan for files
     * @param string $filtre
     * @param array $excluded
     * @return array  List of files in directory
     */
    function _listClasses($path, $filtre = '*.php', $excluded = array()) {
        $ret = array();
        $dir = glob($path . $filtre);
        foreach ($dir as $fileUri) {
            if (is_dir($fileUri)) continue;
            $filename = basename($fileUri);
            if (!empty($excluded) && in_array($filename, $excluded)) continue;
            $ret[] = $filename;
        }
        return $ret;
    }

    /**
     * Retourne la liste du menu principal et des controleurs pour l'affichage de l'onglet des droits
     * Initialise la valeur 'modifiable' en fonction des droits de la collectivité-rôle-utilisateur
     * Intitialise à True le champ 'modifiable' si $cru est vide
     * @param array $cru ('model'=>string, 'foreign_key'=>integer)
     * @return array(
     *    [title] => affiché de vant la case à cocher
     *    [acosAlias] => alias de la table acos
     *    [niveau] => 0:menu principal, 1:sous-menu, 2:sous-sous-menu, ...
     *    [nbSousElements] => nb de sous éléments du menu
     *    [modifiable] => false|true indique si la case à cocher est accessible ou pas.
     */
    function menuCtrlActionAffichage($cru = null) {
        $ret = array();


        // Chargement de l'arborescence du menu
        $this->_chargeMenuControllers($ret, $this->load('webDelib'), $cru);

        // Chargement des controleurs/Action qui ne sont pas dans le menu
        $this->_chargeControllersActions($ret, $cru);

        return $ret;
    }

    /**
     * construit l'arborescence du menu et des controleurs dans le tableau $menuCtrlTree
     * @param $menuCtrlTree
     * @param $menu
     * @param null $cru
     * @param int $niveau
     * @return int nombre d'éléments du menu
     */
    function _chargeMenuControllers(&$menuCtrlTree, $menu, $cru = null, $niveau = 0) {
        // Initialisations
        $nbTotElement = 0;

        // Parcours des items du menu
        $items = $menu['items'];
        foreach ($items as $title => $menuItem) {
            $menuCtrlTree[] = array();
            $key = count($menuCtrlTree) - 1;
            $acosAlias = $this->_calcAction($menuItem['link']);
            $menuCtrlTree[$key]['title'] = ($niveau == 0 ? 'Menu:' : '') . $title;
            $menuCtrlTree[$key]['acosAlias'] = $acosAlias;
            $menuCtrlTree[$key]['niveau'] = $niveau;
            $menuCtrlTree[$key]['nbSousElements'] = 0;
            $menuCtrlTree[$key]['modifiable'] = empty($cru) ? true : $this->Acl->Check($cru, $acosAlias);

            // Traitement des sous-menus
            if (array_key_exists('subMenu', $menuItem) and
                is_array($menuItem['subMenu']) and count($menuItem['subMenu']) > 0
            ) {
                $menuCtrlTree[$key]['nbSousElements'] += $this->_chargemenuControllers($menuCtrlTree, $menuItem['subMenu'], $cru, $niveau + 1);
            }

            $nbTotElement += $menuCtrlTree[$key]['nbSousElements'] + 1;
        }
        return $nbTotElement;
    }

    /**
     * ajoute les actions des controller qui ne sont pas liés au menu $menuCtrlTree
     * retourne le nombre d'éléments ajoutés
     *
     */
    function _chargeControllersActions(&$menuCtrlTree, $cru = null) {

        // Initialisation
        $nbElements = 0;

        // Parcours des controleurs
        $controllerList = $this->_listClasses(APP . "Controller" . DS, '*Controller.php', array('AppController.php', 'PagesController.php', 'CakeflowAppController.php'));
        $plugins = App::objects('plugin');
        foreach ($plugins as $plugin) {
            $plugin_dir_controller = APP . "Plugin" . DS . $plugin . DS . "Controller" . DS;
            $controllerList = array_merge($controllerList, $this->_listClasses($plugin_dir_controller, '*Controller.php', array('AppController.php', 'PagesController.php', 'CakeflowAppController.php')));
        }
        foreach ($controllerList as $controllerFile) {
            if (strpos($controllerFile, 'Controller.php') > 0) {
                $controllerName = Inflector::camelize(str_replace('Controller.php', '', $controllerFile));
                $listeActions = $this->Droits->listeActionsControleur($controllerName);
                // Supprime les actions déjà liées au menu
                foreach ($listeActions as $key => $action) {
                    if ($this->_trouveAction($controllerName . ':' . $action, $menuCtrlTree)) unset($listeActions[$key]);
                }
                // Ajout à la liste des menus-controleurs
                if (!empty($listeActions)) {
                    $menuCtrlTree[] = array();
                    $key = count($menuCtrlTree) - 1;
                    $acosAlias = 'Module:' . $controllerName;
                    $menuCtrlTree[$key]['title'] = $acosAlias;
                    $menuCtrlTree[$key]['acosAlias'] = $acosAlias;
                    $menuCtrlTree[$key]['niveau'] = 0;
                    $menuCtrlTree[$key]['nbSousElements'] = count($listeActions);
                    $menuCtrlTree[$key]['modifiable'] = empty($cru) ? true : $this->Acl->Check($cru, $acosAlias);

                    // Ajoute les libellés des actions
                    $listeLibelles = $this->Droits->libellesActionsControleur($controllerName, $listeActions);
                    $i = 0;
                    foreach ($listeActions as $action) {
                        $key++;
                        $acosAlias = $controllerName . ':' . $action;
                        $menuCtrlTree[$key]['title'] = $listeLibelles[$i];
                        $menuCtrlTree[$key]['acosAlias'] = $acosAlias;
                        $menuCtrlTree[$key]['niveau'] = 1;
                        $menuCtrlTree[$key]['nbSousElements'] = 0;
                        $menuCtrlTree[$key]['modifiable'] = empty($cru) ? true : $this->Acl->Check($cru, $acosAlias);
                        $i++;
                    }

                    $nbElements += 1 + count($listeActions);
                }
            }
        }
        return $nbElements;
    }

    /**
     * retourne True si l'action $actionName est dans $menuCtrlTree
     */
    function _trouveAction($actionName, $menuCtrlTree) {


        foreach ($menuCtrlTree as $menuCtrl) {
            if ($menuCtrl['acosAlias'] == $actionName) return true;
        }
        return false;
    }

}