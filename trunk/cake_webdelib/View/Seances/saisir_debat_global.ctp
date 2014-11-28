<?php
echo $this->Html->script('/libs/bootstrap-filestyle/js/bootstrap-filestyle.min.js');

if(empty($this->data['Seance']['traitee']))
    $this->Html->addCrumb('Post-séances', array($this->request['controller']));
else
    $this->Html->addCrumb('Séance à traiter', array($this->request['controller'], 'action'=>'listerFuturesSeances'));
   
$this->Html->addCrumb(__('Saisir les débats généraux'));
echo $this->Html->tag('h3', __('Saisir les débats généraux'));
    
echo $this->BsForm->create('Seances', array('url' => array('controller' => 'seances', 'action' => 'saisirDebatGlobal', $this->data['Seance']['id']), 'type' => 'file'));

if ($this->data['Seance']['debat_global_size'] > 0) {
    echo $this->Bs->div('media').
            $this->Bs->link($this->Bs->icon('file-text-o',array('4x')),'#',array('class'=>'media-left','escape'=>false)).
        $this->Bs->div('media-body').
            $this->Bs->tag('h4', $this->data['Seance']['debat_global_name'] ,array('class'=>'media-heading')).
            
            $this->Bs->div('btn-group').
            $this->Bs->btn('Telecharger' , array('controller'=>'seances',
                                            'action'=>'download', 
                                            $seance_id, 'debat_global'), array(
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
            $this->Bs->btn('Supprimer' , array('controller'=>'seances',
                                        'action'=>'deleteDebatGlobal',
                                        $seance_id), array(
            'type'=>'danger',
                                            'size' => 'xs',
            'class'=>'media-left',
            'icon'=>'glyphicon glyphicon-floppy-remove',
            'confirm'=>'Voulez-vous vraiment supprimer '.$this->data['Seance']['debat_global_name'].' du projet ?'
            )).
            $this->Bs->close(3).$this->Bs->tag('br/', null);
}

$this->BsForm->setLeft(0);
echo $this->Bs->row().
        $this->Bs->col('xs8').
$this->BsForm->input('Seance.texte_doc', 
            array('label' => false, 
                'type' => 'file', 
                'data-buttonText'=>'Nouveau Débats généraux',
                'data-iconName'=>'fa fa-file-text-o',
                'data-badge'=> false,
                'help' => 'Les modifications apportées ici ne prendront effet que lors de la sauvegarde du projet.',
                'title' => 'Choisir un fichier', 
                'class' => 'filestyle')).$this->Bs->close().
        $this->Bs->col('xs4').
        $this->Bs->div('btn-group btn-group-right').
        $this->Bs->btn('Effacer' , '#', array(
            'type'=>'danger',
            'class'=>'btn-danger-right',
            'icon'=>'glyphicon glyphicon-floppy-remove',
            'onclick'=>'$("#SeanceTexteDoc").filestyle(\'clear\');',
        )).
        $this->Bs->close(3);

echo $this->BsForm->hidden('Seance.id');
echo $this->Html2->btnSaveCancel('', $previous);
echo $this->BsForm->end();