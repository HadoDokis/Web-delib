<div class="seances">

<h2>Liste des informations suppl&eacute;mentaires des projets</h2>

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<th>Ordre</th>
	<th>Nom</th>
	<th>Commentaire</th>
	<th>Code</th>
	<th>Type</th>
	<th>Recherche</th>
	<th width='160px'>Actions</th>
</tr>

<?php foreach ($this->data as $rownum=>$rowElement): ?>
<tr height='36px'>
	<td class="ordre">
		<?php echo $html->link('&#9650;', '/infosupdefs/changerOrdre/'.$rowElement['Infosupdef']['id'].'/0', array(), false, false); ?>
		<?php echo $html->link('&#9660;', '/infosupdefs/changerOrdre/'.$rowElement['Infosupdef']['id'], array(), false, false); ?>
	</td>
	<td><?php echo $rowElement['Infosupdef']['nom']; ?></td>
	<td><?php echo $rowElement['Infosupdef']['commentaire']; ?></td>
	<td><?php echo $rowElement['Infosupdef']['code']; ?></td>
	<td><?php echo $Infosupdef->libelleType($rowElement['Infosupdef']['type']); ?></td>
	<td><?php echo $Infosupdef->libelleRecherche($rowElement['Infosupdef']['recherche']); ?></td>
	<td class="actions">
		<?php if ($rowElement['Infosupdef']['type'] == 'list')
			echo $html->link(SHY,'/infosuplistedefs/index/' . $rowElement['Infosupdef']['id'], array('class'=>'link_liste', 'title'=>'Liste'), false, false); ?>
		<?php echo $html->link(SHY,'/infosupdefs/view/' . $rowElement['Infosupdef']['id'], array('class'=>'link_voir', 'title'=>'Voir'), false, false); ?>
		<?php echo $html->link(SHY,'/infosupdefs/edit/' . $rowElement['Infosupdef']['id'], array('class'=>'link_modifier', 'title'=>'Modifier'), false, false); ?>
	</td>
</tr>
<?php endforeach; ?>
</table>

<ul class="actions">
	<?php echo $html->link('Ajouter une information suppl&eacute;mentaire', '/infosupdefs/add/Deliberation', array('class'=>'link_add', 'title'=>'Ajouter une information suppl�mentaire'), false, false); ?>
</ul>

</div>
