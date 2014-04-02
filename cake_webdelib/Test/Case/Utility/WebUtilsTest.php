<?php
    /**
     * Code source de la classe WebUtilsTest.
     *
     * PHP 5.3
     *
     * @package app.Test.Case.Utility
     * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
     */
    App::uses('WebUtils', 'Utility');

    /**
     * La classe WebUtilsTest ...
     *
     * @package app.Test.Case.Utility
     */
    class WebUtilsTest extends CakeTestCase {

        /**
         * Test de la méthode WebUtils::sqlDateTime()
         */
        public function testSqlDateTime() {
            $result = WebUtils::sqlDateTime('2013-08-22');
            $expected = '22/08/2013 ';
            $this->assertEquals($result, $expected, var_export($result, true));
        }

        /**
         * Test de la méthode WebUtils::decToStringTime()
         */
        public function testDecToStringTime() {
            $result = WebUtils::decToStringTime( 10 );
            $expected = '10 h ';
            $this->assertEquals($result, $expected, var_export($result, true));
        }

        /**
         * Test de la méthode WebUtils::frDateToUkDate()
         */
        public function testFrDateToUkDate() {
            $result = WebUtils::frDateToUkDate('22/08/2013');
            $expected = '2013-08-22';
            $this->assertEquals($result, $expected, var_export($result, true));

            $result = WebUtils::frDateToUkDate('2013-08-23');
            $expected = '2013-08-23';
            $this->assertEquals($result, $expected, var_export($result, true));

            $result = WebUtils::frDateToUkDate(null);
            $expected = null;
            $this->assertEquals($result, $expected, var_export($result, true));
        }

        /**
         * Test de la méthode WebUtils::formattedTime()
         */
        public function testFormattedTime() {
            $result = WebUtils::formattedTime(0);
            $expected = 0;
            $this->assertEquals($result, $expected, var_export($result, true));

            $result = WebUtils::formattedTime(29);
            $expected = 0;
            $this->assertEquals($result, $expected, var_export($result, true));

            $result = WebUtils::formattedTime(3599);
            $expected = "60 min";
            $this->assertEquals($result, $expected, var_export($result, true));

            $result = WebUtils::formattedTime(strtotime('2013-08-22'));
            $expected = '382534 h ';
            $this->assertEquals($result, $expected, var_export($result, true));

            $result = WebUtils::formattedTime(-strtotime('2013-08-22'));
            $expected = '- 382534 h ';
            $this->assertEquals($result, $expected, var_export($result, true));
        }

        /**
         * Test de la méthode WebUtils::simplifyArray()
         */
        public function testSimplifyArray() {
            $array = array(
                array(
                    'id' => 6,
                    'libelle' => 'Mon premier groupe',
                ),
                array(
                    'id' => 11,
                    'libelle' => 'Mon second groupe',
                ),
            );
            $result = WebUtils::simplifyArray($array);
            $expected = array(
                6 => 'Mon premier groupe',
                11 => 'Mon second groupe',
            );
            $this->assertEquals($result, $expected, var_export($result, true));
        }

        /**
         * Test de la méthode WebUtils::strtocamel()
         */
        public function testStrtocamel() {
            $result = WebUtils::strtocamel('Procter & Gamble sont dans un bateau');
            $expected = 'procterEtGambleSontDansUnBateau';
            $this->assertEquals($result, $expected, var_export($result, true));
        }

        /**
         * Test de la méthode WebUtils::strSansAccent()
         */
        public function testStrSansAccent() {
            $result = WebUtils::strSansAccent('À la Clairefontaine, je m\'étais promenée.');
            $expected = 'A-la-Clairefontaine,-je-m\'etais-promenee.';
            $this->assertEquals($result, $expected, var_export($result, true));
        }

        /**
         * Test de la méthode WebUtils::listFromArray()
         */
        public function testListFromArray() {
            $elements = array(
                array(
                    'Seance' => array(
                        'id' => 6,
                        'date' => '2010-08-22',
                        'Typeseance' => array(
                            'libelle' => 'Mon type de séance'
                        )
                    )
                ),
                array(
                    'Seance' => array(
                        'id' => 32,
                        'date' => '2011-08-22',
                        'Typeseance' => array(
                            'libelle' => 'Mon autre type de séance'
                        )
                    )
                ),
            );

            $result = WebUtils::listFromArray(
                $elements,
                '/Seance/id',
                array(
                    '/Seance/date',
                    '/Seance/Typeseance/libelle'
                ),
                '%s : %s',
                'ASC'
            );
            $expected = array(
                32 => '2011-08-22 : Mon autre type de séance',
                6 => '2010-08-22 : Mon type de séance',
            );
            $this->assertEquals($result, $expected, var_export($result, true));

            $result = WebUtils::listFromArray(
                $elements,
                '/Seance/id',
                array(
                    '/Seance/date',
                    '/Seance/Typeseance/libelle'
                ),
                '%s : %s',
                'DESC'
            );
            $expected = array(
                6 => '2010-08-22 : Mon type de séance',
                32 => '2011-08-22 : Mon autre type de séance',
            );
            $this->assertEquals($result, $expected, var_export($result, true));

            $result = WebUtils::listFromArray(
                $elements,
                '/Seance/id',
                array(
                    '/Seance/date',
                    '/Seance/Typeseance/libelle'
                ),
                '%s : %s',
                'KEY_ASC'
            );
            $expected = array(
                6 => '2010-08-22 : Mon type de séance',
                32 => '2011-08-22 : Mon autre type de séance',
            );
            $this->assertEquals($result, $expected, var_export($result, true));

            $result = WebUtils::listFromArray(
                $elements,
                '/Seance/id',
                array(
                    '/Seance/date',
                    '/Seance/Typeseance/libelle'
                ),
                '%s : %s',
                'KEY_DESC'
            );
            $expected = array(
                32 => '2011-08-22 : Mon autre type de séance',
                6 => '2010-08-22 : Mon type de séance',
            );
            $this->assertEquals($result, $expected, var_export($result, true));
        }
    }

?>