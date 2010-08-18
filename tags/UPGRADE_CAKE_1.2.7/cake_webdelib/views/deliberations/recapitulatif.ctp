<?php echo $javascript->link('calendrier.js'); ?>
<?php echo $javascript->link('utils.js'); ?>
<?php $deliberation=$deliberation[0];?>
<h2>R&eacute;capitulatif du projet de d&eacute;liberation</h2>
<br/>

<div id="add_form">
<table class="sample">
	<tr>
		<td width="20%"><?php echo $form->label('Deliberation.objet', 'Libellé');?></td>
		<td width="60%"><?php echo $deliberation['Deliberation']['objet'];?></td>
	</tr>
	<tr>
		<td><?php echo $form->label('Deliberation.titre', 'Titre');?></td>
 		<td><?php echo $deliberation['Deliberation']['titre'];?></td>
	</tr>
	<tr>
		<td><?php echo $form->label('Deliberation.seance_id','Date séance'); ?></td>
		<td><?php echo $deliberation['Seance']['date'];?></td>
	</tr>
	<tr>
		<td><?php echo $form->label('Deliberation.rapporteur_id', 'Rapporteur');?></td>
		<td><?php echo $deliberation['Rapporteur']['nom'];?></td>
	</tr>
	<tr>
		<td><?php echo $form->label('Deliberation.redacteur_id', 'Rédacteur');?></td>
		<td><?php echo $deliberation['Redacteur']['nom'];?></td>
	</tr>
	<tr>
		<td><?php echo $form->label('Deliberation.theme_id', 'Thème');?></td>
		<td><?php echo $deliberation['Theme']['libelle'];?></td>
	</tr>
	<tr>
		<td><?php echo $form->label('Deliberation.service_id', 'Service émetteur');?></td>
		<td><?php echo $deliberation['Service']['libelle'];?></td>
	</tr>
	<tr>
		<td><?php echo $form->label('Deliberation.num_delib', 'Num Delib');?></td>
		<td><?php echo $deliberation['Deliberation']['num_delib'];?></td>
	</tr>
	<tr>
		<td><?php echo $form->label('Deliberation.num_pref', 'Num Pref');?></td>
		<td><?php echo $deliberation['Deliberation']['num_pref'];?></td>
	</tr>
	<tr>
		<td><?php echo $form->label('Deliberation.date_limite', 'Date limite');?></td>
		<td><?php echo $deliberation['Deliberation']['date_limite'];?></td>
	</tr>
	<?php if(!empty($annexes)){  ?>
	<tr>
		<td><?php echo $form->label('Annexe.titre', 'Annexe(s)');?></td>
		<td><?php foreach ($annexes as $annexe) :
				echo 'Titre : '.$annexe['Annex']['titre'];
				echo '<br>Nom fichier : '.$annexe['Annex']['filename'];
				echo '<br>Taille : '.$annexe['Annex']['size'].' '.$html->link('Telecharger','/annexes/download/'.$annexe['Annex']['id']);?><br/><br/>
				<?php endforeach; } ?></td>
	</tr>
	<tr>
		<td><?php echo $form->label('Annexe.titre', 'Texte ');?></td>
		<td id="actions_fiche">	<li><?php echo $html->link(SHY,'/deliberations/textprojetvue/' . $deliberation['Deliberation']['id'], array('class'=>'link_projet', 'title'=>'Projet'), false, false)?></li>
								<li><?php echo $html->link(SHY,'/deliberations/textsynthesevue/' . $deliberation['Deliberation']['id'], array('class'=>'link_synthese', 'title'=>'Synthese'), false, false)?></li>
								<li><?php echo $html->link(SHY,'/deliberations/deliberationvue/' . $deliberation['Deliberation']['id'], array('class'=>'link_deliberation', 'title'=>'Deliberation'), false, false)?></li>
		</td>
	</tr>
	<tr>
		<td><?php echo $form->label('Deliberation.created', 'Date cr&eacute;ation');?></td>
		<td><?php echo $deliberation['Deliberation']['created'];?></td>
	<tr>
		<td><?php echo $form->label('Deliberation.modified', 'Date modification');?></td>
		<td><?php echo $deliberation['Deliberation']['modified'];?></td>
	</tr>
	<tr>
		<td><?php echo $form->label('Deliberation.circuit_id', 'Circuit');?></td>
		<td><?php echo $deliberation['Circuit']['libelle'];?><br/><?php foreach ($user_circuit as $user) { ?>
			<div class="graphique">
			<?php
			if ($deliberation['positionDelib'] == $user['UsersCircuit']['position'] && ($deliberation['Deliberation']['etat']!= 2)){
				echo $html->image('/img/icons/edit.png');
			} elseif ($deliberation['positionDelib'] < $user['UsersCircuit']['position'] && ($deliberation['Deliberation']['etat']!= 2)) {
				echo $html->image('/img/icons/editcreate.png');
			} else {
				echo $html->image('/img/icons/editcreate.png');
			} ?>
			<br/><?php echo $user['User']['prenom']. ' ' .$user['User']['nom']; ?>
			<br/><?php echo $user['Service']['libelle'];?>
			</div>
			<?php } ?>
		</td>
	</tr>
</table>
<br/>
<div class="centre">
	<?php echo $html->link('Modifier', '/deliberations/edit/'.$deliberation['Deliberation']['id'], array('class'=>'link_add', 'title'=>'Modifier le projet de délibération')); ?><br/><br/>
	<?php echo $html->link('Insérer le projet de délibération dans le circuit', '/deliberations/addIntoCircuit/'.$deliberation['Deliberation']['id'], array('class'=>'link_add', 'title'=>'Insérer la délibération dans le circuit')); ?>
</div>
</div>
