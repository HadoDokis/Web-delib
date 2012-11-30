<div class="typeactes">
<h2>Liste des types d'acte</h2>

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<th>Libell&eacute;</th>
	<th>Compteur</th>
	<th>Mod&egrave;les</th>
	<th>Nature</th>
	<th width='90px'>Actions</th>
</tr>
<?php foreach ($typeactes as $typeacte): ?>
<tr>
	<td><?php echo $typeacte['Typeacte']['libelle']; ?></td>
	<td><?php echo $typeacte['Compteur']['nom']; ?></td>
	<td><?php
		echo 'Document préparatoire : ' .       $typeacte['Modelprojet']['modele'] . '<br/>';
		echo 'Document final : ' .  $typeacte['Modeldeliberation']['modele'] . '<br/>';
	?></td>
	<td><?php echo $typeacte['Nature']['libelle']; ?></td>
	<td class="actions">
	<?php
		echo $this->Html->link(SHY,'/typeactes/view/' . $typeacte['Typeacte']['id'], array('class'=>'link_voir', 'escape' => false,  'title'=>'Voir'),false);
		echo $this->Html->link(SHY,'/typeactes/edit/' . $typeacte['Typeacte']['id'], array('class'=>'link_modifier', 'escape' => false, 'title'=>'Modifier'), false);
	    if ($typeacte['Typeacte']['is_deletable'])
			echo $this->Html->link(SHY,'/typeactes/delete/' . $typeacte['Typeacte']['id'], array('class'=>'link_supprimer', 'escape' => false,  'title'=>'Supprimer'), 'Ete vous sur de vouloir supprimer "' . $typeacte['Typeacte']['libelle'] .'" ?');
	?>
	</td>
</tr>
<?php endforeach; ?>
</table>

<ul class="actions">
	<li><?php echo $this->Html->link('Ajouter un type d\' acte', '/typeactes/add', array('class'=>'link_add', 'title'=>'Ajouter')); ?></li>
</ul>
</div>
