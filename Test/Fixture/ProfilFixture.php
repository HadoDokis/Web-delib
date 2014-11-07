<?php
/*
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
class ProfilFixture extends CakeTestFixture {
        var $name = 'Profil';
        var $table = 'profils';
        var $import = array( 'table' => 'profils', 'connection' => 'default', 'records' => false);
         var $records = array(	
        );
}