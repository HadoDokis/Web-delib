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


<div class='btn-group' id="actions_fiche">
	<?php
                $this->Html2->boutonRetour('index', 'float:none;');
		if ($Droits->check($this->Session->read('user.User.id'), 'Sequences:edit'))
                    $this->Html2->boutonModifierUrl('/sequences/edit/' . $sequence['Sequence']['id'], 'Modifier', 'Modifier', 'float:none;', '');
	?>
</div>

</div>
