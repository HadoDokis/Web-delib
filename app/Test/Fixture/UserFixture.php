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
class UserFixture extends CakeTestFixture {
        var $name = 'User';
        var $table = 'users';
        var $import = array( 'table' => 'users', 'connection' => 'default', 'records' => false);
         var $records = array(	
        );
}