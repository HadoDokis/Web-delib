<script type="text/javascript">
$(document).ready(function() {
        $("div.ouvrable").ouvrable({
                arrowUp : '<?php echo $this->Html->webroot('img/icons/arrow-right.png');?>',
                arrowDown : '<?php echo $this->Html->webroot('img/icons/arrow-down.png');?>',
                });
});
</script>

<?php echo $this->Html->script('ouvrable', true); ?>
<h1>Mon tableau de bord</h1>
<div id="content">
    <div id="tableau_bord">
        <?php

          	$user_id = $this->Session->read('user.User.id');
         
            if ($Droit->check($user_id, 'Deliberations:mesProjetsATraiter')) {
            echo $this->requestAction('/deliberations/mesProjetsATraiter', array('return', 'filtre'=>'hide'));
                echo $this->Html->link('Voir le contenu de la banette', '/deliberations/mesProjetsATraiter');
                echo('<br/><br/>');
            }
            if ($Droit->check($user_id, 'Deliberations:mesProjetsValidation')) {
       		    echo $this->requestAction('/deliberations/mesProjetsValidation', array('return', 'filtre'=>'hide'));
                echo $this->Html->link('Voir le contenu de la banette', '/deliberations/mesProjetsValidation');
                echo('<br/><br/>');
            }
            if ($Droit->check($user_id, 'Deliberations:mesProjetsRedaction')) {
                echo $this->requestAction('/deliberations/mesProjetsRedaction', array('return', 'filtre'=>'hide'));
                echo $this->Html->link('Voir le contenu de la banette', '/deliberations/mesProjetsRedaction');
		echo('<br/>');
	    }
	?>
        <div class="spacer"> </div>
        <?php 
            if ($Droit->check($user_id, 'Seances:listerFuturesSeances'))
                echo $this->requestAction('/seances/listerFuturesSeances', array('return', 'filtre'=>'hide'));
        ?>
    </div>
</div>
