<?php
	if($html->value('Sequence.id')) {
		echo "<h2>Modification d'une s&eacute;quence</h2>";
		echo $form->create('Sequence', array('url' => '/sequences/edit/'.$html->value('Sequence.id'),'type'=>'post'));
	}
	else {
		echo "<h2>Ajout d'une s&eacute;quence</h2>";
		echo $form->create('Sequence', array('url' => '/sequences/add/','type'=>'post'));
	}
?>

<div class="required">
 	<?php echo $form->input('Sequence.nom', array('label'=>'Nom <acronym title="obligatoire">(*)</acronym>','size' => '60'));?>
</div>
<br/>
<div class="required">
 	<?php echo $form->input('Sequence.commentaire', array('label'=>'Commentaire','size' => '100'));?>
</div>
<br/>
<div class="required">
 	<?php
 		if (Configure::read('INIT_SEQ'))
 			echo $form->input('Sequence.num_sequence', array('label'=>'Num&eacute;ro de s&eacute;quence','size' => '10', 'value'=>1));
 		else
 			echo $form->input('Sequence.num_sequence', array('label'=>'Num&eacute;ro de s&eacute;quence','size' => '10', 'readonly'=> 'readonly', 'value'=>1));
 	?>
</div>
<br/>

<br/><br/><br/><br/><br/>
<div class="submit">
	<?php if ($this->action=='edit') echo $form->hidden('Sequence.id')?>
	<?php echo $form->submit('Sauvegarder', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Sauvegarder'));?>
	<?php echo $html->link('Annuler', '/sequences/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>

<?php echo $form->end(); ?>
