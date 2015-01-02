<?php
echo $this->Html->script('/components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min');
echo $this->Html->css('/components/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min');

$this->Html->addCrumb('Liste des types de séance', array('controller'=>$this->request['controller'],'action'=>'index'));

if($this->Html->value('Typeseance.id')) {
    $this->Html->addCrumb($this->Html->value('Typeseance.libelle'));
        echo $this->Bs->tag('h3', $this->Html->value('Typeseance.libelle'));
        echo $this->BsForm->create('Typeseance', array(
            'url' => array('controller' => 'typeseances', 'action' => 'edit', $this->Html->value('Typeseance.id')),
            'type'=>'post'));
}
else {
    $this->Html->addCrumb('Ajout d\'un type de séance');
        echo $this->Bs->tag('h3', 'Ajout d\'un type de séance');
        echo $this->BsForm->create('Typeseance', array(
            'url' => array('controller' => 'typeseances', 'action' => 'add'),
            'type'=>'post'));
}

echo $this->Bs->row();
echo $this->Bs->col('lg6').
$this->Html->tag('div', null, array('class' => 'panel panel-default')) .
$this->Html->tag('div', 'Informations générales', array('class' => 'panel-heading')) .
$this->Html->tag('div', null, array('class' => 'panel-body')) .
$this->BsForm->input('Typeseance.libelle', array('label'=>'Libellé <abbr title="obligatoire">*</abbr>')).
 $this->BsForm->input('Typeseance.color', array('class'=>'cssTypeseanceColor','label'=>'Couleur de fond')). 
    $this->Bs->scriptBlock('$(function(){$(".cssTypeseanceColor").colorpicker();});').
$this->BsForm->input('Typeseance.retard', array('label'=>'Nombre de jours avant retard')).
$this->BsForm->select('Typeseance.action', $actions, array(
    'label'=>'Action en séance <abbr title="obligatoire">*</abbr>', 
    'default'=>$this->Html->value('Typeseance.action'), 
    'class' => 'selectone', 
    'empty'=>!$this->Html->value('Typeseance.id'))).
$this->BsForm->select('Typeseance.compteur_id', $compteurs, array(
    'label'=>'Compteur <abbr title="obligatoire">*</abbr>', 
    'class' => 'selectone',
    'default'=>$this->Html->value('Typeseance.compteur_id'), 
    'empty'=>(count($compteurs)>1) && (!$this->Html->value('Typeseance.id')))).
$this->BsForm->select('Typeacte', $natures, array(
    'label'=>'Type d\'acte <abbr title="obligatoire">*</abbr>', 
    'default'=>$selectedNatures, 
    'multiple' => 'multiple', 
    'class' => 'selectmultiple', 
    'empty'=>false)).
$this->Bs->close(3); 

echo $this->Bs->col('lg6');
echo $this->Html->tag('div', null, array('class' => 'panel panel-default')) .
$this->Html->tag('div', 'Modèles pour les éditions', array('class' => 'panel-heading')) .
$this->Html->tag('div', null, array('class' => 'panel-body')) .
$this->BsForm->select('Typeseance.modelprojet_id', $models_projet, array(
    'label'=>'Projet <abbr title="obligatoire">*</abbr>', 
    'class' => 'selectone',
    'default'=>$this->Html->value('Typeseance.modelprojet_id'), 
    'empty'=>!$this->Html->value('Typeseance.id'))).
$this->BsForm->select('Typeseance.modeldeliberation_id', $models_delib, array(
    'label'=>'Document final <abbr title="obligatoire">*</abbr>', 
    'class' => 'selectone',
    'default'=>$this->Html->value('Typeseance.modeldeliberation_id'), 
    'empty'=>!$this->Html->value('Typeseance.id'))).
$this->BsForm->select('Typeseance.modelconvocation_id', $models_convoc, array(
    'label'=>'Convocation <abbr title="obligatoire">*</abbr>', 
    'class' => 'selectone',
    'default'=>$this->Html->value('Typeseance.modelconvocation_id'), 
    'empty'=>!$this->Html->value('Typeseance.id'))).
$this->BsForm->select('Typeseance.modelordredujour_id', $models_odj, array(
    'label'=>'Ordre du jour <abbr title="obligatoire">*</abbr>', 
    'class' => 'selectone',
    'default'=>$this->Html->value('Typeseance.modelordredujour_id'), 
    'empty'=>!$this->Html->value('Typeseance.id'))).
$this->BsForm->select('Typeseance.modelpvsommaire_id', $models_pvsommaire, array(
    'label'=>'PV sommaire <abbr title="obligatoire">*</abbr>', 
    'class' => 'selectone',
    'default'=>$this->Html->value('Typeseance.modelpvsommaire_id'),
    'empty'=>!$this->Html->value('Typeseance.id'))).
$this->BsForm->select('Typeseance.modelpvdetaille_id', $models_pvdetaille, array(
    'label'=>'PV détaillé <abbr title="obligatoire">*</abbr>', 
    'class' => 'selectone',
    $this->Html->value('Typeseance.modelpvdetaille_id'), 
    'empty'=>!$this->Html->value('Typeseance.id'))).
$this->Bs->close(4);

echo $this->Bs->row();
echo $this->Bs->col('lg12');
echo $this->Html->tag('div', null, array('class' => 'panel panel-default')) .
$this->Html->tag('div', 'Convocations (union des deux sélections ci-dessous)', array('class' => 'panel-heading')) .
$this->Html->tag('div', null, array('class' => 'panel-body')) .
$this->Bs->row().
$this->Bs->col('lg6').
        $this->BsForm->select('Typeacteur', $typeacteurs, array(
            'label' => 'Par type d\'acteur',
            'default' => $selectedTypeacteurs, 
            'class' => 'selectmultiple', 
            'multiple' => 'multiple', 
            'empty'=> true)).
        $this->Bs->close().
$this->Bs->col('lg6').
        $this->BsForm->select('Acteur', $acteurs, array(
            'label' => 'Par acteur',
            'default' => $selectedActeurs, 
            'class' => 'selectmultiple',
            'multiple' => 'multiple', 
            'empty' => true)).
$this->Bs->close(6);

if ($this->action == 'edit')
    echo $this->BsForm->hidden('Typeseance.id');
echo $this->Html2->btnSaveCancel('', $previous).
        $this->BsForm->end();