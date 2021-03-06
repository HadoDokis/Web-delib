<div id="vue_cadre">
<h3>Fiche Service</h3>

<div class="imbrique">
	<dt>Libelle</dt>
	<dd>&nbsp;<?php echo $service['Service']['libelle']?></dd>
	<dt>Circuit par d&eacute;faut</dt>
	<dd>&nbsp;<?php echo $circuitDefaut['Circuit']['libelle']?></dd>
</div>
<div class="imbrique">
	<div class="gauche">
		<dt>Date creation</dt>
		<dd>&nbsp;<?php echo $service['Service']['created']?></dd>
	</div>
	<div class="droite">
		<dt>Date modification</dt>
		<dd>&nbsp;<?php echo $service['Service']['modified']?></dd>
	</div>
</div>

<ul id="actions_fiche">
	<?php
		echo '<li>' . $this->Html->link(SHY, $this->Session->read('previous_url'), array('class'=>'link_annuler_sans_border', 'title'=>'Annuler'), false, false) . '</li>';

		if ($Droits->check($this->Session->read('user.User.id'), 'Services:index'))
			echo '<li>'.$this->Html->link(SHY, array('action'=>'edit', $service['Service']['id']), array('class'=>'link_modifier', 'title'=>'Modifier'), false, false).'</li>';
	?>
</ul>

</div>
