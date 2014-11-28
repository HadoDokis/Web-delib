<?php
echo $this->Html->script('/libs/bootstrap-filestyle/js/bootstrap-filestyle.min.js');

if(empty($seance['Seance']['traitee'])) {
    if ($seance['Typeseance']['action'] == 0){
        $this->Html->addCrumb('Séance à traiter', array('controller'=>'seances', 'action' => 'listerFuturesSeances'));
        $this->Html->addCrumb(__('Séance du ') . $seance['Seance']['date'], array('controller'=>'seances', 'action' => 'details', $seance_id));
    }
    if ($seance['Typeseance']['action'] == 1)
    {
        $this->Html->addCrumb('Séance à traiter', array('controller'=>'seances', 'action' => 'listerFuturesSeances'));
        $this->Html->addCrumb(__('Séance du ') . $seance['Seance']['date'], array('controller'=>'seances', 'action' => 'detailsAvis', $seance_id));
    }
    if ($seance['Typeseance']['action'] == 2){
        $this->Html->addCrumb('Séance à traiter', array('controller'=>'seances', 'action' => 'listerFuturesSeances'));
        $this->Html->addCrumb('Post-séances', array('controller'=>'seances', 'action' => 'details', $seance_id));
    }
}  
else {
    $this->Html->addCrumb('Post-séances', array('controller'=>'postseances', 'action' => 'index'));
    $this->Html->addCrumb(__('Séance du ') . $seance['Seance']['date'], array('controller'=>'postseances', 'action' => 'afficherProjets', $seance_id));
}

$this->Html->addCrumb(__('Saisir les débats'));
echo $this->Html->tag('h3', __('Saisir les débats'));

echo $this->BsForm->create('Seances', array('url' => array('controller' => 'seances', 'action' => 'saisirDebat', $delib_id, $seance_id), 'type' => 'file'));

if ($this->data['Deliberation']['debat_size'] > 0) {
    echo $this->Bs->div('media').
            $this->Bs->link($this->Bs->icon('file-text-o',array('4x')),'#',array('class'=>'media-left','escape'=>false)).
        $this->Bs->div('media-body').
            $this->Bs->tag('h4', $this->data['Deliberation']['debat_name'] ,array('class'=>'media-heading')).
            
            $this->Bs->div('btn-group').
            $this->Bs->btn('Telecharger' , array('controller'=>'deliberations',
                                            'action'=>'download', 
                                            $this->data['Deliberation']['id'], 'debat'), array(
            'type'=>'default',
            'size' => 'xs',
            'class'=>'media-left',
            'icon'=>'glyphicon glyphicon-download',
            )).
            $this->Bs->btn('Editer' , $file_debat, array(
            'type'=>'primary',
                'size' => 'xs',
            'class'=>'media-left',
            'icon'=>'glyphicon glyphicon-edit',
            )).
            $this->Bs->btn('Supprimer' , array('controller'=>'deliberations',
                                        'action'=>'deleteDebat',
                                        $delib_id, $seance_id), array(
            'type'=>'danger',
                                            'size' => 'xs',
            'class'=>'media-left',
            'icon'=>'glyphicon glyphicon-floppy-remove',
            'confirm'=>'Voulez-vous vraiment supprimer '.$this->data['Deliberation']['debat_name'].' du projet ?'
            )).
            $this->Bs->close(3).$this->Bs->tag('br/', null);
}

$this->BsForm->setLeft(0);
echo $this->Bs->row().
        $this->Bs->col('xs8').
$this->BsForm->input('Deliberation.texte_doc', 
            array('label' => false, 
                'type' => 'file', 
                'data-buttonText'=>__('Nouveau Débats'),
                'data-iconName'=>'fa fa-file-text-o',
                'data-badge'=> false,
                'help' => __('Les modifications apportées ici ne prendront effet que lors de la sauvegarde du projet.'),
                'title' => 'Choisir un fichier', 
                'class' => 'filestyle')).$this->Bs->close().
        $this->Bs->col('xs4').
        $this->Bs->div('btn-group btn-group-right').
        $this->Bs->btn(__('Effacer') , '#', array(
            'type'=>'danger',
            'class'=>'btn-danger-right',
            'icon'=>'glyphicon glyphicon-floppy-remove',
            'onclick'=>'$("#SeanceTexteDoc").filestyle(\'clear\');',
        )).
        $this->Bs->close(3);

echo $this->BsForm->hidden('Deliberation.id');
echo $this->BsForm->hidden('Seance.id');
echo $this->Html2->btnSaveCancel('', $previous);
echo $this->BsForm->end();