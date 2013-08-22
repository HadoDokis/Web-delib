<?php
	/**
	 * Code source de la classe DateFrenchTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Utility
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'DateFrench', 'Utility' );

	/**
	 * La classe DateFrenchTest ...
	 *
	 * @package app.Test.Case.Utility
	 */
	class DateFrenchTest extends CakeTestCase
	{
        /**
         * Test de la méthode DateFrench::frenchDateConvocation()
         */
        public function testFrenchDateConvocation() {
            $result = DateFrench::frenchDateConvocation( 1377178200 );
            $expected = 'Jeudi 22 août 2013 à 15 h 30';
            $this->assertEquals($result, $expected, var_export($result, true));
        }

        /**
         * Test de la méthode DateFrench::frenchDate()
         */
        public function testFrenchDate() {
            $result = DateFrench::frenchDate( 1377178200 );
            $expected = 'Jeudi 22 août 2013';
            $this->assertEquals($result, $expected, var_export($result, true));
        }

        /**
         * Test de la méthode DateFrench::frDate()
         */
        public function testFrDate() {
            $result = DateFrench::frDate( '2013-08-22' );
            $expected = '22/08/2013';
            $this->assertEquals($result, $expected, var_export($result, true));
        }

        /**
         * Test de la méthode DateFrench::Hour()
         */
        public function testHour() {
            $result = DateFrench::Hour( '2013-08-22 15:30:00' );
            $expected = '15:30';
            $this->assertEquals($result, $expected, var_export($result, true));

            $result = DateFrench::Hour( '2013-08-22 15:30:00', 'hh' );
            $expected = '15';
            $this->assertEquals($result, $expected, var_export($result, true));

            $result = DateFrench::Hour( '2013-08-22 15:30:00', 'mm' );
            $expected = '30';
            $this->assertEquals($result, $expected, var_export($result, true));
        }

        /**
         * Test de la méthode DateFrench::frDate()
         */
        public function testDateLettres() {
            $result = DateFrench::dateLettres( 1377178200 );
            $expected = 'L\'an deux mille treize le vingt deux août ';
            $this->assertEquals($result, $expected, var_export($result, true));
        }
	}
?>