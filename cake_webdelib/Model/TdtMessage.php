<?php
class TdtMessage extends AppModel {

	var $name = 'TdtMessage';
	var $useTable="tdt_messages";
	var $belongsTo = array('Deliberation' =>
				array('className' => 'Deliberation',
						'foreignKey' => 'delib_id',
						'conditions' => '',
						'fields' => '',
						'order' => '',
						'counterCache' => ''
				)

	);
}
?>
