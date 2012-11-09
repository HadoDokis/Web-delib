<div id="content">
<?php
	$userId = $this->Session->read('user.User.id');
	if ($Droits->check($userId, 'Deliberations:tousLesProjetsSansSeance')) {
        echo $this->requestAction('/deliberations/tousLesProjetsSansSeance', array('return'));
        echo('<br/>');
     }
?>
</div>
