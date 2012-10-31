<div class="seances">
<h2>Liste des présents</h2>

<form action="<?php echo $html->url('/seances/listerPresents/'.$seance_id);?>" method="post">
<table cellpadding="0" cellspacing="0">
<tr>
	<th>Elu</th>
	<th>Présent</th>
	<th>Mandataire</th>
</tr>
<?php foreach ($presents as $present): 
$pres = $present['User']['id']; ?>
<tr>
	<td><?php echo $present['User']['prenom'].' '.$present['User']['nom']; ?></td>
	<td>
 		<?php echo $form->input($present['User']['id'].'.present', array('type'=>'radio', 'options'=>array(1=>'oui'),'onclick'=>"javascript:disable('liste_$pres','1');"));?>
		<?php echo $form->input($present['User']['id'].'.present', array('type'=>'radio', 'options'=>array(0=>'non'), 'selected'=>'selected', 'onclick'=>"javascript:disable('liste_$pres','2');")); ?>
	</td>
	<td>
 	    <?php echo $form->input($present['User']['id'].'.mandataire', array('label'=>'Mandataire', 'options'=>$mandataires, "id"=>"liste_".$present['User']['id'],'empty'=>true));?>  
    </td>	
</tr>
<?php endforeach; ?>
</table>
<br />
<div class="submit">
	<?php echo $form->submit('Enregistrer la liste des présents', array('div'=>false,'class'=>'bt_add', 'name'=>'modifier'));?>
</div>
<br />
<?php echo $form->end(); ?>
