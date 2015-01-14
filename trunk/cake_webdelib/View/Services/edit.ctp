<?php
$this->Html->addCrumb('Liste des services', array('action'=>'index'));

$this->Html->addCrumb(__('Modification d\'un service'));

echo $this->Bs->tag('h3', __('Modification du service : ').$this->Html->value('Service.libelle'));

echo $this->BsForm->create('Service', array('action' => 'edit', 'type' => 'post'));
echo $this->BsForm->input('Service.libelle', array('label' => 'Libellé')); 
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
echo $this->BsForm->input('Service.order', array('label' => 'Critère de tri'));

    echo $this->BsForm->hidden('Service.id', array('label' => false));
echo $this->Html2->btnSaveCancel(null, $previous);
echo $this->BsForm->end(); 