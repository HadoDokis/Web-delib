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

class TdtMessageFixture extends CakeTestFixture {
    
   // public $import = 'TdtMessage';
    var $import = array( 'model' => 'TdtMessage', 'records' => false);
    
    public $records;
    
    /**
    * Définition des enregistrements.
    *
    * @var array
    */
    public function init() {
        $this->records = array(
            array( 'id' =>1,
                   'delib_id'=>1,
                   'tdt_id'=>10002,
                   'tdt_type' => 3,
                   'tdt_etat' => 7,
                   'parent_id' =>  NULL,
                    'created' => date('Y-m-d H:i:s'),
                   'modified' => date('Y-m-d H:i:s'),
                ),
            array( 'id' =>2,
                   'delib_id'=>2,
                   'tdt_id'=>10001,
                   'tdt_type' => 4,
                   'tdt_etat' => 7,
                   'parent_id' =>  NULL,
                   'created' => date('Y-m-d H:i:s'),
                   'modified' => date('Y-m-d H:i:s'),
                ),
            array( 'id' =>3,
                   'delib_id'=>1,
                   'tdt_id'=>10004,
                   'tdt_type' => 3,
                   'tdt_etat' => 7,
                   'parent_id' =>  NULL,
                    'created' => date('Y-m-d H:i:s'),
                   'modified' => date('Y-m-d H:i:s'),
                ),
            array( 'id' =>4,
                   'delib_id'=>2,
                   'tdt_id'=>10003,
                   'tdt_type' => 2,
                   'tdt_etat' => 7,
                   'parent_id' =>  NULL,
                   'created' => date('Y-m-d H:i:s'),
                   'modified' => date('Y-m-d H:i:s'),
                ),
            array( 'id' =>5,
                   'delib_id'=>2,
                   'tdt_id'=>10005,
                   'tdt_type' => 2,
                   'tdt_etat' => 7,
                   'parent_id' =>  NULL,
                   'created' => date('Y-m-d H:i:s'),
                   'modified' => date('Y-m-d H:i:s'),
                ),
        );
        
        parent::init();
    }
}

?>