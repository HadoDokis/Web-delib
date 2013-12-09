<div class="typeseances">
<h2>Liste des types de séance</h2>

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<th>Libell&eacute;</th>
	<th>Nb jours avant retard</th>
	<th>Action</th>
	<th>Compteur</th>
	<th>Mod&egrave;les</th>
	<th>Convocations</th>
	<th width='90px'>Actions</th>
</tr>
<?php foreach ($typeseances as $typeseance): ?>
<tr>
	<td><?php echo $typeseance['Typeseance']['libelle']; ?></td>
	<td><?php echo $typeseance['Typeseance']['retard']; ?></td>
	<td><?php echo $typeseance['Typeseance']['action']; ?></td>
	<td><?php echo $typeseance['Compteur']['nom']; ?></td>
	<td><?php
		echo 'projet : ' .       $typeseance['Modelprojet']['modele'] . '<br/>';
		echo 'd&eacute;lib&eacute;ration : ' .  $typeseance['Modeldeliberation']['modele'] . '<br/>';
		echo 'convocation : ' .  $typeseance['Modelconvocation']['modele'] . '<br/>';
		echo 'ordre du jour : ' . $typeseance['Modelordredujour']['modele'] . '<br/>';
		echo 'PV sommaire : ' .  $typeseance['Modelpvsommaire']['modele'] . '<br/>';
		echo 'PV d&eacute;taill&eacute; : ' .  $typeseance['Modelpvdetaille']['modele'] . '<br/>';
	?></td>
	<td><?php
		if (!empty($typeseance['Typeacteur'])) {
			echo 'Types d\'acteur :<br/>';
			foreach ($typeseance['Typeacteur'] as $typeacteur)
				echo '&nbsp;&nbsp;'.$typeacteur['nom'].'<br/>';
		}
		if (!empty($typeseance['Acteur'])) {
			echo 'Acteurs :<br/>';
			foreach ($typeseance['Acteur'] as $acteur)
				echo '&nbsp;'.$acteur['prenom'].' '.$acteur['nom'].'<br/>';
		}
	?></td>
	<td class="actions">
	<?php
		echo $this->Html->link(SHY,'/typeseances/view/' . $typeseance['Typeseance']['id'], array('class'=>'link_voir', 'escape' => false,  'title'=>'Voir'),false);
		echo $this->Html->link(SHY,'/typeseances/edit/' . $typeseance['Typeseance']['id'], array('class'=>'link_modifier', 'escape' => false, 'title'=>'Modifier'), false);
                if ($typeseance['Typeseance']['is_deletable'])
                    echo $this->Html->link(SHY,'/typeseances/delete/' . $typeseance['Typeseance']['id'], array('class'=>'link_supprimer', 'escape' => false,  'title'=>'Supprimer'), 'Êtes vous sur de vouloir supprimer ' . $typeseance['Typeseance']['libelle'] .' ?');
	?>
	</td>
</tr>
<?php endforeach; ?>
</table>

<?php $this->Html2->boutonAdd("Ajouter un type de séance", "Ajoute un type de séance"); ?>

<!--<ul class="actions">
	<li><?php echo $this->Html->link('Ajouter un type de séance', '/typeseances/add', array('class'=>'link_add', 'title'=>'Ajouter')); ?></li>
</ul>-->
</div>
