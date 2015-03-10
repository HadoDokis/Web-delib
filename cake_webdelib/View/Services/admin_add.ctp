<?php
$this->Html->addCrumb(__('Liste des services'), array('action'=>'index'));

$this->Html->addCrumb(__('Liste des services'));

echo $this->Bs->tag('h3', __('Ajout d\'un service'));

echo $this->BsForm->create('Service', array('controller' => 'services', 'action' => 'admin_add', 'type' => 'post')); 
echo $this->BsForm->input('Service.name', array('label' => 'Libellé <abbr title="obligatoire">*</abbr>')); 
echo $this->BsForm->select('Service.parent_id', $services, array(
    'label' => __('Appartient à'),
    'placeholder'=>__('Choisir un service'),
    'empty' => true, 
    'escape' => false,
    'class' => 'selectone'));

echo $this->BsForm->select('Service.circuit_defaut_id', $circuits, array(
    'placeholder'=> __('Choisir un circuit par défaut'),
    'label' => __('Circuit par défaut'),
    'empty' => true, 
    'class' => 'selectone')); 
echo $this->BsForm->input('Service.order', array('label' => __('Critère de tri'))); 
echo $this->Html2->btnSaveCancel(null, $previous);
echo $this->BsForm->end(); 