<?php 

echo $this->Bs->tag('h3', 'Changement du mot de passe pour '.$this->Html->value('User.prenom') . ' ' . $this->Html->value('User.nom')) .
$this->BsForm->create('User', array(
    'url' => array('controller' => 'users', 'action' => 'changeMdp', $this->Html->value('User.id')), 
    'type' => 'post')).
$this->BsForm->input('User.password', array('type' => 'password', 'label' => 'Password <acronym title="obligatoire">*</acronym>', 'value' => '')).
$this->BsForm->input('User.password2', array('type' => 'password', 'label' => 'Confirmez le password <acronym title="obligatoire">*</acronym>', 'value' => '')).
$this->BsForm->hidden('User.id').
$this->Html2->btnSaveCancel("", "index", "Changer le mot de passe", "Changer").
$this->BsForm->end();
