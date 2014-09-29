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
    array('title' => 'id'),
    array('title' => $this->Html->link('Thème', 
                                        array('controller'=>'seances','action'=>'sortby',
                                            $seance_id,'theme_id'), 
                                        array('confirm'=>'Etes-vous sur de vouloir trier par theme ?'))),
    array('title' =>  $this->Html->link('Service émetteur', 
                                        array('controller'=>'seances','action'=>'sortby',
                                            $seance_id,'service_id'), 
                                        array('confirm'=>'Etes-vous sur de vouloir trier par service ?'))),
    array('title' =>  $this->Html->link('Rapporteur', 
                                        array('controller'=>'seances','action'=>'sortby',
                                            $seance_id,'rapporteur_id'), 
                                        array('confirm'=>'Etes-vous sur de vouloir trier par rapporteur ?'))),
    array('title' => $this->Html->link('Libellé', 
                                        array('controller'=>'seances','action'=>'sortby',
                                            $seance_id,'objet'), 
                                        array('confirm'=>'Etes-vous sur de vouloir trier par libelle ?'))),
    array('title' => $this->Html->link('Titre', 
                                        array('controller'=>'seances','action'=>'sortby',
                                            $seance_id,'titre'), 
                                        array('confirm'=>'Etes-vous sur de vouloir trier par titre ?'))),
    array('title' => 'Actions'),
        ), array('hover', 'striped'));
$this->BsForm->setFormType('vertical');
foreach ($projets as $projet) {
    $delibId=$projet['Deliberation']['id'];
    $delibPosition = $projet['Deliberationseance']['position'];
    echo $this->Bs->tableCells(array(
        //$this->Html->link($delibPosition, "javascript:onClickLinkOrdre(".$delibPosition.", ".$delibId.");", array('id'=>'linkOrdre'.$delibPosition))
        $this->BsForm->select('Deliberationseance.position',$aPosition, array(
            'value' =>$delibPosition,
            'autocomplete'=>'off',
            'label'=>false,
            'class'=>'input-sm select2 selectone',
            'id'=>'DelibOrdreId'.$delibId,
        )).$this->Bs->btn(null,array('controller'=>'seances',
                                        'action'=>'changePosition',
                                        $seance_id, $delibId) , array(
            'style'=>'Display:none',
            'id'=>'DelibOrdreId'.$delibId.'link',
        )),
        $delibId/*($delibPosition != 1?
            $this->Html->link(null, '/deliberations/positionner/' . $seance_id . '/' . $projet['Deliberation']['id'] . '/-1', array('class' => 'link_monter', 'title' => 'Monter', 'escape' => false), false):'').
        ($delibPosition != $lastPosition?
            $this->Html->link(null, '/deliberations/positionner/' . $seance_id . '/' . $projet['Deliberation']['id'] . '/1', array('class' => 'link_descendre', 'title' => 'Descendre', 'escape' => false), false):'')
        */,
        '['.$projet['Theme']['order'].'] '.$projet['Theme']['libelle'],
        $projet['Service']['libelle'],
        $this->BsForm->select('deliberation.rapporteur_id',$rapporteurs, array(
            'default' =>$projet['Deliberation']['rapporteur_id'],
            'class'=>'input-sm select2 selectone',
            'id'=>'DelibRappId'.$delibId,
        )).$this->Bs->btn(null,array('controller'=>'seances',
                                        'action'=>'changeRapporteur',
                                        $seance_id, $delibId) , array(
            'style'=>'Display:none',
            'id'=>'DelibRappId'.$delibId.'link',
        )),
        $projet['Deliberation']['objet_delib'],
        $projet['Deliberation']['titre'],
        $this->Bs->div('btn-group') .
        $this->Bs->btn(null, array('controller' => 'deliberations', 'action' => 'view', $projet['Deliberation']['id']), array('type' => 'default', 'icon' => 'glyphicon glyphicon-eye-open', 'title' => 'Voir')) .
        $this->Bs->btn(null, array('controller' => 'deliberations', 'action' => 'edit', $projet['Deliberation']['id']), array('type' => 'primary', 'icon' => 'glyphicon glyphicon-edit', 'title' => 'Modifier')) .
        $this->Bs->btn(null, array('controller' => 'deliberations', 'action' => 'delete', $projet['Deliberation']['id']), array('type' => 'danger', 'icon' => ' glyphicon glyphicon-trash', 'title' => 'Supprimer', 'class' => !$is_deletable ? 'disabled' : ''), 'Êtes vous sur de vouloir supprimer :' . $projet['Deliberation']['objet_delib'] . ' ?') .
        $this->Bs->close(),
    ));
}
echo $this->Bs->endTable() .
         $this->Html2->btnCancel();
if ($is_deliberante) 
    echo $this->Bs->btn('<i class="fa fa-clock-o"></i> Reporter l\'ordre du jour', 
            array('action'=>'reportePositionsSeanceDeliberante', $seance_id), 
            array('type'=>'success',
                'class'=>'pull-right', 
                'name'=>'Retour', 
                'escape'=>false));
?>
<script type="application/javascript">
$(document).ready(function() {
    $('.select2.selectone').select2({
        width: 'element',
        placeholder: false,
        allowClear: true
    }).on('change', function(e) {
        $(location).attr('href', $('#'+e.target.id+'link').attr('href')+'/'+e.val);
    });
 });
</script>
