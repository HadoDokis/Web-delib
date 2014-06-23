<div class="seances">

<h2>Gestion de la liste de l'information supplémentaire : <?php echo $infosupdef['Infosupdef']['nom']; ?></h2>
<?php echo $this->element('filtre'); ?>

<table cellpadding="0" cellspacing="0" width='100%'>
<tr>
	<th>Ordre</th>
	<th>Nom</th>
	<th>Actif</th>
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
		<?php echo $rowElement['Infosuplistedef']['ordre'].' '; ?>
		<?php echo $this->Html->link('&#9650;', '/infosuplistedefs/changerOrdre/'.$rowElement['Infosuplistedef']['id'].'/0', array('escape' => false), false); ?>
		<?php echo $this->Html->link('&#9660;', '/infosuplistedefs/changerOrdre/'.$rowElement['Infosuplistedef']['id'], array('escape' => false), false); ?>
	</td>
	<td><?php echo $rowElement['Infosuplistedef']['nom']; ?></td>
	<td><?php echo $Infosuplistedef->libelleActif($rowElement['Infosuplistedef']['actif']); ?></td>
	<td class="actions">
		<?php echo $this->Html->link(SHY,'/infosuplistedefs/edit/'.$rowElement['Infosuplistedef']['id'], array('class'=>'link_modifier',  'escape' => false, 'title'=>'Modifier'), false); ?>
		<?php echo '&nbsp;&nbsp;'; ?>
		<?php if ($Infosuplistedef->isDeletable($rowElement['Infosuplistedef']['id']))
			echo $this->Html->link(SHY,'/infosuplistedefs/delete/'.$rowElement['Infosuplistedef']['id'], array('class'=>'link_supprimer', 'escape' => false, 'title'=>'Supprimer'), 'Voulez-vous supprimer l\'element \''.$rowElement['Infosuplistedef']['nom'].'\' ?'); ?>
	</td>
</tr>
<?php endforeach; ?>
</table>

<ul class="actions btn-group">
    <?php echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour', array('controller'=>'infosupdefs', 'action'=>'index'), array('class'=>'btn',  'escape' => false), false); ?>
    <?php echo $this->Html->link('<i class="fa fa-plus"></i> Ajouter un élément', array('action'=>'add', $infosupdef['Infosupdef']['id']), array('class'=>'btn btn-primary', 'escape' => false, 'title'=>'Ajouter un élément'), false); ?>
</ul>

</div>
