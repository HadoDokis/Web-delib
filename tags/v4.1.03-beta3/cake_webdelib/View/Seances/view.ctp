<div id="vue_seance">
<h3>S&eacute;ance du <?php echo $seance['Seance']['date']?></h3>

<dl>
<!--	<dt>Id</dt>
	<dd>&nbsp;<?php echo $seance['Seance']['id']?></dd> -->

	<dt>Libelle</dt>
	<dd>&nbsp;<?php echo $seance['Typeseance']['libelle']?></dd>
	<dt>Date</dt>
	<dd>&nbsp;<?php echo $seance['Seance']['date']?></dd>
</dl>

<ul id="actions_fiche">
	<li><?php echo $this->Html->link(SHY, '/seances/edit/'.$seance['Seance']['id'], array('class'=>'link_modifier', 'title'=>'Modifier'), false, false) ?> </li>
<!--	<li><?php echo $this->Html->link('Supprimer', '/seances/delete/' . $seance['Seance']['id'], null, 'Etes-vous sur de vouloir supprimer la seance du "' . $seance['Seance']['date'] . '" ?') ?> </li> -->
	<li><?php echo $this->Html->link(SHY,   '/seances/index', array('class'=>'link_annuler_sans_border', 'title'=>'Annuler'), false, false) ?> </li>
<!--	<li><?php echo $this->Html->link('Ajouter une seance',	'/seances/add') ?> </li> -->
</ul>

</div>
