<?php
$this->Html->addCrumb('Liste des thèmes', array('action'=>'index'));

$this->Html->addCrumb(__('Modification d\'un thème'));

echo $this->Bs->tag('h3', __('Modification du thème : ').$this->Html->value('Theme.libelle'));

echo $this->BsForm->create('Theme'); 

echo $this->BsForm->input('Theme.libelle', array('label' => 'Libellé', 'maxlength' => '500')); 
echo $this->BsForm->input('Theme.order', array('label' => 'Critère de tri')); 
echo $this->BsForm->select('Theme.parent_id', $themes,  array(
    'label' => __('Appartient à'), 
    'placeholder'=>__('Choisir un thème'),
    'default' => $selectedTheme,
    'class' => 'selectone', 
    'empty' => true, 
    'escape' => false));

echo $this->BsForm->hidden('Theme.id'); 
echo $this->Html2->btnSaveCancel(null, $previous);
echo $this->BsForm->end(); 