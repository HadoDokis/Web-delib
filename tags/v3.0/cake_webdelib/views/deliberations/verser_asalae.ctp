<?php echo $javascript->link('utils.js'); ?>
<div class="deliberations">
<?php if (isset($message))  echo ($message); ?>
<h2>Verser les D&eacute;lib&eacute;rations &agrave; AS@LAE</h2>
<?php echo $form->create('Deliberation',array('type'=>'file','url'=>'/deliberations/verserAsalae')); ?>
<table>
	<th></th>
 	<th>Numéro D&eacute;lib&eacute;ration</th>
 	<th>Objet</th>
 	<th>Titre</th>
 	<th>statut</th>
<tr>
<?php
	foreach ($deliberations as $delib) {
		if ($delib['Deliberation']['etat_asalae']==null)
		    echo("<td>".$form->checkbox('Deliberation.id_'.$delib['Deliberation']['id'])."</td>");
		else
		    echo("<td></td>");

                echo "<td>".$html->link($delib['Deliberation']['num_delib'], '/deliberations/downloadDelib/'.$delib['Deliberation']['id']);
		?>
		</td>
		<td>
		<?php echo ($delib['Deliberation']['objet']) ; ?>
		</td>
		<td>
		<?php echo ($delib['Deliberation']['titre']); ?>
		</td>

		   <?php
		    if ($delib['Deliberation']['etat_asalae']==1) {
			   echo("<td>Délibération archivée dans AS@LAE</td>");
			}
			else{
 		            echo("<td>&nbsp;</td>");
			}
		   ?>
		</tr>
<?php	 } ?>

	</table>
	<br />
	<div class="submit">
		<?php echo $form->submit('Envoyer',array('div'=>false));?>
	</div>

<?php $form->end(); ?>
</div>
