<h2>Avancer le projet à une étape suivante</h2>

<?php
	echo $this->Form->create('Traitement', array('url'=>array('controller'=>'deliberations', 'action'=>'goNext', $delib_id)));
		echo $this->Form->input('etape', array('label'=>'Destinataire', 'title'=>"A qui voulez vous envoyer le projet ? : "));
?>
<br> <br> <br> 
<?php
	echo '<div class="submit">';
		echo $this->Form->submit('Valider', array('div'=>false, 'class'=>'bt_add', 'name'=>'Valider'));
		echo $this->Html->link('Annuler', array('action'=>'tousLesProjetsValidation'), array('class'=>'link_annuler', 'name'=>'Annuler'));
	echo '</div>';
echo $this->Form->end();
?>
