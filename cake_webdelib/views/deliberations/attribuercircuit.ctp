<?php echo $javascript->link('fonctions'); ?>
<h2>Attribuer le projet &agrave; un circuit</h2>
<?php echo $form->create('Deliberation',array('type'=>'post','url'=>'/deliberations/attribuercircuit/'.$html->value('Deliberation.id').'/'.$circuit_id)); ?>

<?php
	$loc=$html->url("/deliberations/attribuercircuit/".$html->value('Deliberation.id')."/");

	echo $form->input('Deliberation.circuit_id', array('options'=>$circuits, "onChange"=>"lister_circuits(this, '$loc');", 'empty'=>true, 'default'=>$circuit_id));

?>
<br /><br />
<!-- données concernant le circuit selectionné -->
<table>
	<tr>
		<th>service libellé</th>
		<th>prénom</th>
		<th>nom </th>
		<th>position</th>
	</tr>
	<tr>
	<?php 
    
	if (isset($listeUserCircuit)) {
		for ($i=0; $i<count($listeUserCircuit['id']); $i++){
	    	echo("<tr>");   
	        echo("<td>".$listeUserCircuit['service_libelle'][$i]."</td>");
	        echo("<td>".$listeUserCircuit['prenom'][$i]."</td>");
	        echo("<td>".$listeUserCircuit['nom'][$i]."</td>");
	        echo("<td>".$listeUserCircuit['position'][$i]."</td>");
	        echo("</tr>");
	    }
	}
	?>
</table>

	

<br />
<div class="submit">
	<?php echo $form->submit('Attribuer', array('div'=>false, 'class'=>'bt_add', 'name'=>'ajouter'));?>
</div>
<?php $form->end(); ?>
