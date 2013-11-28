<?php echo $this->Html->script('calendrier.js'); ?>
<?php echo $this->Html->script('utils.js'); ?>
<h2>R&eacute;capitulatif du projet de d&eacute;liberation</h2>
<br/>

<div id="add_form">
<table class="sample">
	<tr>
		<td width="20%"><?php echo $this->Form->label('Deliberation.objet', 'Libellé');?></td>
		<td width="60%"><?php echo $deliberation['Deliberation']['objet'];?></td>
	</tr>
	<tr>
		<td><?php echo $this->Form->label('Deliberation.titre', 'Titre');?></td>
 		<td><?php echo $deliberation['Deliberation']['titre'];?></td>
	</tr>
	<tr>
		<td><?php echo $this->Form->label('Deliberation.seance_id','Date séance'); ?></td>
		<td>
                <?php
                    if(isset($deliberation['Seance'][0])){
                        foreach( $deliberation['Seance'] as  $seance) {
                            echo($seance['Typeseance']['libelle']." : ");
                            echo($this->Html2->ukToFrenchDateWithHour($seance['date']).'<br>');
                        }
                    }
                ?>
                </td>
	</tr>
	<tr>
		<td><?php echo $this->Form->label('Deliberation.rapporteur_id', 'Rapporteur');?></td>
		<td><?php echo $deliberation['Rapporteur']['nom'];?></td>
	</tr>
	<tr>
		<td><?php echo $this->Form->label('Deliberation.redacteur_id', 'Rédacteur');?></td>
		<td><?php echo $deliberation['Redacteur']['nom'];?></td>
	</tr>
	<tr>
		<td><?php echo $this->Form->label('Deliberation.theme_id', 'Thème');?></td>
		<td><?php echo $deliberation['Theme']['libelle'];?></td>
	</tr>
	<tr>
		<td><?php echo $this->Form->label('Deliberation.service_id', 'Service émetteur');?></td>
		<td><?php echo $deliberation['Service']['libelle'];?></td>
	</tr>
	<tr>
		<td><?php echo $this->Form->label('Deliberation.num_delib', 'Num Delib');?></td>
		<td><?php echo $deliberation['Deliberation']['num_delib'];?></td>
	</tr>
	<tr>
		<td><?php echo $this->Form->label('Deliberation.num_pref', 'Num Pref');?></td>
		<td><?php echo $deliberation['Deliberation']['num_pref'];?></td>
	</tr>
	<tr>
		<td><?php echo $this->Form->label('Deliberation.date_limite', 'Date limite');?></td>
		<td><?php echo $deliberation['Deliberation']['date_limite'];?></td>
	</tr>
	<?php if(!empty($annexes)){  ?>
	<tr>
		<td><?php echo $this->Form->label('Annexe.titre', 'Annexe(s)');?></td>
		<td><?php 
foreach ($annexes as $annexe) :
    echo 'Titre : '.$annexe['Annex']['titre'];
    echo '<br>Nom fichier : '.$annexe['Annex']['filename'];
    echo '<br>Taille : '.$annexe['Annex']['size'].' '.$this->Html->link('Telecharger','/annexes/download/'.$annexe['Annex']['id']);
    echo '<br><br>';
endforeach; 
                ?></td>
	</tr>
<?php } ?>
	<tr>
		<td><?php echo $this->Form->label('Annexe.titre', 'Texte ');?></td>
		<td id="actions_fiche">	<li><?php echo $this->Html->link(SHY,'/deliberations/textprojetvue/' . $deliberation['Deliberation']['id'], array('class'=>'link_projet', 'escape'=>false, 'title'=>'Projet'), false)?></li>
								<li><?php echo $this->Html->link(SHY,'/deliberations/textsynthesevue/' . $deliberation['Deliberation']['id'], array('class'=>'link_synthese', 'escape'=>false, 'title'=>'Synthese'), false)?></li>
								<li><?php echo $this->Html->link(SHY,'/deliberations/deliberationvue/' . $deliberation['Deliberation']['id'], array('class'=>'link_deliberation', 'escape'=>false, 'title'=>'Acte'), false)?></li>
		</td>
	</tr>
	<tr>
		<td><?php echo $this->Form->label('Deliberation.created', 'Date cr&eacute;ation');?></td>
		<td><?php echo $deliberation['Deliberation']['created'];?></td>
	<tr>
		<td><?php echo $this->Form->label('Deliberation.modified', 'Date modification');?></td>
		<td><?php echo $deliberation['Deliberation']['modified'];?></td>
	</tr>
	<tr>
		<td><?php echo $this->Form->label('Deliberation.circuit_id', 'Circuit');?></td>
		<td width='100%'><?php echo $deliberation['Circuit']['libelle'];?><br/>
                   <?php echo $visu; ?> 
                    <?php /*foreach ($user_circuit as $user) { ?>
			<?php
			if ($deliberation['positionDelib'] == $user['UsersCircuit']['position'] && ($deliberation['Deliberation']['etat']!= 2)){
				echo $this->Html->image('/img/icons/edit.png');
			} elseif ($deliberation['positionDelib'] < $user['UsersCircuit']['position'] && ($deliberation['Deliberation']['etat']!= 2)) {
				echo $this->Html->image('/img/icons/editcreate.png');
			} else {
				echo $this->Html->image('/img/icons/editcreate.png');
			} ?>
			<br/><?php echo $user['User']['prenom']. ' ' .$user['User']['nom']; ?>
			<br/><?php echo $user['Service']['libelle'];?>
			<?php } */ ?>
		</td>
	</tr>
</table>
<br/>
<div class="centre">
<!--	<?php // echo $this->Html->link('Modifier', '/deliberations/edit/'.$deliberation['Deliberation']['id'], array('class'=>'link_add', 'title'=>'Modifier le projet de délibération')); ?><br/><br/>-->
		<?php // echo $this->Html->link('Insérer le projet de délibération dans le circuit', '/deliberations/addIntoCircuit/'.$deliberation['Deliberation']['id'], array('class'=>'link_add', 'title'=>'Insérer la délibération dans le circuit')); ?>
<?php echo $this->Html->link('<i class="fa fa-edit"></i> Modifier', '/deliberations/edit/'.$deliberation['Deliberation']['id'], array('escape'=> false, 'class'=>'btn', 'title'=>'Modifier le projet de délibération')); ?><br/><br/>
	<?php echo $this->Html->link('<i class="fa fa-cogs"></i> Insérer le projet de délibération dans le circuit', '/deliberations/addIntoCircuit/'.$deliberation['Deliberation']['id'], array('escape'=> false, 'class'=>'btn btn-primary', 'title'=>'Insérer la délibération dans le circuit')); ?>
</div>
</div>
