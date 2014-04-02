<div id="vue_cadre">
<h3>Fiche Thème</h3>

<div class="imbrique">
	<dt>Libelle</dt>
	<dd>&nbsp;<?php echo $theme['Theme']['libelle']?></dd>
</div>
<div class="imbrique">
	<div class="gauche">
		<dt>Date creation</dt>
		<dd>&nbsp;<?php echo $theme['Theme']['created']?></dd>
	</div>
	<div class="droite">
		<dt>Date modification</dt>
		<dd>&nbsp;<?php echo $theme['Theme']['modified']?></dd>
	</div>
</div>

<ul id="actions_fiche">
	<?php
		echo '<li>'.$this->Html->link(SHY,$this->Session->read('user.User.lasturl'),array('class'=>'link_annuler_sans_border','title'=>'Annuler'),false,false).'</li>';

		if ($Droits->check($user_id, 'Themes:index'))
			echo '<li>'.$this->Html->link(SHY,'/themes/edit/'.$theme['Theme']['id'],array('class'=>'link_modifier','title'=>'Modifier'),false,false).'</li>';
	?>
</ul>

</div>
