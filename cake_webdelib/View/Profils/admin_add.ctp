<?php
$this->Html->addCrumb(__('Liste des profils'), array('action'=>'index'));

$this->Html->addCrumb(__('Ajout d\'un profil'));

echo $this->Bs->tag('h3', __('Ajout d\'un profil'));

echo $this->BsForm->create('Profil', array('controller' => 'profils', 'action' => 'add', 'type' => 'post')); 
echo $this->BsForm->input('Profil.name', array('label' => 'Libellé <abbr title="obligatoire">(*)</abbr>')); 
echo $this->BsForm->select('Profil.parent_id', $profils, array(
    'placeholder'=> __('Choisir un profil'),
    'label' => __('Appartient à'),
    'class' => 'selectone', 'empty' => true)); 
echo $this->Html2->btnSaveCancel(null, $previous);
echo $this->BsForm->end(); 