<?php

class MenuHelper extends Helper {

    var $helpers = array(
        'Session',
        'Bs',
        'Navbar' => array(
            'className' => 'Bootstrap3.BootstrapNavbar'));
    
    var $_navbar;

    function createMenuPrincipale(&$navbar) {
        $this->_navbar=$navbar;
        foreach ($this->Session->read('Auth.Navbar') as $key=>$menu) {
            $this->_createMenu($key, $menu);
        }
    }



    private function _createMenu($title, $menu) {
        $this->_navbar->beginMenu($title, null);
        foreach ($menu['subMenu'] as $key=>$value) {
            switch ($value['html']) {
                case 'link':
                    $this->Navbar->link(
                            (!empty($value['icon']) ? $this->Bs->icon($value['icon']) . ' ' : '') . $value['libelle'], $value['url'], array('escape' => false,
                        'title' => $value['title']));

                    break;
                case 'subMenu':
                    foreach ($value['content'] as $keyContent=>$content) {
                        $this->_createMenu($keyContent, $content);
                    }
                    break;

                case 'divider':
                    $this->Navbar->divider();
                    break;


                default:
                    break;
            }
        }
        $this->_navbar->endMenu();
    }

}
