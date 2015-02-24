<?php
$this->Html->addCrumb('Liste des profils', array('action'=>'index'));

$this->Html->addCrumb(__('Modification d\'un profil'));

echo $this->Bs->tag('h3', __('Modification du profil : ').$this->Html->value('Profil.libelle'));

echo $this->BsForm->create('Profil', array(
    'controller' => 'profils', 
    'action' => 'edit', 
    'type' => 'post'));


$aTab=array(
    'infos' => 'Informations principales',
    'droits' => 'Droits');

echo $this->Bs->tab($aTab, array(   'active' => 'infos', 
                                    'class' => '-justified')) .
 $this->Bs->tabContent();

echo $this->Bs->tabPane('infos', array('class' => isset($nameTab) ? $nameTab : 'active'));
echo $this->Html->tag('br /'); 
    echo $this->BsForm->input('Profil.name', array(
        'label' => array('text'=>'Libellé', 'style'=>'padding-top:5px;')));
    echo $this->BsForm->select('Profil.parent_id', $profils, array(
    'placeholder'=> __('Choisir un profil'),
    'label' => __('Appartient à'),
    'class' => 'selectone', 'empty' => true)); 
    
    if(Configure::read('USE_LDAP')){
    echo $this->BsForm->select('Profil.ldap_name', $profils, array(
    'placeholder'=> __('Choisir un groupe LDAP'),
    'label' => __('Groupe LDAP'),
    'class' => 'selectone', 'empty' => true));
    }
echo $this->Bs->tabClose();

echo $this->Bs->tabPane('droits');

if ($this->Html->value('Profil.id')){
    echo $this->element('AuthManager.permissions', array('model' => 'Typeacte'));
    echo $this->element('AuthManager.permissions', array('model' => 'Profil'));
    echo $this->element('AuthManager.permissions', array('model' => 'User'));
}
else {
    echo $this->Html->para(null, __('Sauvegardez puis &eacute;ditez &agrave; nouveau l\'utilisateur pour modifier ses droits.', true));
    echo $this->Html->para(null, __('Les nouveaux utilisateurs h&eacute;ritent des droits des profils auxquels ils sont rattach&eacute;s.', true));
}

echo $this->Bs->tabClose();
echo $this->Bs->tabPaneClose();

echo $this->BsForm->hidden('Profil.id'); 
echo $this->Html2->btnSaveCancel(null, $previous);
echo $this->BsForm->end(); 