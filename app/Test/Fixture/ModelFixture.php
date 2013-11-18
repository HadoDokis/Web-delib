<?php

class ModelFixture extends CakeTestFixture {
        var $name = 'Model';
        var $table = 'models';
        var $import = array( 'table' => 'models', 'connection' => 'default', 'records' => true);
        var $records = array(
                array(
                        'id' => '1',
                        'modele' => 'Modéle défaut',
                        'type' => 'Document',
                        'name' => 'projet.odt',
                        'size' => '7908',
                        'extension' => 'application/vnd.oasis.opendocument.text',
                        'content' => 'aa',
                ),
        );
}
?>