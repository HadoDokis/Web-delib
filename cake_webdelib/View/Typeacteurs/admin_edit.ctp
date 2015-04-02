<?php
$this->Html->addCrumb('Liste des types d\'acteur',array('action'=>'index'));

if ($this->Html->value('Typeacteur.id')) {
    $this->Html->addCrumb('Modification d\'un acteur');
    echo $this->Bs->tag('h3', 'Modification :'. $this->Html->value('Typeacteur.nom'));
    echo $this->BsForm->create('Typeacteur', array(
        'url' => array('action' => 'edit', $this->Html->value('Typeacteur.id')), 'type' => 'post'));
} else {
    $this->Html->addCrumb('Ajouter d\'un acteur');
    echo $this->Bs->tag('h3', 'Ajouter un type d\'acteur');
    echo $this->BsForm->create('Typeacteur', array(
        'url' => array('action' => 'add'), 'type' => 'post'));
}
echo $this->BsForm->input('Typeacteur.nom', array('label' => 'Nom <abbr title="obligatoire">*</abbr>')).
$this->BsForm->input('Typeacteur.commentaire', array('label' => 'Commentaire')).
$this->BsForm->radio('Typeacteur.elu', $eluNonElu, array(
        'label' => 'Statut <abbr title="obligatoire">*</abbr>',
        'style' => 'margin-left:5px; margin-right:5px;',
         )).
$this->BsForm->hidden('Typeacteur.id').
$this->Html2->btnSaveCancel('', array('action' => 'index')).
$this->BsForm->end(); ?>
