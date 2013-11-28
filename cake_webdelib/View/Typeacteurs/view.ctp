<div id="vue_cadre">
<h3>Fiche Type d'acteur</h3>

<dl>

	<div class="demi">
		<dt>Nom</dt>
		<dd>&nbsp;<?php echo $typeacteur['Typeacteur']['nom']?></dd>
	</div>
	<div class="demi">
		<dt>Commentaire</dt>
		<dd>&nbsp;<?php echo $typeacteur['Typeacteur']['commentaire']?></dd>
	</div>

	<div class="spacer"></div>

	<div class="demi">
		<dt>Statut</dt>
		<dd>&nbsp;<?php echo $typeacteur['Typeacteur']['elu'] ? 'élu' : 'non élu'; ?></dd>
	</div>

	<div class="spacer"></div>

	<div class="demi">
		<dt>Date de cr&eacute;ation</dt>
		<dd>&nbsp;<?php echo $typeacteur['Typeacteur']['created']?></dd>
	</div>
	<div class="demi">
		<dt>Date de modification</dt>
		<dd>&nbsp;<?php echo $typeacteur['Typeacteur']['modified']?></dd>
	</div>

	<div class="spacer"></div>

</dl>


<br />
<ul id="actions_fiche">
	<li><?php echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour', '/typeacteurs/index', array('class'=>'btn', 'title'=>'Retourner à la liste', 'escape' =>false), false); ?>
	<?php if ($Droits->check($this->Session->read('user.User.id'), 'Typeacteurs:edit'))
		echo '<li>'.$this->Html->link('<i class="fa fa-edit"></i> Modifier', '/typeacteurs/edit/' . $typeacteur['Typeacteur']['id'], array('class'=>'btn', 'title'=>'Modifier',
                                                                                                     'escape' =>false), false).'</li>';
	?>
</ul>

</div>
