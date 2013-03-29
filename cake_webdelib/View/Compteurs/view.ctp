<div id="vue_cadre">
<h3>Fiche Compteur param&eacute;trable</h3>

<dl>

<div class="imbrique">
	<div class="gauche">
		<dt>Nom</dt>
		<dd>&nbsp;<?php echo $compteur['Compteur']['nom']?></dd>
	</div>
	<div class="droite">
		<dt>Commentaire</dt>
		<dd>&nbsp;<?php echo $compteur['Compteur']['commentaire']?></dd>
	</div>
</div>

<div class="imbrique">
	<div class="gauche">
		<dt>D&eacute;finition du compteur</dt>
		<dd>&nbsp;<?php echo $compteur['Compteur']['def_compteur']?></dd>
	</div>
	<div class="droite">
		<dt>Nom et num&eacute;ro de la s&eacute;quence</dt>
		<dd>&nbsp;<?php echo $compteur['Sequence']['nom'].' : '.$compteur['Sequence']['num_sequence']?></dd>
	</div>
</div>

<div class="imbrique">
	<div class="gauche">
		<dt>Crit&egrave;re de r&eacute;initialisation de la s&eacute;quence</dt>
		<dd>&nbsp;<?php echo $compteur['Compteur']['def_reinit']?></dd>
	</div>
	<div class="droite">
		<dt>Dernière valeur calcul&eacute;e du crit&egrave;re de r&eacute;initialisation</dt>
		<dd>&nbsp;<?php echo $compteur['Compteur']['val_reinit']?></dd>
	</div>
</div>

<div class="imbrique">
	<div class="gauche">
		<dt>Date de cr&eacute;ation</dt>
		<dd>&nbsp;<?php echo $compteur['Compteur']['created']?></dd>
	</div>
	<div class="droite">
		<dt>Date de modification</dt>
		<dd>&nbsp;<?php echo $compteur['Compteur']['modified']?></dd>
	</div>
</div>
</dl>

<div id="actions_fiche" class='btn-group' style='text-align: center;'>
	<?php
        $this->Html2->boutonRetour('index', 'float:none;');
		if ($Droits->check($this->Session->read('user.User.id'), 'Compteurs:edit'))
                    $this->Html2->boutonModifierUrl('/compteurs/edit/' . $compteur['Compteur']['id'], 'Modifier', 'Modifier', 'float:none;', '');
                
	?>
</div>

<div class="spacer"></div>

<!--<br />
<ul id="actions_fiche">
	<?php
//		echo '<li>' . $this->Html->link(SHY, $this->Session->read('user.User.lasturl'), array('class'=>'link_annuler_sans_border', 'title'=>'Annuler'), false, false) . '</li>';
//		if ($Droits->check($this->Session->read('user.User.id'), 'Compteurs:edit'))
//			echo '<li>'.$this->Html->link(SHY, '/compteurs/edit/' . $compteur['Compteur']['id'], array('class'=>'link_modifier', 'title'=>'Modifier'), false, false).'</li>';
	?>
</ul>-->

</div>
