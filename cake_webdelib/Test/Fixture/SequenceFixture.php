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

class SequenceFixture extends CakeTestFixture {
        var $import = array( 'model' => 'Sequence', 'records' => false);
        public $records;
        public function init() {
        $this->records = array(
            array( 'id' =>1, 
                   'nom'=>'compteur_jour',
                   'commentaire' => '',
                   'num_sequence' => 245,
                   'created' => date('Y-m-d H:i:s'),
                   'modified' => date('Y-m-d H:i:s'),
                ),
        );
        
        parent::init();
    }
}