<div class="seances">

<h2>Liste des compteurs param&eacute;trables</h2>

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<th>Nom</th>
	<th>Commentaire</th>
	<th>D&eacute;finition</th>
	<th>Crit&egrave;re de r&eacute;initialisation</th>
	<th>S&eacute;quence</th>
	<th>Actions</th>
</tr>

<?php foreach ($compteurs as $compteur): ?>
<tr height="36px">
	<td><?php echo $compteur['Compteur']['nom']; ?></td>
	<td><?php echo $compteur['Compteur']['commentaire']; ?></td>
	<td><?php echo $compteur['Compteur']['def_compteur']; ?></td>
	<td><?php echo $compteur['Compteur']['def_reinit']; ?></td>
	<td><?php echo $compteur['Sequence']['nom'].' : '.$compteur['Sequence']['num_sequence']; ?></td>
	<td class="actions">
		<?php echo $this->Html->link(SHY,'/compteurs/view/' . $compteur['Compteur']['id'], array('class'=>'link_voir','escape' => false,  'title'=>'Voir'), false)?>
		<?php echo $this->Html->link(SHY,'/compteurs/edit/' . $compteur['Compteur']['id'], array('class'=>'link_modifier', 'escape' => false, 'title'=>'Modifier'), false)?>
		<?php if (empty($compteur['Typeseance'])) echo $this->Html->link(SHY,'/compteurs/delete/' . $compteur['Compteur']['id'], array('class'=>'link_supprimer', 'escape' => false, 'title'=>'Supprimer'), 'Voulez-vous supprimer le compteur \''.$compteur['Compteur']['nom'].'\' ?')?>
	</td>
</tr>
<?php endforeach; ?>
</table>

<?php $this->Html2->boutonAdd("Ajouter un compteur", "Ajouter un compteur"); ?>
<!--<ul class="actions">
	<li><?php echo $this->Html->link('Ajouter un compteur', '/compteurs/add/', array('class'=>'link_add', 'title'=>'Ajouter un compteur')); ?></li>
</ul>-->

</div>
