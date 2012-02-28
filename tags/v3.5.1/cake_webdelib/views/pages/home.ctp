<script type="text/javascript">
$(document).ready(function() {
        $("div.ouvrable").ouvrable({
                arrowUp : '<?php echo $html->webroot('img/icons/arrow-right.png');?>',
                arrowDown : '<?php echo $html->webroot('img/icons/arrow-down.png');?>',
                });
});
</script>

<?php echo $javascript->link('ouvrable', true); ?>
<h1>Mon tableau de bord</h1>
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
                echo $html->link('Voir le contenu de la banette', '/deliberations/mesProjetsRedaction');
		echo('<br/>');
	    }
	?>
        <div class="spacer"> </div>
        <?php 
            if ($Xacl->check($userId, 'Seances:listerFuturesSeances'))
                echo $this->requestAction('/seances/listerFuturesSeances', array('return', 'filtre'=>'hide'));
        ?>
    </div>
</div>
