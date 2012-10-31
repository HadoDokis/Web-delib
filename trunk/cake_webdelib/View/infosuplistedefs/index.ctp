<div class="seances">

<h2>Gestion de la liste de l'information suppl&eacute;mentaire : <?php echo $infosupdef['Infosupdef']['nom']; ?></h2>

<table cellpadding="0" cellspacing="0">
<tr>
	<th>Ordre</th>
	<th>Nom</th>
	<th width='90px'>Actions</th>
</tr>

<?php foreach ($this->data as $rownum=>$rowElement): ?>
<tr>
	<td class="ordre">
		<?php echo $html->link('&#9650;', '/infosuplistedefs/changerOrdre/'.$rowElement['Infosuplistedef']['id'].'/0', array(), false, false); ?>
		<?php echo $html->link('&#9660;', '/infosuplistedefs/changerOrdre/'.$rowElement['Infosuplistedef']['id'], array(), false, false); ?>
	</td>
	<td><?php echo $rowElement['Infosuplistedef']['nom']; ?></td>
	<td class="actions">
		<?php echo $html->link(SHY,'/infosuplistedefs/edit/'.$rowElement['Infosuplistedef']['id'], array('class'=>'link_modifier', 'title'=>'Modifier'), false, false); ?>
		<?php echo '&nbsp;&nbsp;'; ?>
		<?php echo $html->link(SHY,'/infosuplistedefs/delete/'.$rowElement['Infosuplistedef']['id'], array('class'=>'link_supprimer', 'title'=>'Supprimer'), 'Voulez-vous supprimer l\'element \''.$rowElement['Infosuplistedef']['nom'].'\' ?', false); ?>
	</td>
</tr>
<?php endforeach; ?>
</table>

<ul class="actions">
	<?php echo $html->link('Ajouter un &eacute;l&eacute;ment', '/infosuplistedefs/add/'.$infosupdef['Infosupdef']['id'], array('class'=>'link_add', 'title'=>'Ajouter un &eacute;l&eacute;ment'), false, false); ?>
	<?php echo $html->link('Retourner à la liste des informations suppl&eacute;mentaires', '/infosupdefs/index/', array('class'=>'link_annuler_sans_border'), false, false); ?>
</ul>

</div>
