<?php
	if( !defined( 'CAKEPHP_UNIT_TEST_EXECUTION' ) ) {
		define( 'CAKEPHP_UNIT_TEST_EXECUTION', 1 );
	}

	require_once( dirname( __FILE__ ).'/cake_app_test_case.php' );

	class CakeAppHelperTestCase extends CakeAppTestCase
	{
		/**
		* Recursively creates Helper and it's helper classes
		*/
		protected function _recursiveInit( $helperName ) {
			$helperClass = "{$helperName}Helper";

			$return = new $helperClass();
			$helpers = $return->helpers;

			if( !empty( $helpers ) ) {
				foreach( $helpers as $helperName ) {
					$return->{$helperName} = $this->_recursiveInit( $helperName );
				}
			}

			return $return;
		}

		/**
		* Here we instantiate our helper, all other helpers we need,
		* and a View class.
		*/
		public function startTest() {
			$helperName = preg_replace( '/TestCase$/', '', get_class( $this ) );
			$this->{$helperName} = $this->_recursiveInit( $helperName );
			/*$helperClass = "{$helperName}Helper";

			$this->{$helperName} = new $helperClass();
			$helpers = $this->{$helperName}->helpers;

			if( !empty( $helpers ) ) {
				foreach( $helpers as $helperName ) {
debug( $helperName );
					$this->{$helperName} = $this->_recursiveInit( $helperName );
				}
			}*/

			ClassRegistry::init( 'View' );
			/*$testedClass = preg_replace( '/Test$/', '', get_class( $this ) );
			//$this->{$testedClass} = $this->freu( $testedClass );
			$helperClass = "{$testedClass}Helper";

			$name=strtolower($testedClass);

			$this->{$testedClass} = new $helperClass();

			//ClassRegistry::init( 'View' );*/
		}

		/**
		* tearDown method
		*
		* @access public
		* @return void
		*/
		function tearDown() {
			$testedClass = preg_replace( '/TestCase$/', '', get_class( $this ) );
			unset( $this->{$testedClass} );

			ClassRegistry::flush();
		}
	}
?>