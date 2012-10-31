<div class="seances">

<h2>Gestion de la liste de l'information suppl&eacute;mentaire : <?php echo $infosupdef['Infosupdef']['nom']; ?></h2>

<table cellpadding="0" cellspacing="0" width='100%'>
<tr>
	<th>Ordre</th>
	<th>Nom</th>
	<th width='150px'>Actions</th>
</tr>

<?php 
       $numLigne = 1;
       foreach ($this->data as $rownum=>$rowElement): 
       $rowClass = ($numLigne & 1)?array('height' => '36px'):array( 'height' => '36px', 'class'=>'altrow');
       echo $this->Html->tag('tr', null, $rowClass);
       $numLigne++;
?>

	<td class="ordre">
		<?php echo $this->Html->link('&#9650;', '/infosuplistedefs/changerOrdre/'.$rowElement['Infosuplistedef']['id'].'/0', array('escape' => false), false); ?>
		<?php echo $this->Html->link('&#9660;', '/infosuplistedefs/changerOrdre/'.$rowElement['Infosuplistedef']['id'], array('escape' => false), false); ?>
	</td>
	<td><?php echo $rowElement['Infosuplistedef']['nom']; ?></td>
	<td class="actions">
		<?php echo $this->Html->link(SHY,'/infosuplistedefs/edit/'.$rowElement['Infosuplistedef']['id'], array('class'=>'link_modifier',  'escape' => false, 'title'=>'Modifier'), false); ?>
		<?php echo '&nbsp;&nbsp;'; ?>
		<?php echo $this->Html->link(SHY,'/infosuplistedefs/delete/'.$rowElement['Infosuplistedef']['id'], array('class'=>'link_supprimer', 'escape' => false, 'title'=>'Supprimer'), 'Voulez-vous supprimer l\'element \''.$rowElement['Infosuplistedef']['nom'].'\' ?'); ?>
	</td>
</tr>
<?php endforeach; ?>
</table>

<ul class="actions">
	<?php echo $this->Html->link('Ajouter un &eacute;l&eacute;ment', '/infosuplistedefs/add/'.$infosupdef['Infosupdef']['id'], array('class'=>'link_add',  'escape' => false,  'title'=>'Ajouter un &eacute;l&eacute;ment'), false); ?>
	<?php echo $this->Html->link('Retourner à la liste des informations suppl&eacute;mentaires', '/infosupdefs/index/', array('class'=>'link_annuler_sans_border',  'escape' => false), false); ?>
</ul>

</div>
