<h2>Renvoyer le projet à une étape précédente</h2>

<?php
//	echo $this->Form->create('Deliberation',array('url'=>'/deliberations/retour/'.$delib_id,'type'=>'post'));
	echo $this->Form->create('Traitement', array('url'=>array('controller'=>'deliberations', 'action'=>'retour', $delib_id)));
		echo $this->Form->input('etape', array('label'=>'Destinataire', 'title'=>"A qui voulez vous envoyer le projet ? : "));
?>
<br> <br> <br> 
<?php
	echo '<div class="submit">';
		echo $this->Form->submit('Valider', array('div'=>false, 'class'=>'bt_add', 'name'=>'Valider'));
		echo $this->Html->link('Annuler', array('action'=>'traiter', $delib_id), array('class'=>'link_annuler', 'name'=>'Annuler'));
	echo '</div>';
echo $this->Form->end();
?>
