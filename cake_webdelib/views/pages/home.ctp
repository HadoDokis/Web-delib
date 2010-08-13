<div id="content">
	<div id="tableau_bord">
		<?php
			$userId = $session->read('user.User.id');
			if ($Xacl->check($userId, 'Deliberations:mesProjetsATraiter')) {
				echo $this->requestAction('/deliberations/mesProjetsATraiter', array('return'));
				echo('<br/>');
			}
			if ($Xacl->check($userId, 'Deliberations:mesProjetsValidation')) {
				echo $this->requestAction('/deliberations/mesProjetsValidation', array('return'));
				echo('<br/>');
			}
			if ($Xacl->check($userId, 'Deliberations:mesProjetsRedaction')) {
				echo $this->requestAction('/deliberations/mesProjetsRedaction', array('return'));
				echo('<br/>');
			}
			if ($Xacl->check($userId, 'Seances:listerFuturesSeances'))
				 echo $this->requestAction('/seances/listerFuturesSeances', array('return'));
		?>
	</div>
</div>
