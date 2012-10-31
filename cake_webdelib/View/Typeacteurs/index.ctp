<div class="seances">

<h2>Liste des types d'acteur</h2>

<table cellpadding="0" cellspacing="0">
<tr>
	<th>Nom</th>
	<th>Commentaire</th>
	<th>Statut</th>
	<th>Actions</th>
</tr>

<?php foreach ($typeacteurs as $typeacteur): ?>
<tr>
	<td><?php echo $typeacteur['Typeacteur']['nom']; ?></td>
	<td><?php echo $typeacteur['Typeacteur']['commentaire']; ?></td>
	<td><?php echo $typeacteur['Typeacteur']['elu'] ? 'élu' : 'non élu'; ?></td>
	<td class="actions">
		<?php echo $html->link(SHY,'/typeacteurs/view/' . $typeacteur['Typeacteur']['id'], array('class'=>'link_voir', 'title'=>'Voir'), false, false)?>
		<?php echo $html->link(SHY,'/typeacteurs/edit/' . $typeacteur['Typeacteur']['id'], array('class'=>'link_modifier', 'title'=>'Modifier'), false, false)?>
		<?php if (empty($typeacteur['Acteur'])) echo $html->link(SHY,'/typeacteurs/delete/' . $typeacteur['Typeacteur']['id'], array('class'=>'link_supprimer', 'title'=>'Supprimer'), 'Voulez-vous supprimer le type d\'acteur \''.$typeacteur['Typeacteur']['nom'].'\' ?', false); ?>
	</td>
</tr>
<?php endforeach; ?>
</table>

<ul class="actions">
	<li><?php echo $html->link('Ajouter un type d\'acteur', '/typeacteurs/add/', array('class'=>'link_add', 'title'=>'Ajouter un type d\'acteur')); ?></li>
</ul>

</div>