<div id="content">
<?php
	$userId = $this->Session->read('user.User.id');
	if ($Droits->check($userId, 'Deliberations:projetsMonService')) {
        echo $this->requestAction('/deliberations/projetsMonService', array('return'));
        echo('<br/>');
     }
?>
</div>
