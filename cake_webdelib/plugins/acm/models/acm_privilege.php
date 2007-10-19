<?php
class AcmPrivilege extends AcmAppModel
{
	var $name = 'AcmPrivilege';
	var $useTable = false;

	var $hasAndBelongsToMany = array('AcmUser','AcmRole');


}
?>