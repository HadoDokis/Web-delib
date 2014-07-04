<?php
/**
* AllTests file
*
* PHP 5.3
*
* @package       app.Test.Case
* @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
*/

/**
* AllTests class
*
* This test group will run all tests.
*
* @see           http://book.cakephp.org/2.0/en/development/testing.html
* @package       app.Test.Case
*/
class AllTests extends PHPUnit_Framework_TestSuite
{
       /**
        * Test suite with all test case files.
        *
        * @return void
        */
       public static function suite() {
               $suite = new CakeTestSuite( 'All tests' );
               $suite->addTestDirectoryRecursive( TESTS.DS.'Case'.DS );

               // FIXME: tous les plugins ?
               $suite->addTestDirectoryRecursive( APP.DS.'Plugin'.DS.'Appchecks'.DS.'Test'.DS.'Case'.DS );
               $suite->addTestDirectoryRecursive( APP.DS.'Plugin'.DS.'Search'.DS.'Test'.DS.'Case'.DS );
               $suite->addTestDirectoryRecursive( APP.DS.'Plugin'.DS.'Validation'.DS.'Test'.DS.'Case'.DS );

               return $suite;
       }
}
?>