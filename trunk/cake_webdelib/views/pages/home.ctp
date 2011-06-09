<div id="content">
    <div id="tableau_bord">
        <?php
            $userId = $session->read('user.User.id');
            if ($Xacl->check($userId, 'Deliberations:mesProjetsATraiter')) {
                echo $this->requestAction('/deliberations/mesProjetsATraiter', array('return', 'filtre'=>'hide'));
                echo $html->link('Voir le contenu de la banette', '/deliberations/mesProjetsATraiter');
                echo('<br/><br/>');
            }
            if ($Xacl->check($userId, 'Deliberations:mesProjetsValidation')) {
                echo $this->requestAction('/deliberations/mesProjetsValidation', array('return', 'filtre'=>'hide'));
                echo $html->link('Voir le contenu de la banette', '/deliberations/mesProjetsValidation');
                echo('<br/><br/>');
            }
            if ($Xacl->check($userId, 'Deliberations:mesProjetsRedaction')) {
                echo $this->requestAction('/deliberations/mesProjetsRedaction', array('return', 'filtre'=>'hide'));
                echo('<br/>');
            }
            if ($Xacl->check($userId, 'Seances:listerFuturesSeances'))
                echo $this->requestAction('/seances/listerFuturesSeances', array('return', 'filtre'=>'hide'));
        ?>
    </div>
</div>
