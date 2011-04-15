<?php
	if( !defined( 'CAKEPHP_UNIT_TEST_EXECUTION' ) ) {
		define( 'CAKEPHP_UNIT_TEST_EXECUTION', 1 );
	}

	class CakeAppBehaviorTestCase extends CakeTestCase
	{
		/**
		* Here we instantiate our helper, all other helpers we need,
		* and a View class.
		*/
		public function startTest() {
            $this->Item =& new Item();
			$this->Item->useDbConfig = $this->useDbConfig;

			// Detach all behaviors
			$behaviors = array_values( $this->Item->Behaviors->attached() );
			foreach( $behaviors as $behavior ) {
				$this->Item->Behaviors->detach( $behavior );
			}
		}

		/**
		* Exécuté après chaque test.
		*/
        function tearDown() {
            ClassRegistry::flush();
            unset( $this->Item );
        }
	}
?>