<div class="seances">

<h2><?php echo $titre; ?></h2>
<?php echo $this->element('filtre'); ?>

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<th>Ordre</th>
	<th>Nom</th>
	<th>Commentaire</th>
	<th>Code</th>
	<th>Type</th>
	<th>Recherche</th>
	<th>Active</th>
	<th width='160px'>Actions</th>
</tr>

<?php foreach ($this->data as $rownum=>$rowElement): ?>
<tr height='36px'>
	<td class="ordre">
		<?php echo $this->Html->link('&#9650;', '/infosupdefs/changerOrdre/'.$rowElement['Infosupdef']['id'].'/0', array('escape' => false), false); ?>
		<?php echo $this->Html->link('&#9660;', '/infosupdefs/changerOrdre/'.$rowElement['Infosupdef']['id'], array('escape' => false), false); ?>
	</td>
	<td><?php echo $rowElement['Infosupdef']['nom']; ?></td>
	<td><?php echo $rowElement['Infosupdef']['commentaire']; ?></td>
	<td><?php echo $rowElement['Infosupdef']['code']; ?></td>
	<td><?php echo $Infosupdef->libelleType($rowElement['Infosupdef']['type']); ?></td>
	<td><?php echo $Infosupdef->libelleRecherche($rowElement['Infosupdef']['recherche']); ?></td>
	<td><?php echo $Infosupdef->libelleActif($rowElement['Infosupdef']['actif']); ?></td>
	<td class="actions">
<?php
	if ($rowElement['Infosupdef']['type'] == 'list')
		echo $this->Html->link(SHY,'/infosuplistedefs/index/' . $rowElement['Infosupdef']['id'], array('class'=>'link_liste', 'escape' => false, 'title'=>'Liste'), false);
	echo $this->Html->link(SHY,'/infosupdefs/view/' . $rowElement['Infosupdef']['id'], array('class'=>'link_voir', 'escape' => false, 'title'=>'Voir'), false);
	echo $this->Html->link(SHY,'/infosupdefs/edit/' . $rowElement['Infosupdef']['id'], array('class'=>'link_modifier', 'escape' => false, 'title'=>'Modifier'), false);
	if ($Infosupdef->isDeletable($rowElement['Infosupdef']['id']))
		echo $this->Html->link(SHY,'/infosupdefs/delete/' . $rowElement['Infosupdef']['id'], array('class'=>'link_supprimer', 'escape' => false, 'title'=>'Supprimer'), 'Voulez-vous supprimer l\'information \''.$rowElement['Infosupdef']['nom'].'\' ?');
?>
	</td>
</tr>
<?php endforeach; ?>
</table>

<ul class="actions">
	<?php echo $this->Html->link('<i class="icon-plus-sign"></i> Ajouter une information suppl&eacute;mentaire', $lienAdd, array('class'=>'btn btn-primary', 'escape' => false, 'title'=>'Ajouter une information supplémentaire'), false); ?>
</ul>

</div>
