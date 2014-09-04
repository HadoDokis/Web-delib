<?php

$this->Html->addCrumb('Séance à traiter', array($this->request['controller'], 'action'=>'listerFuturesSeances'));

echo $this->Bs->tag('h3', __('Liste des projets pour la séance du '.$date_seance));
$this->Html->addCrumb(__('Liste des projets pour la séance du '.$date_seance));

// select masqués utilisés par le javascript
//echo $this->Form->input('Deliberation.position', array('options'=>$lst_pos, 'id'=>'selectOrdre', 'label'=>false, 'div'=>false, 'style'=>"display:none; width: auto;", 'onChange'=>"onChangeSelectOrdre(this.value);"));
//echo $this->Form->input('Deliberation.rapporteur_id', array('options'=>$rapporteurs, 'empty'=>true, 'id'=>'selectRapporteur', 'label'=>false, 'div'=>false, 'style'=>"display:none;", 'onChange'=>"onChangeSelectRapporteur(this.value);"));
//cho $this->Form->hidden('Aplication.url', array('value'=>FULL_BASE_URL.$this->webroot));
//echo $this->Form->hidden('Aplication.seanceid', array('value'=>$seance_id));

echo $this->Bs->table(array(array('title' => 'Ordre'),
    array('title' => $this->Html->link('Thème', 
                                        array('controller'=>'deliberations','sortby',
                                            $seance_id,'theme_id'), 
                                        array('confirm'=>'Etes-vous sur de vouloir trier par theme ?'))),
    array('title' =>  $this->Html->link('Service émetteur', 
                                        array('controller'=>'deliberations','sortby',
                                            $seance_id,'service_id'), 
                                        array('confirm'=>'Etes-vous sur de vouloir trier par service ?'))),
    array('title' =>  $this->Html->link('Rapporteur', 
                                        array('controller'=>'deliberations','sortby',
                                            $seance_id,'rapporteur_id'), 
                                        array('confirm'=>'Etes-vous sur de vouloir trier par rapporteur ?'))),
    array('title' => $this->Html->link('Libellé', 
                                        array('controller'=>'deliberations','sortby',
                                            $seance_id,'objet'), 
                                        array('confirm'=>'Etes-vous sur de vouloir trier par libelle ?'))),
    array('title' => $this->Html->link('Titre', 
                                        array('controller'=>'deliberations','sortby',
                                            $seance_id,'titre'), 
                                        array('confirm'=>'Etes-vous sur de vouloir trier par titre ?'))),
   array('title' => 'id'),
    array('title' => 'Actions'),
        ), array('hover', 'striped'));

foreach ($projets as $projet) {
    $delibId=$projet['Deliberation']['id'];
    $delibPosition = $projet['Deliberationseance']['position'];
    
    echo $this->Bs->tableCells(array(
        $this->BsForm->select('Deliberationseance.position',$rapporteurs, array(
            'default' =>$delibPosition,
            'class'=>'select2 selectone'
        )),
        //$this->Html->link($delibPosition, "javascript:onClickLinkOrdre(".$delibPosition.", ".$delibId.");", array('id'=>'linkOrdre'.$delibPosition)),
        '['.$projet['Theme']['order'].'] '.$projet['Theme']['libelle'],
        $projet['Service']['libelle'],
        $this->BsForm->select('deliberation.rapporteur_id',$rapporteurs, array(
            'default' =>$projet['Deliberation']['rapporteur_id'],
            'class'=>'select2 selectone'
        )),
        $projet['Deliberation']['objet_delib'],
        $projet['Deliberation']['titre'],
        ($delibPosition != 1?
            $this->Html->link(null, '/deliberations/positionner/' . $seance_id . '/' . $projet['Deliberation']['id'] . '/-1', array('class' => 'link_monter', 'title' => 'Monter', 'escape' => false), false):'').
        ($delibPosition != $lastPosition?
            $this->Html->link(null, '/deliberations/positionner/' . $seance_id . '/' . $projet['Deliberation']['id'] . '/1', array('class' => 'link_descendre', 'title' => 'Descendre', 'escape' => false), false):'')
        ,
        $this->Bs->div('btn-group') .
        $this->Bs->btn(null, array('controller' => 'users', 'action' => 'view', $projet['Deliberation']['id']), array('type' => 'default', 'icon' => 'glyphicon glyphicon-eye-open', 'title' => 'Voir')) .
        $this->Bs->btn(null, array('controller' => 'users', 'action' => 'edit', $projet['Deliberation']['id']), array('type' => 'primary', 'icon' => 'glyphicon glyphicon-edit', 'title' => 'Modifier')) .
        $this->Bs->btn(null, array('controller' => 'users', 'action' => 'delete', $projet['Deliberation']['id']), array('type' => 'danger', 'icon' => ' glyphicon glyphicon-trash', 'title' => 'Supprimer', 'class' => !$is_deletable ? 'disabled' : ''), 'Êtes vous sur de vouloir supprimer :' . $projet['Deliberation']['objet_delib'] . ' ?') .
        $this->Bs->close()
    ));
}
echo $this->Bs->endTable() .
         $this->Html2->btnCancel();
if ($is_deliberante) 
    echo $this->Bs->btn('<i class="fa fa-clock-o"></i> Reporter l\'ordre du jour', array('action'=>'reportePositionsSeanceDeliberante', $seance_id), 
            array('type'=>'btn','class'=>'btn-inverse', 'name'=>'Retour', 'escape'=>false, 'style'=>'float:right;'));
//foreach($projets as $projet):
//		$delibId=$projet['Deliberation']['id'];
//		$delibPosition = $projet['Deliberationseance']['position'];
?>
<script type="application/javascript">
$(document).ready(function() {
    $('.select2.selectone').select2({
        width: 'resolve',
        placeholder: 'Sélectionner un rapporteur',
        allowClear: true
    });
 });
//        
//var ordreCourant = null;
//var curdelibIdOrdre = null;
//var curdelibIdRapporteur = null;
//
//function onClickLinkOrdre(ordre, delibId) {
//	// initialisations
//	var jqSelect = $('#selectOrdre');
//	var jqLink = $('#linkOrdre'+ordre);
//	var jqTd = jqLink.parent();
//
//	// déplacement du select 'ordre des projets' dans la cellule du lien cliqué
//	jqSelect.val(ordre);
//	jqSelect.appendTo(jqTd);
//	jqSelect.show();
//	// masquage du lien cliqué
//	jqLink.hide();
//	// initialisation des variables globales
//	if (ordreCourant) $('#linkOrdre'+ordreCourant).show();
//	ordreCourant = ordre;
//	curdelibIdOrdre = delibId;
//}
//
//function onChangeSelectOrdre(ordre) {
//    var seanceid = $('#AplicationSeanceid').val();
//	var url = $('#AplicationUrl').val()+"seances/changePosition/"+seanceid+"/"+ordre+"/"+curdelibIdOrdre;
//	document.location=url;
//}
//
//function onClickLinkRapporteur(rapporteurId, delibId) {
//	// initialisations
//	var jqSelect = $('#selectRapporteur');
//	var jqLink = $('#linkRapporteur'+delibId);
//	var jqTd = jqLink.parent();
//
//	// déplacement du select 'rapporteur' dans la cellule du lien cliqué
//	if (rapporteurId) jqSelect.val(rapporteurId);
//	jqSelect.appendTo(jqTd);
//	jqSelect.show();
//	// masquage du lien cliqué
//	jqLink.hide();
//	// initialisation des variables globales
//	if (curdelibIdRapporteur) $('#linkRapporteur'+curdelibIdRapporteur).show();
//	curdelibIdRapporteur = delibId;
//}
//
//function onChangeSelectRapporteur(rapporteurId) {
//    var seanceid = $('#AplicationSeanceid').val();
//	var url = $('#AplicationUrl').val()+"seances/changeRapporteur/"+seanceid+"/"+rapporteurId+"/"+curdelibIdRapporteur;
//	document.location=url;
//}
</script>
