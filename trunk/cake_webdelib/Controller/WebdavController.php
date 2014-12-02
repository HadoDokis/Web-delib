<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

App::uses('AppController', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');


use Sabre\DAV;

/**
 * CakePHP WebdavController
 * @author splaza
 */
class WebdavController extends AppController {
    
    // Gestion des droits
    public $aucunDroit;
    
    public $demandeDroit = array();
    
    /*var $commeDroit = array(
        'edit' => 'Typeactes:index',
        'add' => 'Typeactes:index',
        'delete' => 'Typeactes:index',
        'view' => 'Typeactes:index',
    );*/

    public function beforeFilter() {
        
        $this->SabreDav = $this->Components->load('SabreDav');
        $this->SabreDav->Server();
    }
    
    public function index() {
        
    }

}
