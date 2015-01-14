<?php

echo $this->Html->script('/libs/bootstrap-filestyle/js/bootstrap-filestyle.min.js');
echo $this->Html->css("Cakeflow.design.css");
$this->Html->addCrumb(__('Gestion des connecteurs'), array('plugin' => '', 'controller' => 'connecteurs', 'action' => 'index'));
$this->Html->addCrumb(__('Ajout d\'un connecteur'));

echo $this->BsForm->create('ConnecteurPlug', array('type' => 'file'));

$name_content = '';
$name_content .= $this->Html->tag('legend', __('Nom du connecteur'));
$name_content .= $this->BsForm->input('nom_u', array(
    'type' => 'text',
    'placeholder' => 'Nouveuau connecteur',
    'label' => 'Nom'
        ));
$fieldset = $this->Html->tag('fieldset', $name_content);
echo $this->Html->tag('div', $fieldset, array('id' => 'name_content'));


$name_content = '';
$name_content .= $this->Html->tag('legend', __('Fichier de configuration(Json)'));
$name_content .= $this->BsForm->input('monfichier', array(
    //'label' => false, 
    'label' => 'Veuillez sélectionner un fichier de configuration(json)',
    'type' => 'file',
    'data-buttonText' => 'Fichier de configuration',
    'data-iconName' => 'fa fa-file-text-o',
    'data-badge' => false,
    'help' => 'Les modifications apportées ici ne prendront effet que lors de la sauvegarde du projet.',
    'title' => 'Choisir un fichier',
    //'style' => 'width:100%;height:25%',
    //'placeholder' => 'vide',
    'class' => 'filestyle'
        ));
$fieldset = $this->Html->tag('fieldset', $name_content);
echo $this->Html->tag('div', $fieldset);

echo $this->Html2->btnSaveCancel('', array('plugin' => null, 'controller' => 'connecteurs', 'action' => 'index'));
echo $this->BsForm->end();
?>

