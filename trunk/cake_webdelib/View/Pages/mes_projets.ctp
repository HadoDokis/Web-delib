<div id="content">
<?php
    $userId = $this->Session->read('user.User.id');
    if ($Droits->check($userId, 'Deliberations:mesProjetsRedaction')) {
        echo $this->requestAction('/deliberations/mesProjetsRedaction', array('return'));
        echo('<br/>');
    }
?>
</div>
