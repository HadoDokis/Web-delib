<div id="content">
<?php
	$userId = $session->read('user.User.id');
	if ($Xacl->check($userId, 'Deliberations:tousLesProjetsSansSeance')) {
        echo $this->requestAction('/deliberations/tousLesProjetsSansSeance', array('return'));
        echo('<br/>');
     }
?>
</div>
