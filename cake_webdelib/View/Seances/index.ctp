<div class="seances">
<h2>Liste des seances</h2>

<table cellpadding="0" cellspacing="0">
<tr>
	<th>Id</th>
	<th>Libelle</th>
	<th>Date</th>
	<th>Actions</th>
</tr>
<?php foreach ($seances as $seance): ?>
<tr>
	<td><?php echo $seance['Seance']['id']; ?></td>
	<td><?php echo $seance['Typeseance']['libelle']; ?></td>
	<td><?php echo $seance['Seance']['date']; ?></td>
	<td class="actions">
		<?php echo $this->Html->link(SHY,'/seances/view/' . $seance['Seance']['id'], array('class'=>'link_voir', 'title'=>'Voir'), false, false)?>
		<?php echo $this->Html->link(SHY,'/seances/edit/' . $seance['Seance']['id'], array('class'=>'link_modifier', 'title'=>'Modifier'), false, false)?>
		<?php echo $this->Html->link(SHY,'/seances/delete/' . $seance['Seance']['id'], array('class'=>'link_supprimer', 'title'=>'Supprimer'), 'Etes-vous sur de vouloir supprimer la seance du "' . $seance['Seance']['date'].'" ?', false)?>
	</td>
</tr>
<?php endforeach; ?>
</table>

<ul class="actions">
	<li><?php echo $this->Html->link('Ajouter une seance', '/seances/add', array('class'=>'link_add', 'title'=>'Ajouter une Séance')); ?></li>
</ul>
</div>
