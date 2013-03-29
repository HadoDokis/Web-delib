<div class="seances">

<h2>Liste des acteurs</h2>
<table cellpadding="0" cellspacing="0" width='100%'>
<tr>
	<th>Civilit&eacute;</th>
	<th><?php echo $this->Paginator->sort( 'nom', 'Nom'); ?></th>
	<th><?php echo $this->Paginator->sort('prenom', 'Prénom'); ?></th>
	<th><?php echo $this->Paginator->sort('titre', 'Titre'); ?></th>
	<th><?php echo $this->Paginator->sort( 'Typeacteur.nom', 'Type d\'acteur'); ?></th>
	<th>Elus</th>
	<th><?php echo $this->Paginator->sort('position', 'N° d\'ordre'); ?></th>
	<th>Téléphone</th>
	<th>Mobile</th>
	<th>Suppléant</th>
	<th><?php echo $this->Paginator->sort('Service.libelle',  'Délégation(s)'); ?></th>
	<th width='160px'>Actions</th>
</tr>
<?php foreach ($acteurs as $acteur): ?>
<tr height="36px">
	<td><?php echo $acteur['Acteur']['salutation']; ?></td>
	<td><?php echo $acteur['Acteur']['nom']; ?></td>
	<td><?php echo $acteur['Acteur']['prenom']; ?></td>
	<td><?php echo $acteur['Acteur']['titre']; ?></td>
	<td><?php echo $acteur['Typeacteur']['nom']; ?></td>
	<td><?php echo $acteur['Typeacteur']['elu']; ?></td>
	<td><?php echo $acteur['Acteur']['libelleOrdre']; ?></td>
	<td><?php echo $acteur['Acteur']['telfixe']; ?></td>
	<td><?php echo $acteur['Acteur']['telmobile']; ?></td>
	<td><?php if (isset( $acteur['Acteur']['suppleant_id'])) echo $acteur['Suppleant']['prenom']." ".$acteur['Suppleant']['nom']; ?></td>
	<td><?php foreach ($acteur['Service'] as $service) 
              echo ($service['libelle']).'<br/>';
             ?></td>
	<td class="actions">
		<?php echo $this->Html->link(SHY,'/acteurs/view/' . $acteur['Acteur']['id'], array('class'=>'link_voir', 'escape' => false,  'title'=>'Voir'), false); ?>
		<?php echo $this->Html->link(SHY,'/acteurs/edit/' . $acteur['Acteur']['id'], array('class'=>'link_modifier', 'escape' => false, 'title'=>'Modifier'), false); ?>
		<?php
			$message='';
                        echo $this->Html->link(SHY,'/acteurs/delete/' . $acteur['Acteur']['id'], array('class'=>'link_supprimer','escape' => false,  'title'=>'Supprimer'), 'Voulez-vous supprimer l\'acteur \''.$acteur['Acteur']['prenom'].' '.$acteur['Acteur']['nom'].'\' ?'); ?>
	</td>
</tr>
<?php endforeach; ?>
</table>

<div class='paginate'>
	<!-- Affiche les numéros de pages -->
	<?php echo $this->Paginator->numbers(); ?>
	<!-- Affiche les liens des pages précédentes et suivantes -->
	<?php
		echo $this->Paginator->prev('« Précédent ', null, null, array( 'tag' => 'span', 'class' => 'disabled'));
		echo $this->Paginator->next(' Suivant »', null, null, array( 'tag' => 'span', 'class' => 'disabled'));
	?> 
	<!-- Affiche X de Y, où X est la page courante et Y le nombre de pages -->
	<?php echo $this->Paginator->counter(array('format'=>'Page %page% sur %pages%')); ?>
</div>

<?php $this->Html2->boutonAdd("Ajouter un acteur","Ajouter un acteur"); ?>

</div>
