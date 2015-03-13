<?php

echo $this->Html->script('/components/bootstrap-filestyle/src/bootstrap-filestyle.js');
echo $this->Html->script('/components/smalot-bootstrap-datetimepicker/js/bootstrap-datetimepicker.min') .
     $this->Html->script('/components/smalot-bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.fr') .
     $this->Html->css('/components/smalot-bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css');

$this->Html->addCrumb('Liste des types d\'acte', array('controller'=>$this->request['controller'],'action'=>'index'));

if ($this->Html->value('Typeacte.id')) {
    echo $this->Bs->tag('h3', 'Modification d\'un type d\'acte');
    $this->Html->addCrumb('Modification d\'un type d\'acte');
    echo $this->BsForm->create('Typeacte', array(
        'controller' => 'typeactes', 
        'action' => 'edit', 
        'type' => 'file', 
        $this->Html->value('Typeacte.id')));
} else {
    echo $this->Bs->tag('h3', 'Ajout d\'un type d\'acte');
    $this->Html->addCrumb('Ajout d\'un type d\'acte');
    echo $this->BsForm->create('Typeacte', array(
        'controller' => 'typeactes', 
        'action' => 'add', 
        'type' => 'file'));
}
echo $this->Html->tag('div', null, array('class' => 'panel panel-default')) .
    $this->Html->tag('div', 'Informations générales', array('class' => 'panel-heading')) .
    $this->Html->tag('div', null, array('class' => 'panel-body')) .
    $this->BsForm->input('Typeacte.name', array(
        'label' => 'Libellé <abbr title="obligatoire">*</abbr>',
        'type'=> 'text')) .
    $this->BsForm->select('Typeacte.compteur_id', $compteurs,
    array(
    'id' => 'Typeacte.nature_id',
    'label' => 'Compteur <abbr title="obligatoire">*</abbr>', 
    'class' => 'select2 selectone',
    'empty' => (count($compteurs) > 1) && (!$this->Html->value('Typeacte.id')),
    'default' => $this->Html->value('Typeacte.compteur_id'),
    'inline' => true,
    'autocomplete' => 'off'
    )) .
    $this->BsForm->select('Typeacte.nature_id', $natures,
    array(
    'id' => 'Typeacte.nature_id',
    'label' => 'Nature <abbr title="obligatoire">*</abbr>', 
    'class' => 'select2 selectone',
    'empty' => true,
    'inline' => true,
    'autocomplete' => 'off',
    'value' => !empty($this->request->data['Typeacte']['nature_id']) ? $this->request->data['Typeacte']['nature_id'] : false
    )) .
    $this->BsForm->checkbox('Typeacte.teletransmettre', array(
        'label' => 'Télétransmettre', 
        'div'=>false, 
        'checked'=>($this->action == 'add')?true:false)) .
$this->Bs->close(2) .


$this->Html->tag('div', null, array('class' => 'panel panel-default')) .
$this->Html->tag('div', 'Modèles pour les éditions', array('class' => 'panel-heading')) .
$this->Html->tag('div', null, array('class' => 'panel-body')) .    
    $this->BsForm->select('Typeacte.modeleprojet_id', $models_projet,
    array(
    'id' => 'Typeacte.nature_id',
    'label' => 'Projet <abbr title="obligatoire">*</abbr>', 
    'class' => 'select2 selectone',
    'empty' => false,
    'inline' => true,
    'autocomplete' => 'off',
    'value' => $this->Html->value('Typeacte.modelprojet_id')
    )) .
    $this->BsForm->select('Typeacte.modelefinal_id', $models_docfinal,
    array(
    'id' => 'Typeacte.nature_id',
    'label' => 'Document final <abbr title="obligatoire">*</abbr>', 
    'class' => 'select2 selectone',
    'empty' => false,
    'inline' => true,
    'autocomplete' => 'off',
    'value' => $this->Html->value('Typeacte.modeldeliberation_id')
    )) .
$this->Bs->close(2) .

$this->Bs->div('spacer') . $this->Bs->close() .


$this->Html->tag('div', null, array('class' => 'panel panel-default')) .
$this->Html->tag('div', 'Gabarits / Textes par défaut', array('class' => 'panel-heading')) .
$this->Html->tag('div', null, array('class' => 'panel-body'));   
    
    //champ file projet
    if (!empty($this->data['Typeacte']['gabarit_projet']))
    {
        echo $this->Bs->div('media').
             $this->Bs->link($this->Bs->icon('file-text-o',array('4x')),'#',array('class'=>'media-left','escape'=>false)).
             $this->Bs->div('media-body').
             $this->Bs->tag('h4', $this->data['Typeacte']['gabarit_projet_name'] ,array('class'=>'media-heading')).
            
             $this->Bs->div('btn-group').
             $this->Bs->btn('Telecharger' , array(
                 'controller'=>'typeactes',
                 'action'=>'downloadGabarit', 
                 $typeacte_id, 'projet'), array(
                 'type'=>'default',
                 'size' => 'xs',
                 'class'=>'media-left',
                 'icon'=>'glyphicon glyphicon-download',
             )).
             $this->Bs->btn('Editer' , $file_gabarit_projet, array(
                 'type'=>'primary',
                 'size' => 'xs',
                 'class'=>'media-left',
                 'icon'=>'glyphicon glyphicon-edit',
             )).
             $this->Bs->btn('Supprimer' , array(
                 'controller'=>'typeactes',
                 'action'=>'deleteGabarit', $typeacte_id, 'projet'), array(
                 'type'=>'danger',
                 'size' => 'xs',
                 'class'=>'media-left',
                 'icon'=>'glyphicon glyphicon-floppy-remove',
                 'confirm'=>'Voulez-vous vraiment supprimer '.$this->data['Typeacte']['gabarit_projet_name'].' du projet ?'
            )).
            $this->Bs->close(3).$this->Bs->tag('br/', null);
    }
    
    echo $this->BsForm->setLeft(0) .
    $this->Bs->row().
    $this->Bs->col('xs8').
    $this->BsForm->input('Typeacte.gabarit_projet_upload', 
        array('label' => false, 
            'type' => 'file', 
            'data-buttonText'=>'Texte projet',
            'data-iconName'=>'fa fa-file-text-o',
            'data-badge'=> false,
            'help' => 'Les modifications apportées ici ne prendront effet que lors de la sauvegarde du projet.',
            'title' => 'Choisir un fichier', 
            'class' => 'filestyle')).$this->Bs->close().
    $this->Bs->col('xs4').
    $this->Bs->div('btn-group btn-group-right').
    $this->Bs->btn('Effacer' , '#TypeacteGabaritProjetUpload', array(
        'type'=>'danger',
        'class'=>'btn-danger-right',
        'icon'=>'glyphicon glyphicon-floppy-remove',
        'onclick'=>'$("#TypeacteGabaritProjetUpload").filestyle(\'clear\');',
    )).
    $this->Bs->close(3) .
    $this->Bs->div('spacer') . $this->Bs->close();
    
    //champ file Synthese
    if (!empty($this->data['Typeacte']['gabarit_synthese']))
    {
        echo $this->Bs->div('media').
             $this->Bs->link($this->Bs->icon('file-text-o',array('4x')),'#',array('class'=>'media-left','escape'=>false)).
             $this->Bs->div('media-body').
             $this->Bs->tag('h4', $this->data['Typeacte']['gabarit_synthese_name'] ,array('class'=>'media-heading')).
            
             $this->Bs->div('btn-group').
             $this->Bs->btn('Telecharger' , array(
                 'controller'=>'typeactes',
                 'action'=>'downloadGabarit', 
                 $typeacte_id, 'synthese'), array(
                 'type'=>'default',
                 'size' => 'xs',
                 'class'=>'media-left',
                 'icon'=>'glyphicon glyphicon-download',
             )).
             $this->Bs->btn('Editer' , $file_gabarit_synthese, array(
                 'type'=>'primary',
                 'size' => 'xs',
                 'class'=>'media-left',
                 'icon'=>'glyphicon glyphicon-edit',
             )).
             $this->Bs->btn('Supprimer' , array(
                 'controller'=>'typeactes',
                 'action'=>'deleteGabarit', $typeacte_id, 'synthese'), array(
                 'type'=>'danger',
                 'size' => 'xs',
                 'class'=>'media-left',
                 'icon'=>'glyphicon glyphicon-floppy-remove',
                 'confirm'=>'Voulez-vous vraiment supprimer '.$this->data['Typeacte']['gabarit_synthese_name'].' du projet ?'
            )).
            $this->Bs->close(3).$this->Bs->tag('br/', null);
    }
    echo $this->BsForm->setLeft(0) .
    $this->Bs->row().
    $this->Bs->col('xs8').
    $this->BsForm->input('Typeacte.gabarit_synthese_upload', 
        array('label' => false, 
            'type' => 'file', 
            'data-buttonText'=>'Texte synthese',
            'data-iconName'=>'fa fa-file-text-o',
            'data-badge'=> false,
            'help' => 'Les modifications apportées ici ne prendront effet que lors de la sauvegarde du projet.',
            'title' => 'Choisir un fichier', 
            'class' => 'filestyle')).$this->Bs->close().
    $this->Bs->col('xs4').
    $this->Bs->div('btn-group btn-group-right').
    $this->Bs->btn('Effacer' , '#TypeacteGabaritSyntheseUpload', array(
        'type'=>'danger',
        'class'=>'btn-danger-right',
        'icon'=>'glyphicon glyphicon-floppy-remove',
        'onclick'=>'$("#TypeacteGabaritSyntheseUpload").filestyle(\'clear\');',
    )).
    $this->Bs->close(3) .
    $this->Bs->div('spacer') . $this->Bs->close();
   
    //champ file Acte
    if (!empty($this->data['Typeacte']['gabarit_acte']))
    {
        echo $this->Bs->div('media').
             $this->Bs->link($this->Bs->icon('file-text-o',array('4x')),'#',array('class'=>'media-left','escape'=>false)).
             $this->Bs->div('media-body').
             $this->Bs->tag('h4', $this->data['Typeacte']['gabarit_acte_name'] ,array('class'=>'media-heading')).
            
             $this->Bs->div('btn-group').
             $this->Bs->btn('Telecharger' , array(
                 'controller'=>'typeactes',
                 'action'=>'downloadGabarit', 
                 $typeacte_id, 'acte'), array(
                 'type'=>'default',
                 'size' => 'xs',
                 'class'=>'media-left',
                 'icon'=>'glyphicon glyphicon-download',
             )).
             $this->Bs->btn('Editer' , $file_gabarit_acte, array(
                 'type'=>'primary',
                 'size' => 'xs',
                 'class'=>'media-left',
                 'icon'=>'glyphicon glyphicon-edit',
             )).
             $this->Bs->btn('Supprimer' , array(
                 'controller'=>'typeactes',
                 'action'=>'deleteGabarit', $typeacte_id, 'acte'), array(
                 'type'=>'danger',
                 'size' => 'xs',
                 'class'=>'media-left',
                 'icon'=>'glyphicon glyphicon-floppy-remove',
                 'confirm'=>'Voulez-vous vraiment supprimer '.$this->data['Typeacte']['gabarit_acte_name'].' du projet ?'
            )).
            $this->Bs->close(3).$this->Bs->tag('br/', null);
    }
    echo $this->BsForm->setLeft(0) .
         $this->Bs->row().
         $this->Bs->col('xs8').
         $this->BsForm->input('Typeacte.gabarit_acte_upload', 
            array('label' => false, 
                'type' => 'file', 
                'data-buttonText'=>'Texte acte',
                'data-iconName'=>'fa fa-file-text-o',
                'data-badge'=> false,
                'help' => 'Les modifications apportées ici ne prendront effet que lors de la sauvegarde du projet.',
                'title' => 'Choisir un fichier', 
                'class' => 'filestyle')).$this->Bs->close().
         $this->Bs->col('xs4').
         $this->Bs->div('btn-group btn-group-right').
         $this->Bs->btn('Effacer' , '#TypeacteGabaritActeUpload', array(
            'type'=>'danger',
            'class'=>'btn-danger-right',
            'icon'=>'glyphicon glyphicon-floppy-remove',
            'onclick'=>'$("#TypeacteGabaritActeUpload").filestyle(\'clear\');',
         )).
         $this->Bs->close(5) .

$this->Bs->div('spacer') . $this->Bs->close() .

$this->Bs->div('submit') .
    $this->Form->hidden('Typeacte.id') .
    $this->Html2->btnSaveCancel('', array('action' => 'index', 'class' => 'btn btn-primary col-md-offset-5')) .
$this->Bs->close() .
$this->Form->end();