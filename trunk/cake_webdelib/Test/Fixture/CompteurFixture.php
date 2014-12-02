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

class CompteurFixture extends CakeTestFixture {
        var $import = array( 'model' => 'Compteur', 'records' => false);
        public $records;
        public function init() {
        $this->records = array(
            array( 'id' =>1, 
                   'nom'=>'Deliberations',
                   'commentaire' => 'Numero des deliberations votees dans l\'ordre du jour des seances',
                   'def_compteur' => '#AAAA#_#MM#_#000#',
                   'sequence_id' => 1,
                   'def_reinit' => '#JJ#',
                   'val_reinit' => '27',
                   'created' => date('Y-m-d H:i:s'),
                   'modified' => date('Y-m-d H:i:s'),
                ),
        );
        
        parent::init();
    }
}