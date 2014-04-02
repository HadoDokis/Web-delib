<?php
	/**
	 * Code source de la classe AppModelTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe AppModelTest ...
	 *
	 * @package app.Test.Case.Model
	 */
	class AppModelTest extends CakeTestCase
	{
    /**
         *
         * @var Model
         */
        public $Theme = null;

        /**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
            'app.Theme'
		);

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();
            $this->Theme = ClassRegistry::init( 'Theme' );
		}

		/**
		 * Nettoyage postérieur au test.
		 */
		public function tearDown() {
            unset( $this->Theme );
			parent::tearDown();
		}

		/**
		 * Test de la méthode AppModel::postgresFindParents()
		 */
		public function testPostgresFindParents() {
            $result = $this->Theme->postgresFindParents( 2 );
            $expected = array(
                array(
                    'Theme' => array(
                        'id' => 1,
                        'parent_id' => null,
                        'order' => '1',
                        'libelle' => 'Thème 1',
                        'actif' => true,
                        'created' => '2013-08-26 22:12:10',
                        'modified' => '2013-08-26 22:12:10',
                        'lft' => 1,
                        'rght' => 6
                    )
                ),
                array(
                    'Theme' => array(
                        'id' => 2,
                        'parent_id' => 1,
                        'order' => '2',
                        'libelle' => 'Thème 1.1',
                        'actif' => true,
                        'created' => '2013-08-26 22:12:10',
                        'modified' => '2013-08-26 22:12:10',
                        'lft' => 2,
                        'rght' => 3
                    )
                )
            );
			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}
	}
?>
