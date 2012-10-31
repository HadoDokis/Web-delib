<div id="content">
<?php
	$userId = $session->read('user.User.id');
	if ($Xacl->check($userId, 'Deliberations:projetsMonService')) {
        echo $this->requestAction('/deliberations/projetsMonService', array('return'));
        echo('<br/>');
     }
?>
</div>
