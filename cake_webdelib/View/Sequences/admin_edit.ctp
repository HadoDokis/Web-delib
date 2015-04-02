<?php
$this->Html->addCrumb('Liste des séquences', array('action'=>'index'));

if ($this->Html->value('Sequence.id')) {
    $this->Html->addCrumb(__('Modification d\'un séquence'));
    echo $this->Bs->tag('h3', 'Modification d\'une séquence') .
    $this->BsForm->create('Sequence', array(
         'admin' => 'true',
         'prefix' => 'admin',
         'url' => array(
             'controller' => 'sequences', 
             'action' => 'edit'
             ), 
        'type' => 'post'));
} else {
    $this->Html->addCrumb(__('Ajout d\'un séquence'));
    echo $this->Bs->tag('h3', 'Ajout d\'une séquence') .
    $this->BsForm->create('Sequence', array(
         'admin' => 'true',
         'prefix' => 'admin',
         'url' => array(
             'controller' => 'sequences', 
             'action' => 'add'
             ), 
        'type' => 'post'));
}

echo $this->Bs->div('required') .
$this->BsForm->input('Sequence.nom', array(
    'label' => 'Nom <acronym title="obligatoire">(*)</acronym>')) .       
$this->Bs->close() . 
$this->Bs->div('spacer').$this->Bs->close() . 
$this->Bs->div('required') .
$this->BsForm->input('Sequence.commentaire', array(
    'label' => 'Commentaire')) .
$this->Bs->close() . 
$this->Bs->div('spacer').$this->Bs->close() . 
$this->Bs->div('required');

if (Configure::read('INIT_SEQ') && $this->action == 'admin_add')
    echo $this->BsForm->input(
            'Sequence.num_sequence', array(
                'label' => 'Num&eacute;ro de s&eacute;quence', 
                'value' => 0));
elseif ($this->action == 'admin_edit')
    echo $this->BsForm->input(
            'Sequence.num_sequence', array(
                'label' => 'Num&eacute;ro de s&eacute;quence', 
                'disabled' => true));
elseif ($this->action == 'admin_add')
    echo $this->BsForm->input(
            'Sequence.num_sequence', array(
                'label' => 'Num&eacute;ro de s&eacute;quence', 
                'type' => 'number',
                'readonly' => true, 
                'value' => 0));

$this->BsForm->setLeft(5);
echo $this->Bs->close() . 
$this->Bs->div('spacer').$this->Bs->close() . 
$this->Form->hidden('Sequence.id') .
$this->Html2->btnSaveCancel('', $previous) .
$this->Form->end();

