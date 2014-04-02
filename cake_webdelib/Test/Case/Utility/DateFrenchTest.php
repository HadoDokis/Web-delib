<?php
    /**
     * Code source de la classe DateFrenchTest.
     *
     * PHP 5.3
     *
     * @package app.Test.Case.Utility
     * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
     */
    App::uses('DateFrench', 'Utility');

    /**
     * La classe DateFrenchTest ...
     *
     * @package app.Test.Case.Utility
     */
    class DateFrenchTest extends CakeTestCase {

        /**
         * Test de la méthode DateFrench::frenchDateConvocation()
         */
        public function testFrenchDateConvocation() {
            // Avec un timestamp en paramètre
            $result = DateFrench::frenchDateConvocation(1377178200);
            $expected = 'Jeudi 22 août 2013 à 15 h 30';
            $this->assertEquals($result, $expected, var_export($result, true));

            // Avec une date "SQL" en paramètre
            $result = DateFrench::frenchDateConvocation('2013-08-22 15:30:00');
            $expected = 'Jeudi 22 août 2013 à 15 h 30';
            $this->assertEquals($result, $expected, var_export($result, true));
        }

        /**
         * Test de la méthode DateFrench::frenchDate()
         */
        public function testFrenchDate() {
            // Avec un timestamp en paramètre
            $result = DateFrench::frenchDate(1377178200);
            $expected = 'Jeudi 22 août 2013';
            $this->assertEquals($result, $expected, var_export($result, true));

            // Avec une date "SQL" en paramètre
            $result = DateFrench::frenchDate('2013-08-22 15:30:00');
            $expected = 'Jeudi 22 août 2013';
            $this->assertEquals($result, $expected, var_export($result, true));
        }

        /**
         * Test de la méthode DateFrench::frDate()
         */
        public function testFrDate() {
            $result = DateFrench::frDate('2013-08-22');
            $expected = '22/08/2013';
            $this->assertEquals($result, $expected, var_export($result, true));
        }

        /**
         * Test de la méthode DateFrench::hour()
         */
        public function testHour() {
            $result = DateFrench::hour('2013-08-22 15:30:00');
            $expected = '15:30';
            $this->assertEquals($result, $expected, var_export($result, true));

            $result = DateFrench::hour('2013-08-22 15:30:00', 'hh');
            $expected = '15';
            $this->assertEquals($result, $expected, var_export($result, true));

            $result = DateFrench::hour('2013-08-22 15:30:00', 'mm');
            $expected = '30';
            $this->assertEquals($result, $expected, var_export($result, true));

            $result = DateFrench::hour(null);
            $expected = null;
            $this->assertEquals($result, $expected, var_export($result, true));
        }

        /**
         * Test de la méthode DateFrench::frDate()
         *
         * @todo faire les tests pour tous les jours d'août (1-31) et pour toutes
         * les années entre 2004 et 2015, ensuite refactoriser le code existant
         * pour utiliser des tableaux de correspondances.
         */
        public function testDateLettres() {
            $tests = array(
                // Test des jours
                '2013-01-01' => 'L\'an deux mille treize le un janvier ',
                '2013-01-02' => 'L\'an deux mille treize le deux janvier ',
                '2013-01-11' => 'L\'an deux mille treize le onze janvier ',
                '2013-01-31' => 'L\'an deux mille treize le trente et un janvier ',
                // Test des mois
                '2013-02-22' => 'L\'an deux mille treize le vingt deux février ',
                '2013-10-22' => 'L\'an deux mille treize le vingt deux octobre ',
                '2013-12-22' => 'L\'an deux mille treize le vingt deux décembre ',
                // Test des années
                '2004-08-22' => 'L\'an deux mille quatre le vingt deux août ',
                '2013-08-22' => 'L\'an deux mille treize le vingt deux août ',
                '2015-08-22' => 'L\'an deux mille quinze le vingt deux août ',
            );
            foreach ($tests as $parameter => $expected) {
                // Avec un timestamp en paramètre
                $result = DateFrench::dateLettres(strtotime($parameter));
                $expected = $expected;
                $this->assertEquals($result, $expected, var_export($result, true));

                // Avec une date "SQL" en paramètre
                $result = DateFrench::dateLettres($parameter);
                $expected = $expected;
                $this->assertEquals($result, $expected, var_export($result, true));
            }
        }

    }

?>