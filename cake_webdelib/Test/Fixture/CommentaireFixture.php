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
class CommentaireFixture extends CakeTestFixture {

    public $import = array( 'model' => 'Commentaire', 'records' => false);
        
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
                   'agent_id' => 0,
                   'texte' => '[Commentaire automatique]totototo',
                   'pris_en_compte' => 0,
                   'commentaire_auto'=> true,
                   'created' => date('Y-m-d H:i:s'),
                   'modified' => date('Y-m-d H:i:s'),
            ),
            array( 'id' =>2, 
                   'delib_id'=>1,
                   'agent_id' => 1,
                   'texte' => '[Commentaire non automatique]Je suis le commentaire du projet id:1',
                   'pris_en_compte' => 0,
                   'commentaire_auto'=> false,
                   'created' => date('Y-m-d H:i:s'),
                   'modified' => date('Y-m-d H:i:s'),
            ),
        );
        
        parent::init();
    }
}