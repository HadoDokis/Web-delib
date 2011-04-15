<?php

class ActeursServiceFixture extends CakeTestFixture {
 var $name = 'ActeursService';
 var $table = 'acteurs_services';
 var $import = array( 'table' => 'acteurs_services', 'connection' => 'default', 'records' => false);
 var $records = array(
 array(
 'acteur_id' => '1',
 'service_id' => '1',
 ),
 array(
 'acteur_id' => '2',
 'service_id' => '1',
 ),
 );
}

?>
