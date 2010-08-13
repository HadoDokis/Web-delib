<script>
    document.getElementById("pourcentage").style.display='none';
    document.getElementById("progrbar").style.display='none';
    document.getElementById("affiche").style.display='none';
    document.getElementById("contTemp").style.display='none';
</script>

<?php echo $javascript->link('utils.js'); ?>
<div class="deliberations">

<?php if (isset($message))  echo ($message); ?>
<h2>T&eacute;l&eacute;transmission des d&eacute;lib&eacute;rations</h2>>
<?php echo $form->create('Deliberation',array('type'=>'file','url'=>'/deliberations/sendActe')); ?>
    La Classification enregistrée date du <?php echo $html->link($dateClassification,'/deliberations/getClassification/', array('title'=>'Date classification'))?><br /><br />
	<table style='width:auto;'>
	<th></th>
 	<th>Numéro Généré</th>
 	<th>Objet</th>
 	<th>Titre</th>
 	<th>Classification</th>
 	<th>statut</th>
<tr>
<?php
	foreach ($deliberations as $delib) {
		if ($delib['Deliberation']['etat']!= 5)
			echo("<td>".$form->checkbox('Deliberation.id_'.$delib['Deliberation']['id'])."</td>");
		else
		    echo("<td></td>");

                echo "<td>".$html->link($delib['Deliberation']['num_delib'], '/deliberations/downloadDelib/'.$delib['Deliberation']['id']);
		?>
		</td>
		<td><?php echo $delib['Deliberation']['objet']; ?></td>
		<td><?php echo $delib['Deliberation']['titre']; ?></td>

		<td><?php echo $form->input('Deliberation.'.$delib['Deliberation']['id'].'_num_pref',array('label'=>false, 'div'=>false, 'id'=>$delib['Deliberation']['id'].'classif1', 'size' => '60','disabled'=>'disabled', 'value' => $delib['Deliberation'][$delib['Deliberation']['id'].'_num_pref'] ));?><br/>
		<a class="list_form" href="#add" onclick="javascript:window.open('<?php echo $this->base;?>/deliberations/classification?id=<?php echo $delib['Deliberation']['id'];?>', 'Classification', 'scrollbars=yes,,width=570,height=450');" id="<?php echo $delib['Deliberation']['id']; ?> _classification_text">[Choisir la classification]</a>
		 <?php 
		         echo $form->hidden('Deliberation.'.$delib['Deliberation']['id'].'_num_pref',array('id'=>$delib['Deliberation']['id'].'classif2','name'=>$delib['Deliberation']['id'].'classif2')); 
                 ?>
		 </td>
		   <?php
		        if ($delib['Deliberation']['etat']== 5) {
			   $tdt_id = $delib['Deliberation']['tdt_id'];
			   echo  ("<td><a href='https://$host/modules/actes/actes_transac_get_status.php?transaction=$tdt_id'>envoye</a></td>");
			}
		        else
 		            echo("<td>non envoyé</td>");
		   ?>
		</tr>
<?php	 } ?>

	</table>
	<br />

	<div class="submit">
		<?php echo $form->submit('Envoyer',array('div'=>false));?>
	</div>

<?php echo $form->end(); ?>
</div>
