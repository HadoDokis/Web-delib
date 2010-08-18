<?php
	require_once( 'cake_app_test_case.php' );

	class CakeAppControllerTestCase extends CakeAppTestCase
	{

		function startTest() {
			Configure::write('Acl.database', 'test_suite');
			ClassRegistry::config( array( 'ds' => 'test_suite' ) );
			$name = preg_replace( '/Test$/', '', get_class( $this ) );
			$testname = 'Test'.$name;
			$this->{$name} =& new $testname();
			$this->{$name}->constructClasses();
			$this->{$name}->Component->initialize($this->{$name});
		}

		function tearDown() {
			$name = preg_replace( '/Test$/', '', get_class( $this ) );
			unset( $this->{$name} );
 			ClassRegistry::flush();
		}

	}
?>
