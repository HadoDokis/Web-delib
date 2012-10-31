<div id="vue_cadre">
<h3><?php echo $titre; ?></h3>

<dl>
	<dt>Nom</dt>
		<dd class="compact"><?php echo $this->data['Infosupdef']['nom']; ?></dd>
	<dt>Commentaire</dt>
		<dd class="compact"><?php echo $this->data['Infosupdef']['commentaire']; ?></dd>
	<dt>Code</dt>
		<dd class="compact"><?php echo $this->data['Infosupdef']['code']; ?></dd>
	<dt>Num&eacute;ro d'ordre</dt>
		<dd class="compact"><?php echo $this->data['Infosupdef']['ordre']; ?></dd>
	<dt>Type</dt>
		<dd class="compact"><?php echo $this->data['Infosupdef']['libelleType']; ?></dd>
	<dt>Taille</dt>
		<dd class="compact"><?php echo $this->data['Infosupdef']['taille']; ?></dd>
	<dt>Valeur initiale</dt>
		<dd class="compact"><?php echo $this->data['Infosupdef']['val_initiale']; ?></dd>
	<dt>Inclure dans la recherche</dt>
		<dd class="compact"><?php echo $this->data['Infosupdef']['libelleRecherche']; ?></dd>
	<div class="gauche">
		<dt>Date de cr&eacute;ation</dt>
		<dd>&nbsp;<?php echo $this->data['Infosupdef']['created']?></dd>
	</div>
	<div class="droite">
		<dt>Date de modification</dt>
		<dd>&nbsp;<?php echo $this->data['Infosupdef']['modified']?></dd>
	</div>
</dl>

<ul id="actions_fiche">
	<?php
		echo '<li>' . $html->link(SHY, $lienRetour, array('class'=>'link_annuler_sans_border', 'title'=>'Annuler'), false, false) . '</li>';
		if ($Droits->check($session->read('user.User.id'), 'Infosupdefs:edit'))
			echo '<li>'.$html->link(SHY, '/infosupdefs/edit/' . $this->data['Infosupdef']['id'], array('class'=>'link_modifier', 'title'=>'Modifier'), false, false).'</li>';
	?>
</ul>

</div>
