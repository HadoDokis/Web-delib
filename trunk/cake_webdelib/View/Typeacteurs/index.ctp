<div class="seances">

<h2>Liste des types d'acteur</h2>

<table cellpadding="0" cellspacing="0" width='100%'>
<tr>
	<th>Nom</th>
	<th>Commentaire</th>
	<th>Statut</th>
	<th>Actions</th>
</tr>

<?php foreach ($typeacteurs as $typeacteur): ?>
<tr height='36px'>
	<td><?php echo $typeacteur['Typeacteur']['nom']; ?></td>
	<td><?php echo $typeacteur['Typeacteur']['commentaire']; ?></td>
	<td><?php echo $typeacteur['Typeacteur']['elu'] ? 'élu' : 'non élu'; ?></td>
	<td class="actions">
		<?php echo $this->Html->link(SHY,'/typeacteurs/view/' . $typeacteur['Typeacteur']['id'], array('class'=>'link_voir', 'escape' => false, 'title'=>'Voir'), false)?>
		<?php echo $this->Html->link(SHY,'/typeacteurs/edit/' . $typeacteur['Typeacteur']['id'], array('class'=>'link_modifier','escape' => false,  'title'=>'Modifier'), false)?>
		<?php if (empty($typeacteur['Acteur'])) echo $this->Html->link(SHY,'/typeacteurs/delete/' . $typeacteur['Typeacteur']['id'], array('class'=>'link_supprimer','escape' => false,  'title'=>'Supprimer'), 'Voulez-vous supprimer le type d\'acteur \''.$typeacteur['Typeacteur']['nom'].'\' ?'); ?>
	</td>
</tr>
<?php endforeach; ?>
</table>
<?php $this->Html2->boutonAdd("Ajouter un type d'acteur","Ajouter un type d'acteur"); ?>
</div>