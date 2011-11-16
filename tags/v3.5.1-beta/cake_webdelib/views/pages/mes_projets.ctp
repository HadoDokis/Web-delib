<div id="content">
<?php
    $userId = $session->read('user.User.id');
    if ($Xacl->check($userId, 'Deliberations:mesProjetsRedaction')) {
        echo $this->requestAction('/deliberations/mesProjetsRedaction', array('return'));
        echo('<br/>');
    }
?>
</div>
