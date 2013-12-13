<?php
// select masqués utilisés par le javascript
echo $this->Form->input('Deliberation.position', array('options'=>$lst_pos, 'id'=>'selectOrdre', 'label'=>false, 'div'=>false, 'style'=>"display:none;", 'onChange'=>"onChangeSelectOrdre(this.value);"));
echo $this->Form->input('Deliberation.rapporteur_id', array('options'=>$rapporteurs, 'empty'=>true, 'id'=>'selectRapporteur', 'label'=>false, 'div'=>false, 'style'=>"display:none;", 'onChange'=>"onChangeSelectRapporteur(this.value);"));
echo $this->Form->hidden('Aplication.url', array('value'=>FULL_BASE_URL.$this->webroot));
echo $this->Form->hidden('Aplication.seanceid', array('value'=>$seance_id));
?>

<h2>Liste des projets pour la séance du <?php echo $date_seance; ?></h2>
<table  class="table table-hover">
	<tr>
		<th style='width:4%'>Ordre</th>
		<?php echo ('<th width="13%">'.$this->Html->link('Thème', "/deliberations/sortby/$seance_id/theme_id",null,'Etes-vous sur de vouloir trier par theme ?'). "</th>"); ?>
		<?php echo ('<th width="13%">'.$this->Html->link('Service émetteur', "/deliberations/sortby/$seance_id/service_id",null,'Etes-vous sur de vouloir trier par service ?'). "</th>"); ?>
		<?php echo ('<th width="5%">'.$this->Html->link('Rapporteur', "/deliberations/sortby/$seance_id/rapporteur_id", null,'Etes-vous sur de vouloir trier par rapporteur ?'). "</th>"); ?>
		<?php echo ('<th>'.$this->Html->link("Libellé de l'acte", "/deliberations/sortby/$seance_id/objet",null,'Etes-vous sur de vouloir trier par libelle ?'). "</th>"); ?>
		<?php echo ('<th width="10%">'.$this->Html->link('Titre', "/deliberations/sortby/$seance_id/titre",null,'Etes-vous sur de vouloir trier par titre ?'). "</th>"); ?>
		<th style='width:4%'>Id.</th>
		<th style='width:2%'>&nbsp;</th>
		<th style='width:2%'>&nbsp;</th>
	</tr>

	<?php foreach($projets as $projet):
		$delibId=$projet['Deliberation']['id'];
		$delibPosition = $projet['Deliberationseance']['position'];
	?>

	<tr height='36px'>
		<td><?php echo $this->Html->link($delibPosition, "javascript:onClickLinkOrdre(".$delibPosition.", ".$delibId.");", array('id'=>'linkOrdre'.$delibPosition)); ?></td>
		<td><?php echo '['.$projet['Theme']['order'].'] '.$projet['Theme']['libelle']; ?></td>
        <td><?php echo $projet['Service']['libelle']; ?></td>
        <td>
            <?php
            if (empty($projet['Deliberation']['rapporteur_id']) || !array_key_exists($projet['Deliberation']['rapporteur_id'], $rapporteurs))
                echo $this->Html->link(" [sélectionner_un_rapporteur] ",
                    "javascript:onClickLinkRapporteur(0, " . $delibId . ");",
                    array('id' => 'linkRapporteur' . $delibId));
            else
                echo $this->Html->link($rapporteurs[$projet['Deliberation']['rapporteur_id']],
                    "javascript:onClickLinkRapporteur(" . $projet['Deliberation']['rapporteur_id'] . ", " . $delibId . ");",
                    array('id' => 'linkRapporteur' . $delibId));
            ?>
        </td>
	    <td><?php echo $projet['Deliberation']['objet_delib']; ?></td>
        <td><?php echo $projet['Deliberation']['titre']; ?></td>
        <td><?php echo $this->Html->link($projet['Deliberation']['id'], array('controller'=>'deliberations', 'action'=>'edit',$projet['Deliberation']['id'])); ?></td>
        <?php
        if ($delibPosition != 1)
            echo('<td>' . $this->Html->link(SHY, '/deliberations/positionner/' . $seance_id . '/' . $projet['Deliberation']['id'] . '/-1', array('class' => 'link_monter', 'title' => 'Monter', 'escape' => false), false) . '</td>');
        else
            echo("<td>&nbsp;</td>");
        if ($delibPosition != $lastPosition)
            echo('<td>' . $this->Html->link(SHY, '/deliberations/positionner/' . $seance_id . '/' . $projet['Deliberation']['id'] . '/1', array('class' => 'link_descendre', 'title' => 'Descendre', 'escape' => false), false) . '</td>');
        else
            echo("<td>&nbsp;</td>");
        ?>
	</tr>
	<?php endforeach; ?>
</table>
<br/>
<div class="submit">
<?php 
    $this->Html2->boutonRetour("listerFuturesSeances");
    if ($is_deliberante) 
        echo $this->Html->link('Reporter l\'ordre du jour', "/seances/reportePositionsSeanceDeliberante/$seance_id", array('class'=>'btn btn-inverse', 'name'=>'Retour','style'=>'float:right;'));
?>
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
        var seanceid = $('#AplicationSeanceid').val();
	var url = $('#AplicationUrl').val()+"seances/changePosition/"+seanceid+"/"+ordre+"/"+curdelibIdOrdre;
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
        var seanceid = $('#AplicationSeanceid').val();
	var url = $('#AplicationUrl').val()+"seances/changeRapporteur/"+seanceid+"/"+rapporteurId+"/"+curdelibIdRapporteur;
	document.location=url;
}
</script>
