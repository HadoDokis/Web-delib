<?php
/**
* Code source de la classe Histochoixcer93Test.
*
* PHP 5.3
*
* @package app.Test.Case.Model
* @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
*/
//App::uses('Traitement', 'Cakeflow.Model');
App::uses('AppController', 'Controller');
/**
* Classe DeliberationsTest.
*
* @package app.Test.Case.Controller
* 
*/
/**
* Classe CircuitTest.
*
* @package Cakeflow.Test.Case.Controller
* 
*/
class DeliberationsControllerTest extends ControllerTestCase {

    // Les fixtures de plugin localisé dans /app/Plugin/Blog/Test/Fixture/
    public $fixtures = array(   'plugin.cakeflow.circuit',
                                'plugin.cakeflow.traitement',
                                'plugin.cakeflow.visa',
                                'plugin.cakeflow.etape',
                                'plugin.cakeflow.composition',
                                 CAKEFLOW_USER_MODEL,
                                 CAKEFLOW_TARGET_MODEL
                            );
    /*public $Circuit;
    public $Traitement;

    public function setUp() {
        parent::setUp();
        $this->Circuit = ClassRegistry::init('Cakeflow.Circuit');
        $this->Traitement = ClassRegistry::init('Cakeflow.Traitement');
        
    }

    /**
    * Méthode exécutée avant chaque test.
    *
    * @return void
    *//*
    public function tearDown() {
        unset( $this->Circuit );
        unset( $this->Traitement );
    }*/
        
        public function testaddIntoCircuit() {
            
        $id=499;
        $data = array(/*
        'Article' => array(
            'user_id' => 1,
            'published' => 1,
            'slug' => 'new-article',
            'title' => 'New Article',
            'body' => 'New Body'
            )*/
        );
        $result = $this->testAction(
            '/deliberations/addIntoCircuit/'.$id,
            array('data' => $data, 'method' => 'GET')
        );
        //$this->assertRegExp('/<html/', $this->contents);
        //$this->assertRegExp('/<form/', $this->view);
        debug($result);
        /*$Deliberation = $this->generate('Deliberation', array(
        'components' => array(
            'Session',
            'Email' => array('send')
        )
        ));
        $id=499;
        $user_connecte=1;
        $circuit=1;
        $this->Circuit->insertDansCircuit($circuit, $id, $user_connecte);
                    $options = array(
                        'insertion' => array(
                            '0' => array(
                                'Etape' => array(
                                    'etape_nom' => 'Rédacteur',
                                    'etape_type' => 1
                                ),
                                'Visa' => array(
                                    '0' => array(
                                        'trigger_id' => $user_connecte,
                                        'type_validation' => 'V'
                                    )
                                ),
                            )
                        ),
                        'optimisation'=> configure::read('Cakeflow.optimisation')
                    );
                    $traitementTermine = $this->Traitement->execute('IN', $user_connecte, $id, $options);
                    
         // envoi un mail a tous les membres du circuit
        $listeUsers = $this->Circuit->getAllMembers($circuit);
        $prem = true;
        $etape_courante=$this->Traitement->findById($this->Traitement->getEtapeCouranteId($id));
        foreach ($listeUsers as $etape) {
            if ($prem && $etape==$etape_courante) {
                foreach ($etape as $user_id)
                    $this->_notifier($id, empty($user_id)?$user_connecte:$user_id, 'traiter');
                $prem = false;
            } else {
                foreach ($etape as $user_id)
                    $this->_notifier($id, empty($user_id)?$user_connecte:$user_id, 'insertion');
            }
        }

        // faire des tests utiles ici
        $this->assertTrue(is_object($this->BlogPost));*/
    }

}