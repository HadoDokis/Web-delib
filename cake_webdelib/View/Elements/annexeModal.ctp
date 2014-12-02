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
$this->BsForm->input('Annex.0.file', array('label' => 'Document <abbr title="obligatoire">*</abbr>', 'type' => 'file', 'after' => $this->Html->tag('p', 'Document obligatoire !', array('class' => "error-message", 'id' => 'annexe-error-message', 'style' => "display: none")))).
$this->Html->tag('div', '', array('class' => 'spacer')).
$this->BsForm->input('Annex.0.titre', array('label' => 'Titre', 'autocomplete' => 'off')).
$this->Html->tag('div', '', array('class' => 'spacer')).
$this->BsForm->input('Annex.0.ctrl', array('label' => array('text' => 'Joindre au controle de légalité', 'style' => 'width:auto'), 'type' => 'checkbox', 'checked' => false)).
$this->Html->tag('div', '', array('class' => 'spacer')).
$this->BsForm->input('Annex.0.fusion', array('label' => array('text' => 'Joindre à la fusion', 'style' => 'width:auto'), 'type' => 'checkbox', 'checked' => true));
            
echo $this->Bs->modal('Nouvelle annexe', $body);



return;
?>
<div class="modal hide fade" id="annexeModal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 id="annexeModalTitle">Nouvelle annexe</h3>
    </div>
    <div class="modal-body">
        <div id="annexeModalBloc">
            <?php
           
            ?>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Annuler</button>
        <a href="#" class="btn btn-primary" id="annexeModalSubmit">Valider</a>
    </div>
</div>