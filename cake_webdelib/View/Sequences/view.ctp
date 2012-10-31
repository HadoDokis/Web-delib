<div id="vue_cadre">
<h3>Fiche S&eacute;quence</h3>

<dl>

<div class="imbrique">
	<div class="gauche">
		<dt>Nom</dt>
		<dd>&nbsp;<?php echo $sequence['Sequence']['nom']?></dd>
	</div>
	<div class="droite">
		<dt>Commentaire</dt>
		<dd>&nbsp;<?php echo $sequence['Sequence']['commentaire']?></dd>
	</div>
</div>

<div class="imbrique">
	<div class="gauche">
		<dt>Num&eacute;ro de la s&eacute;quence</dt>
		<dd>&nbsp;<?php echo $sequence['Sequence']['num_sequence']?></dd>
	</div>
</div>

<div class="imbrique">
	<div class="gauche">
		<dt>Date de cr&eacute;ation</dt>
		<dd>&nbsp;<?php echo $sequence['Sequence']['created']?></dd>
	</div>
	<div class="droite">
		<dt>Date de modification</dt>
		<dd>&nbsp;<?php echo $sequence['Sequence']['modified']?></dd>
	</div>
</div>

</dl>


<br />
<ul id="actions_fiche">
	<?php
		echo '<li>' . $html->link(SHY, $session->read('user.User.lasturl'), array('class'=>'link_annuler_sans_border', 'title'=>'Annuler'), false, false) . '</li>';
		if ($Droits->check($session->read('user.User.id'), 'Sequences:edit'))
			echo '<li>'.$html->link(SHY, '/sequences/edit/' . $sequence['Sequence']['id'], array('class'=>'link_modifier', 'title'=>'Modifier'), false, false).'</li>';
	?>
</ul>

</div>
