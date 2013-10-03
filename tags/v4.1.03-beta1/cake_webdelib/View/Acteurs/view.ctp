<div id="vue_cadre">
<h3>Fiche acteur</h3>

<dl>
	<div class="demi">
		<dt>Identit&eacute;</dt>
		<dd>&nbsp;<?php echo $acteur['Acteur']['salutation'].' '.$acteur['Acteur']['prenom'].' '.$acteur['Acteur']['nom'].($acteur['Acteur']['titre'] ? ', ':'').$acteur['Acteur']['titre']?></dd>
		<dt>Adresse postale</dt>
			<dd class="compact"><?php echo $acteur['Acteur']['adresse1']?></dd>
			<dd class="compact"><?php echo $acteur['Acteur']['adresse2']?></dd>
			<dd class="compact"><?php echo $acteur['Acteur']['cp']?></dd>
			<dd class="compact"><?php echo $acteur['Acteur']['ville']?></dd>
		<dt>Contacts</dt>
			<dd class="compact">Téléphone fixe : <?php echo $acteur['Acteur']['telfixe']?></dd>
			<dd class="compact">Téléphone mobile : <?php echo $acteur['Acteur']['telmobile']?></dd>
			<dd class="compact">Adresse email : <?php echo $acteur['Acteur']['email']?></dd>
	</div>
	<div class="demi">
		<dt>Type</dt>
		<dd>&nbsp;<?php echo $acteur['Typeacteur']['nom']?></dd>
		<?php if($acteur['Typeacteur']['elu']) {
			echo "<dt>Numéro d'ordre dans le conseil</dt>";
			echo "<dd>".$acteur['Acteur']['position']."</dd>";
			echo "<dt>Délégations</dt>";
			foreach ($acteur['Service'] as $service){
				echo '<dd class="compact">'.$service['libelle'].'</dd>';
			};
			echo "<dt>Date Naissance</dt>";
			echo "<dd>".$acteur['Acteur']['date_naissance']."</dd>";
		} ?>
	</div>
	<div class="spacer"></div>

	<div class="tiers">
		<dt>Note</dt>
		<dd>&nbsp;<?php echo $acteur['Acteur']['note']?></dd>
	</div>
	<div class="tiers">
		<dt>Date de création</dt>
		<dd>&nbsp;<?php echo $acteur['Acteur']['created']?></dd>
	</div>
	<div class="tiers">
		<dt>Date de modification</dt>
		<dd>&nbsp;<?php echo $acteur['Acteur']['modified']?></dd>
	</div>
	<div class="spacer"></div>

</dl>


<div class='btn-group' style='text-align: center;'>
	<?php
        $this->Html2->boutonRetour('index', 'float:none;');
		if ($Droits->check($this->Session->read('user.User.id'), 'Acteurs:edit'))
                    $this->Html2->boutonModifierUrl('/acteurs/edit/' . $acteur['Acteur']['id'], 'Modifier', 'Modifier', 'float:none;', '');
                
//		echo '<li>' . $this->Html->link(SHY, $this->Session->read('user.User.lasturl'), array('class'=>'link_annuler_sans_border', 'escape' => false, 'title'=>'Annuler'), false) . '</li>';
//            if ($Droits->check($this->Session->read('user.User.id'), 'Acteurs:edit'))
//			echo '<li>'.$this->Html->link(SHY, '/acteurs/edit/' . $acteur['Acteur']['id'], array('class'=>'link_modifier', 'escape' => false, 'title'=>'Modifier'), false).'</li>';
	?>
</div>

<div class="spacer"></div>

</div>