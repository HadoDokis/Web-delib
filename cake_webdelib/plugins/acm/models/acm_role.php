<?php
class AcmRole extends AcmAppModel
{
	var $name = 'AcmRole';
	var $useTable = false;

	var $hasAndBelongsToMany = array('AcmUser','AcmPrivilege');

}
?>