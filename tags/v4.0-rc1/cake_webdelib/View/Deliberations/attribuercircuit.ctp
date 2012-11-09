<?php echo $this->Html->script('fonctions'); ?>
<h2>Attribuer le projet &agrave; un circuit</h2>
<?php echo $this->Form->create('Deliberation',array('type'=>'post','url'=>'/deliberations/attribuercircuit/'.$this->Html->value('Deliberation.id').'/'.$circuit_id)); ?>

<?php
	$loc=$this->Html->url("/deliberations/attribuercircuit/".$this->Html->value('Deliberation.id')."/");
	echo $this->Form->input('Deliberation.circuit_id', array('options'=>$circuits, "onChange"=>"lister_circuits(this, '$loc');", 'empty'=>true, 'selected'=>$circuit_id));
	echo '<br />';
 // données concernant le circuit selectionné
    if (isset($visu)) 
        echo ($visu); 
?>
<div class="spacer"></div>
<div class="submit">
	<?php echo $this->Form->submit('Attribuer', array('div'=>false, 'class'=>'bt_add', 'name'=>'ajouter'));?>
	<?php echo $this->Html->link('Annuler', $lien_retour, array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>
<?php $this->Form->end(); ?>
