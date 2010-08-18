<?php echo $javascript->link('fonctions'); ?>
<h2>Attribuer le projet &agrave; un circuit</h2>
<?php echo $form->create('Deliberation',array('type'=>'post','url'=>'/deliberations/attribuercircuit/'.$html->value('Deliberation.id').'/'.$circuit_id)); ?>

<?php
	$loc=$html->url("/deliberations/attribuercircuit/".$html->value('Deliberation.id')."/");
	echo $form->input('Deliberation.circuit_id', array('options'=>$circuits, "onChange"=>"lister_circuits(this, '$loc');", 'empty'=>true, 'default'=>$circuit_id));

 // données concernant le circuit selectionné
    if (isset($visu)) 
        echo ($visu); 
?>
<br />
<div class="submit">
	<?php echo $form->submit('Attribuer', array('div'=>false, 'class'=>'bt_add', 'name'=>'ajouter'));?>
</div>
<?php $form->end(); ?>
