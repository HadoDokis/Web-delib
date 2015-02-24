<?php
$this->Html->addCrumb('Séance à traiter', array('controller'=>'seances', 'action'=>'index'));

echo $this->Bs->tag('h3', __('Ordre du jour de la séance du '. $this->Time->i18nFormat($date_seance, '%d %B %Y à %k h %M')));
$this->Html->addCrumb(__('Ordre du jour'));

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
        //$this->Html->link($delibPosition, "javascript:onClickLinkOrdre(".$delibPosition.", ".$projet['Deliberation']['id'].");", array('id'=>'linkOrdre'.$delibPosition))
        echo $this->Bs->cell(
                $this->BsForm->select('Deliberationseance.position', $aPosition, 
        array(
            'value' => $projet['Deliberationseance']['position'],
            'autocomplete'=>'off',
            'label'=>false,
            'class'=>'input-sm selectone',
            'id'=>'DelibOrdreId'.$projet['Deliberation']['id'],
        )).$this->Bs->btn(null,array('controller'=>'seances',
                                        'action'=>'changePosition',
                                        $seance_id, $projet['Deliberation']['id']) , array(
            'style'=>'Display:none',
            'id'=>'DelibOrdreId'.$projet['Deliberation']['id'].'link',
        )));
        
        echo $this->Bs->cell($projet['Deliberation']['id']);/*($delibPosition != 1?
            $this->Html->link(null, '/deliberations/positionner/' . $seance_id . '/' . $projet['Deliberation']['id'] . '/-1', array('class' => 'link_monter', 'title' => 'Monter', 'escape' => false), false):'').
        ($delibPosition != $lastPosition?
            $this->Html->link(null, '/deliberations/positionner/' . $seance_id . '/' . $projet['Deliberation']['id'] . '/1', array('class' => 'link_descendre', 'title' => 'Descendre', 'escape' => false), false):'')
        */
        echo $this->Bs->cell('['.$projet['Theme']['order'].'] '.$projet['Theme']['libelle']);
        echo $this->Bs->cell($projet['Service']['libelle']);
        echo $this->Bs->cell($this->BsForm->select('deliberation.rapporteur_id',$rapporteurs, array(
            'default' =>$projet['Deliberation']['rapporteur_id'],
            'class'=>'input-sm select2 selectone',
            'id'=>'DelibRappId'.$projet['Deliberation']['id'],
        )).$this->Bs->btn(null,array('controller'=>'seances',
                                        'action'=>'changeRapporteur',
                                        $seance_id, $projet['Deliberation']['id']) , array(
            'style'=>'Display:none',
            'id'=>'DelibRappId'.$projet['Deliberation']['id'].'link',
        )));
        echo $this->Bs->cell($projet['Deliberation']['objet_delib']);
        echo $this->Bs->cell($projet['Deliberation']['titre']);
        
        echo $this->Bs->cell(
        $this->Bs->div('btn-group-vertical') .
        $this->Bs->btn(null, array('controller' => 'deliberations', 'action' => 'view', $projet['Deliberation']['id']), array('type' => 'default', 'icon' => 'glyphicon glyphicon-eye-open', 'title' => 'Voir le projet')) .
        $this->Bs->btn(null, array('controller' => 'deliberations', 'action' => 'edit', $projet['Deliberation']['id']), array('type' => 'primary', 'icon' => 'glyphicon glyphicon-edit', 'title' => 'Modifier le projet')) .
        //$this->Bs->btn(null, array('controller' => 'deliberations', 'action' => 'delete', $projet['Deliberation']['id']), array('type' => 'danger', 'icon' => ' glyphicon glyphicon-remove', 'title' => 'Enlever le projet de la séance', 'class' => !$is_deletable ? 'disabled' : ''), 'Êtes vous sur de vouloir enlever le projet :' . $projet['Deliberation']['objet_delib'] . ' ?') .
        $this->Bs->close());
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
    $('.selectone').select2({
        width: 'element',
        placeholder: false,
        allowClear: true
    }).on('change', function(e) {
        $(location).attr('href', $('#'+e.target.id+'link').attr('href')+'/'+e.val);
    });
 });
</script>
