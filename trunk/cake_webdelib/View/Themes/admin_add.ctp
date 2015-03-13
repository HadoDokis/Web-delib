<?php
$this->Html->addCrumb(__('Liste des thèmes'), array('action'=>'index'));

$this->Html->addCrumb(__('Ajout d\'un thème'));

echo $this->Bs->tag('h3', __('Ajout d\'un thème'));


echo $this->BsForm->create('Theme');
echo $this->BsForm->input('Theme.libelle', array('label' => 'Libellé <abbr title="obligatoire">*</abbr>', 'maxlength' => '500')); 
echo $this->BsForm->input('Theme.order', array('label' => __('Critère de tri')));
echo $this->BsForm->select('Theme.parent_id', $themes,  array(
    'label' => __('Appartient à'), 
    'placeholder'=>__('Choisir un thème'),
    'class' => 'selectone', 
    'empty' => true, 
    'escape' => false));
echo $this->Html2->btnSaveCancel(null, $previous);
echo $this->BsForm->end(); 