<?php
// select masqués utilisés par le javascript
echo $form->input('Deliberation.position', array('options'=>$lst_pos, 'id'=>'selectOrdre', 'label'=>false, 'div'=>false, 'style'=>"display:none;", 'onChange'=>"onChangeSelectOrdre(this.value);"));
echo $form->input('Deliberation.rapporteur_id', array('options'=>$rapporteurs, 'empty'=>true, 'id'=>'selectRapporteur', 'label'=>false, 'div'=>false, 'style'=>"display:none;", 'onChange'=>"onChangeSelectRapporteur(this.value);"));
echo $form->hidden('Aplication.url', array('value'=>FULL_BASE_URL.$this->webroot));
?>

<h2>Liste des projets pour la séance du <?php echo $date_seance; ?></h2>
<table>
	<tr>
		<th width='4%'>Ordre</th>
		<?php echo ('<th width="13%">'.$html->link('Thème', "/deliberations/sortby/$seance_id/theme_id",null,'Etes-vous sur de vouloir trier par theme ?'). "</th>"); ?>
		<?php echo ('<th width="13%">'.$html->link('Service émetteur', "/deliberations/sortby/$seance_id/service_id",null,'Etes-vous sur de vouloir trier par service ?'). "</th>"); ?>
		<?php echo ('<th width="5%">'.$html->link('Rapporteur', "/deliberations/sortby/$seance_id/rapporteur_id", null,'Etes-vous sur de vouloir trier par rapporteur ?'). "</th>"); ?>
		<?php echo ('<th>'.$html->link('Libellé', "/deliberations/sortby/$seance_id/objet",null,'Etes-vous sur de vouloir trier par libelle ?'). "</th>"); ?>
		<?php echo ('<th width="10%">'.$html->link('Titre', "/deliberations/sortby/$seance_id/titre",null,'Etes-vous sur de vouloir trier par titre ?'). "</th>"); ?>
		<th width='4%'>Id.</th>
		<th width='2%'>&nbsp;&nbsp;</th>
		<th width='2%'>&nbsp;&nbsp;</th>
	</tr>

	<?php foreach($projets as $projet):
		$delibId=$projet['Deliberation']['id'];
		$delibPosition = $projet['Deliberation']['position'];
	?>

	<tr>
		<td><?php echo $html->link($delibPosition, "javascript:onClickLinkOrdre(".$delibPosition.", ".$delibId.");", array('id'=>'linkOrdre'.$delibPosition)); ?></td>
		<td><?php echo '['.$projet['Theme']['order'].'] '.$projet['Theme']['libelle']; ?></td>
	    <td><?php echo $projet['Service']['libelle']; ?></td>
	    <td><?php
			if (empty($projet['Deliberation']['rapporteur_id']) || !array_key_exists($projet['Deliberation']['rapporteur_id'], $rapporteurs))
				echo $html->link('[sélectionner_un_rapporteur]', "javascript:onClickLinkRapporteur(0, ".$delibId.");", array('id'=>'linkRapporteur'.$delibId));
			else
				echo $html->link($rapporteurs[$projet['Deliberation']['rapporteur_id']], "javascript:onClickLinkRapporteur(".$projet['Deliberation']['rapporteur_id'].", ".$delibId.");", array('id'=>'linkRapporteur'.$delibId));
			?>
		</td>
	    <td><?php echo $projet['Deliberation']['objet']; ?></td>
	    <td><?php echo $projet['Deliberation']['titre']; ?></td>
	    <td><?php echo $projet['Deliberation']['id']; ?></td>
	    <?php
	        if($delibPosition!= 1)
	            echo ('<td>'.$html->link(SHY, '/deliberations/positionner/'.$projet['Deliberation']['id'].'/-1', array('class'=>'link_monter', 'title'=>'Monter'), false, false).'</td>');
	        else
	           echo("<td>&nbsp;</td>");
			if($delibPosition!= $lastPosition)
	                    echo ('<td>'.$html->link(SHY, '/deliberations/positionner/'.$projet['Deliberation']['id'].'/1', array('class'=>'link_descendre', 'title'=>'Descendre'), false, false).'</td>');
		        else
		            echo("<td>&nbsp;</td>");
             ?>
	</tr>
	<?php endforeach; ?>
</table>
<br/>
<div class="submit">
<?php echo $html->link('Retour', '/seances/listerFuturesSeances', array('class'=>'link_annuler', 'name'=>'Retour'))?>
</div>

<script>
var ordreCourant = null;
var curdelibIdOrdre = null;
var curdelibIdRapporteur = null;

function onClickLinkOrdre(ordre, delibId) {
	// initialisations
	var jqSelect = $('#selectOrdre');
	var jqLink = $('#linkOrdre'+ordre);
	var jqTd = jqLink.parent();

	// déplacement du select 'ordre des projets' dans la cellule du lien cliqué
	jqSelect.val(ordre);
	jqSelect.appendTo(jqTd);
	jqSelect.show();
	// masquage du lien cliqué
	jqLink.hide();
	// initialisation des variables globales
	if (ordreCourant) $('#linkOrdre'+ordreCourant).show();
	ordreCourant = ordre;
	curdelibIdOrdre = delibId;
}

function onChangeSelectOrdre(ordre) {
	var url = $('#AplicationUrl').val()+"seances/changePosition/"+ordre+"/"+curdelibIdOrdre;
	document.location=url;
}

function onClickLinkRapporteur(rapporteurId, delibId) {
	// initialisations
	var jqSelect = $('#selectRapporteur');
	var jqLink = $('#linkRapporteur'+delibId);
	var jqTd = jqLink.parent();

	// déplacement du select 'rapporteur' dans la cellule du lien cliqué
	if (rapporteurId) jqSelect.val(rapporteurId);
	jqSelect.appendTo(jqTd);
	jqSelect.show();
	// masquage du lien cliqué
	jqLink.hide();
	// initialisation des variables globales
	if (curdelibIdRapporteur) $('#linkRapporteur'+curdelibIdRapporteur).show();
	curdelibIdRapporteur = delibId;
}

function onChangeSelectRapporteur(rapporteurId) {
	var url = $('#AplicationUrl').val()+"seances/changeRapporteur/"+rapporteurId+"/"+curdelibIdRapporteur;
	document.location=url;
}
</script>
