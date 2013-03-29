<div id="vue_cadre">
<h3>Fiche profil</h3>

<div class="imbrique">
	<dt>Libelle</dt>
	<dd>&nbsp;<?php echo $profil['Profil']['libelle']?></dd>
</div>
<div class="imbrique">
	<div class="gauche">
		<dt>Date de création</dt>
		<dd>&nbsp;<?php echo $profil['Profil']['created']?></dd>
	</div>
	<div class="droite">
		<dt>Date modification</dt>
		<dd>&nbsp;<?php echo $profil['Profil']['modified']?></dd>
	</div>
</div>

<div id="actions_fiche" class='btn-group' style='text-align: center;'>
	<?php
        $this->Html2->boutonRetour('index', 'float:none;');
		if ($Droits->check($this->Session->read('user.User.id'), 'Profils:edit'))
                    $this->Html2->boutonModifierUrl('/profils/edit/' . $profil['Profil']['id'], 'Modifier', 'Modifier', 'float:none;', '');
                
	?>
</div>

<!--
<ul id="actions_fiche">
	<?php
		echo '<li>' . $this->Html->link(SHY, $this->Session->read('user.User.lasturl'), array('class'=>'link_annuler_sans_border', 'title'=>'Annuler'), false, false) . '</li>';

		if ($Droits->check($this->Session->read('user.User.id'), 'Profils:index'))
			echo '<li>'.$this->Html->link(SHY,   '/profils/edit/' . $profil['Profil']['id'], array('class'=>'link_modifier', 'title'=>'Modifier'), false, false).'</li>';
	?>
</ul>-->

</div>
