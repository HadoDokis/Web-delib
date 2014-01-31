<?php
echo $this->Html->script('fonctions');
echo $this->Html->tag('h2', 'Attribuer le projet à un circuit');
echo $this->Form->create('Deliberation', array('type' => 'post', 'url' => array('plugin' => null, 'controller' => 'deliberations', 'action' => 'attribuercircuit', $this->Html->value('Deliberation.id'), $circuit_id)));
$loc = $this->Html->url(array('controller' => 'deliberations', 'action' => 'attribuercircuit', $this->Html->value('Deliberation.id')));
echo $this->Form->input('Deliberation.circuit_id', array('options' => $circuits, "onChange" => "lister_circuits(this, '$loc/');", 'empty' => true, 'selected' => $circuit_id, 'style' => 'float:left;', 'class' => 'select2 selectone'));
echo $this->Html->tag('div', '', array('class' => 'spacer', 'style' => 'heigth:0px;'));
echo $this->Html->tag('div', null, array('style' => 'margin-left:69px;'));
// données concernant le circuit selectionné
if (isset($visu))
    echo($visu);
echo $this->Html->tag('div', '', array('class' => 'spacer', 'style' => 'heigth:0px;'));
echo $this->Html->tag('div', null, array('class' => 'submit btn-group', 'style' => 'margin-left:38px;'));
echo $this->Html->link('<i class="fa fa-arrow-left"></i> Annuler', $lien_retour, array('class' => 'btn', 'escape' => false, 'name' => 'Annuler'));
echo $this->Form->button('<i class="fa fa-check"></i> Attribuer', array('div' => false, 'class' => 'btn btn-primary', 'type' => 'submit', 'name' => 'ajouter'));
echo $this->Html->tag('/div', null);
echo $this->Html->tag('/div', null);
echo $this->Form->end();
?>
<style>
    label {
        padding: 6px .5em 0 0;
    }

    #DeliberationCircuitId {
        width: auto;
        min-width: 200px;
        max-width: 80%;
    }
</style>
<script type="application/javascript">
    $("#DeliberationCircuitId").select2({
        width: "resolve",
        allowClear: true,
        placeholder: "Selectionnez un élément"
    });
</script>