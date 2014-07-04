<?php
/**
* Code source de la classe ActionFixture.
*
* PHP 5.3
*
* @package app.Test.Fixture
* @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
*/

/**
* Classe ActionFixture.
*
* @package app.Test.Fixture
*/

class DeliberationseanceFixture extends CakeTestFixture 
{
    /**
    * On importe la définition de la table, pas les enregistrements.
    *
    * @var array
    */
    public $import = 'Deliberationseance';
    //var $import = array( 'table' => 'deliberations_seances', 'records' => true);
    public $fields = array(
          'id' => array('type' => 'integer', 'key' => 'primary'),
          'deliberation_id' => array('type' => 'integer', 'null' => false),
          'seance_id' => array('type' => 'integer', 'null' => false),
          'position' => array('type' => 'integer'),
          'avis' => array('type' => 'int'),
          'commentaire' => array('type' => 'string', 'length' => 255)
    );
    /**
    * Définition des enregistrements.
    *
    * @var array
    */
    public function init() {
        $this->records = array(
            array(
                    'deliberation_id'   =>1,
                    'seance_id'         =>1,
                    'position'          =>1,
                    'avis'              =>1,
                    'commentaire'       =>'',
            ),
            array(
                    'deliberation_id'   =>2,
                    'seance_id'         =>1,
                    'position'          =>2,
                    'avis'              =>1,
                    'commentaire'       =>'',
            ),
        );
        parent::init();
    }
}

?>