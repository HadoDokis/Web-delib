<?php

/**
 * Code source de la classe Histochoixcer93Test.
 *
 * PHP 5.3
 *
 * @package app.Test.Case.Model
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */
App::uses('Traitement', 'Cakeflow.Model');
App::uses('DeliberationsController', 'Controller');
App::uses('ComponentCollection', 'Controller');
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
class DeliberationsControllerTest extends CakeTestCase {

    public $collection = null;
    // Les fixtures de plugin localisé dans /app/Plugin/Blog/Test/Fixture/
    public $fixtures = array(
        'Deliberationseance',
        'Seance',
        'Typeseance',
        'Typeacte',
        'Collectivite',
        'Theme',
        'Annex',
        'Infosupdef',
        'plugin.ModelOdtValidator.Modeltemplate',
        'plugin.cakeflow.circuit',
        'plugin.cakeflow.traitement',
        'plugin.cakeflow.visa',
        'plugin.cakeflow.etape',
        'plugin.cakeflow.composition',
        CAKEFLOW_USER_MODEL,
        CAKEFLOW_TARGET_MODEL
    );
    public $Deliberation;
    public $Deliberationseance;
    public $Circuit;
    public $DeliberationsController;
    public $Traitement;
    public $Modeltemplate;

    public function setUp() {
        parent::setUp();
        $this->Deliberation = ClassRegistry::init('Deliberation');
        $this->Deliberationseance = ClassRegistry::init('Deliberationseance');
        //$this->Circuit = ClassRegistry::init('Cakeflow.Circuit');
        //$this->collection = new ComponentCollection();
        //$this->DeliberationsController = new DeliberationsController($collection);
        //$this->Traitement = ClassRegistry::init('Cakeflow.Traitement');

        $this->Modeltemplate = ClassRegistry::init('Modeltemplate');
        $this->Modeltemplate->id = 1;
        $aData['content'] = file_get_contents(APP . 'Plugin/ModelOdtValidator/Test/Data/Modeltemplate1.odt');
        $this->Modeltemplate->save($aData, false);
    }

    /**
     * Méthode exécutée avant chaque test.
     *
     * @return void
     */
    public function tearDown() {
        unset($this->Deliberation);
        unset($this->Deliberationseance);
        unset($this->Circuit);
        unset($this->DeliberationsController);
        unset($this->Traitement);
    }

    /* public function testaddIntoCircuit() {    
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
      $this->DeliberationsController->_notifier($id, empty($user_id)?$user_connecte:$user_id, 'traiter');
      $prem = false;
      } else {
      foreach ($etape as $user_id)
      $this->DeliberationsController->_notifier($id, empty($user_id)?$user_connecte:$user_id, 'insertion');
      }
      }

      // faire des tests utiles ici
      // $this->assertTrue(is_object($this->BlogPost)); */

    //}


    public function test_delegToParapheurDocument() {

        $this->Deliberation->id = 1;
        $result = $this->Deliberation->_delegToParapheurDocument();
        $expected = array(
            'docPrincipale' => $result['docPrincipale'], //FIX remplacer par le fichier sortie dans Data
            'annexes' => array()
        );

        $this->assertEquals($result, $expected, var_export($result, true));
    }

}
