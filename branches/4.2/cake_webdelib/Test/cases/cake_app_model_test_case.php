<?php
	require_once( 'cake_app_test_case.php' );

	class CakeAppModelTestCase extends CakeAppTestCase
	{
		/**
		* Exécuté avant chaque test.
		*/
		public function startTest() {
			$name = preg_replace( '/TestCase$/', '', get_class( $this ) );
			ClassRegistry::config( array( 'ds' => 'test_suite' ) );
			$this->{$name} =& ClassRegistry::init( $name );
		}

		/**
		* Exécuté après chaque test.
		*/
		function tearDown() {
			$name = preg_replace( '/TestCase$/', '', get_class( $this ) );
			ClassRegistry::flush();
			unset( $this->{$name} );
		}
	}
?>
