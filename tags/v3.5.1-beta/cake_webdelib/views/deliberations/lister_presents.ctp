<?php echo $form->create('Deliberation',array('type'=>'post','url'=>'/deliberations/listerPresents/'.$delib_id)); ?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th>Elu</th>
	<th>Présent</th>
	<th>Mandataire</th>
</tr>
<?php foreach ($presents as $present):
     $pres = $present['Acteur']['id']; ?>
<tr>
	<td><?php echo $present['Acteur']['prenom'].' '.$present['Acteur']['nom']; ?></td>
	<td>
 		<?php echo $form->input($present['Acteur']['id'].'.present', array('label'=>false, 'fieldset'=>false, 'legend'=>false, 'div'=>false, 'type'=>'radio', 'options'=>array(1=>'oui'),'selected'=>'selected', 'onclick'=>"javascript:disable('liste_$pres','1');")); ?>
		<?php echo $form->input($present['Acteur']['id'].'.present', array('label'=>false, 'fieldset'=>false, 'legend'=>false, 'div'=>false, 'type'=>'radio', 'options'=>array(0=>'non'),'onclick'=>"javascript:disable('liste_$pres','2');")); ?>
	</td>
	<td>
 	   <?php
	   if (empty($present['Acteur']['id']))
	       echo $form->input($present['Acteur']['id'].'.mandataire', array('label'=>false, 'options'=>$mandataires, 'readonly'=>'readonly', "id"=>"liste_".$present['Acteur']['id'],'empty'=>true));
	   else 
	       echo $form->input($present['Acteur']['id'].'.mandataire', array('label'=>false, 'options'=>$mandataires, "id"=>"liste_".$present['Acteur']['id'], 'empty'=>true));
	   ?>
    </td>
</tr>
<?php endforeach; ?>
</table>
<br />
<div class="submit">
	<?php echo $form->submit('Enregistrer la liste des présents', array('div'=>false, 'class'=>'bt_add', 'name'=>'modifier'));?>
</div>
<br />
<?php echo $form->end(); ?>
