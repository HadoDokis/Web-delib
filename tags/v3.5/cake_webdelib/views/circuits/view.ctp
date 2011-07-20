<div class="circuit">
<h2>View Circuit</h2>

<dl>
	<dt>Id</dt>
	<dd>&nbsp;<?php echo $circuit['Circuit']['id']?></dd>
	<dt>Libelle</dt>
	<dd>&nbsp;<?php echo $circuit['Circuit']['libelle']?></dd>
</dl>
<ul class="actions">
	<li><?php echo $html->link('Modifier le circuit',   '/circuits/edit/' . $circuit['Circuit']['id']) ?> </li>
	<li><?php echo $html->link('Supprimer le circuit', '/circuits/delete/' . $circuit['Circuit']['id'], null, 'Are you sure you want to delete: id ' . $circuit['Circuit']['id'] . '?') ?> </li>
	<li><?php echo $html->link('Annuler',   '/circuits/index') ?> </li>
	<li><?php echo $html->link('Ajouter un circuit',	'/circuits/add') ?> </li>
</ul>

</div>
