<div class="seances">

<h2>Liste des acteurs</h2>

<table cellpadding="0" cellspacing="0">
<tr>
	<th>Civilit&eacute;</th>
	<th><?php echo $paginator->sort('Nom', 'nom'); ?></th>
	<th><?php echo $paginator->sort('Prénom', 'prenom'); ?></th>
	<th><?php echo $paginator->sort('Titre', 'titre'); ?></th>
	<th><?php echo $paginator->sort('Type d\'acteur', 'Typeacteur.nom'); ?></th>
	<th>Elus</th>
	<th><?php echo $paginator->sort('N° d\'ordre', 'position'); ?></th>
	<th>Téléphone</th>
	<th>Mobile</th>
	<th><?php echo $paginator->sort('Délégation(s)', 'Service.libelle'); ?></th>
	<th width='90px'>Actions</th>
</tr>
<?php foreach ($acteurs as $acteur): ?>
<tr height="36px">
	<td><?php echo $acteur['Acteur']['salutation']; ?></td>
	<td><?php echo $acteur['Acteur']['nom']; ?></td>
	<td><?php echo $acteur['Acteur']['prenom']; ?></td>
	<td><?php echo $acteur['Acteur']['titre']; ?></td>
	<td><?php echo $acteur['Typeacteur']['nom']; ?></td>
	<td><?php echo $Acteur->Typeacteur->libelleElu($acteur['Typeacteur']['elu'], true); ?></td>
	<td><?php echo $Acteur->libelleOrdre($acteur['Acteur']['position']); ?></td>
	<td><?php echo $acteur['Acteur']['telfixe']; ?></td>
	<td><?php echo $acteur['Acteur']['telmobile']; ?></td>
	<td><?php foreach ($acteur['Service'] as $service) 
              echo ($service['libelle']).'<br/>';
             ?></td>
	<td class="actions">
		<?php echo $html->link(SHY,'/acteurs/view/' . $acteur['Acteur']['id'], array('class'=>'link_voir', 'title'=>'Voir'), false, false); ?>
		<?php echo $html->link(SHY,'/acteurs/edit/' . $acteur['Acteur']['id'], array('class'=>'link_modifier', 'title'=>'Modifier'), false, false); ?>
		<?php
			$message='';
                        echo $html->link(SHY,'/acteurs/delete/' . $acteur['Acteur']['id'], array('class'=>'link_supprimer', 'title'=>'Supprimer'), 'Voulez-vous supprimer l\'acteur \''.$acteur['Acteur']['prenom'].' '.$acteur['Acteur']['nom'].'\' ?', false); ?>
	</td>
</tr>
<?php endforeach; ?>
</table>

<div class='paginate'>
	<!-- Affiche les numéros de pages -->
	<?php echo $paginator->numbers(); ?>
	<!-- Affiche les liens des pages précédentes et suivantes -->
	<?php
		echo $paginator->prev('« Précédent ', null, null, array( 'tag' => 'span', 'class' => 'disabled'));
		echo $paginator->next(' Suivant »', null, null, array( 'tag' => 'span', 'class' => 'disabled'));
	?> 
	<!-- Affiche X de Y, où X est la page courante et Y le nombre de pages -->
	<?php echo $paginator->counter(array('format'=>'Page %page% sur %pages%')); ?>
</div>

<ul class="actions">
	<li><?php echo $html->link('Ajouter un acteur', '/acteurs/add/', array('class'=>'link_add', 'title'=>'Ajouter un acteur')); ?></li>
</ul>

</div>
