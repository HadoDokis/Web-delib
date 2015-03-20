<?php
/**
 * Application: webdelib / Adullact.
 * Date: 06/03/14
 * @author: Florian Ajir <florian.ajir@adullact.org>
 * @license CeCiLL V2 <http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html>
 *
 * Description : Fenêtre modale contenant un formulaire pour l'ajout d'annexe
 */
$body=$this->Form->hidden('Annex.0.ref', array('id' => 'refDelib')).
$this->Form->hidden('Annex.0.numAnnexe', array('id' => 'numAnnexe')).
$this->BsForm->input('Annex.0.file', array(
    'label' => 'Document <abbr title="obligatoire">*</abbr>', 
    'type' => 'file', 
    'data-buttonText'=>'Choisir une annexe',
    'data-iconName'=>'fa fa-file-text-o',
    'data-badge'=> false,
    'title' => 'Document', 
    'class' => 'filestyle'
   // 'after' => $this->Html->tag('p', 'Document obligatoire !', array('class' => "error-message", 'id' => 'annexe-error-message', 'style' => "display: none"))
    )).
$this->Bs->div('spacer', '').
$this->BsForm->input('Annex.0.titre', array('label' => 'Titre', 'autocomplete' => 'off')).
$this->Html->tag('div', '', array('class' => 'spacer')).
$this->BsForm->checkbox('Annex.0.ctrl', array(
    'label' => __('Joindre au controle de légalité'), 
    //'inline'=> true,
    'checked' => false)).
$this->Bs->div('spacer', '').
$this->BsForm->checkbox('Annex.0.fusion', array(
    'label' => __('Joindre à la fusion'),
    //'inline' => true,
    'checked' => true)
).
$this->Bs->div('spacer', '');
            
echo $this->Bs->modal('Nouvelle annexe', $body, array('id'=> 'annexeModal','form'=> false), array(
    'open'=> array(
        'name'=>'Ajouter une annexe',
        'class'=>'test',
        'options'=>array(
            'id' => 'btnAnnexeModal',
            'data-ref' => $ref,
            //'onclick'=> 'javascript: afficherAnnexeModal(this); false;',
            'type'=>'default',
            'icon'=>'glyphicon glyphicon-plus',
            'escape' => false)),
    'close',
    'confirm'=> array(
        'name'=>'Ajouter annexe',
        'link'=>'#',
        'options'=>array('id' => 'btnAnnexeModalAdd'))
));