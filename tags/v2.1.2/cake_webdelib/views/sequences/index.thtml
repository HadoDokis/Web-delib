<div class="seances">

<h2>Liste des s&eacute;quences</h2>

<table cellpadding="0" cellspacing="0">
<tr>
	<th>Nom</th>
	<th>Commentaire</th>
	<th>Num&eacute;ro de s&eacute;quence</th>
	<th>Actions</th>
</tr>

<?php foreach ($sequences as $sequence): ?>
<tr>
	<td><?php echo $sequence['Sequence']['nom']; ?></td>
	<td><?php echo $sequence['Sequence']['commentaire']; ?></td>
	<td><?php echo $sequence['Sequence']['num_sequence']; ?></td>
	<td class="actions">
		<?php echo $html->link(SHY,'/sequences/view/' . $sequence['Sequence']['id'], array('class'=>'link_voir', 'title'=>'Voir'), false, false)?>
		<?php echo $html->link(SHY,'/sequences/edit/' . $sequence['Sequence']['id'], array('class'=>'link_modifier', 'title'=>'Modifier'), false, false)?>
		<?php if (empty($sequence['Compteur'])) echo $html->link(SHY,'/sequences/delete/' . $sequence['Sequence']['id'], array('class'=>'link_supprimer', 'title'=>'Supprimer'), 'Voulez-vous supprimer la séquence \''.$sequence['Sequence']['nom'].'\' ?', false); ?>
	</td>
</tr>
<?php endforeach; ?>
</table>

<ul class="actions">
	<li><?php echo $html->link('Ajouter une séquence', '/sequences/add/', array('class'=>'link_add', 'title'=>'Ajouter une séquence')); ?></li>
</ul>

</div>