<?php
echo $this->Html->script('fonctions');
echo $this->Html->tag('h2', 'Attribuer le projet à un circuit');
echo $this->Form->create('Deliberation', array('type' => 'post', 'url' => '/deliberations/attribuercircuit/' . $this->Html->value('Deliberation.id') . '/' . $circuit_id));
$loc = $this->Html->url("/deliberations/attribuercircuit/" . $this->Html->value('Deliberation.id') . "/");
echo $this->Form->input('Deliberation.circuit_id', array('options' => $circuits, "onChange" => "lister_circuits(this, '$loc');", 'empty' => true, 'selected' => $circuit_id, 'style'=>'float:left;'));
echo $this->Html->tag('div', '', array('class' => 'spacer', 'style'=>'heigth:0px;'));
echo $this->Html->tag('div', null, array('style' => 'margin-left:69px;'));
// données concernant le circuit selectionné
if (isset($visu))
    echo ($visu);
echo $this->Html->tag('div', '', array('class' => 'spacer', 'style'=>'heigth:0px;'));
echo $this->Html->tag('div', null, array('class' => 'submit btn-group', 'style'=>'margin-left:38px;'));
echo $this->Html->link('<i class="icon-arrow-left"></i> Annuler', $lien_retour, array('class' => 'btn', 'escape' => false, 'name' => 'Annuler'));
echo $this->Form->button('<i class="icon-ok-sign"></i> Attribuer', array('div' => false, 'class' => 'btn btn-primary', 'type' => 'submit', 'name' => 'ajouter'));
echo $this->Html->tag('/div', null);
echo $this->Html->tag('/div', null);
echo $this->Form->end();
?>
<style>
    label{
        padding: 6px .5em 0 0;
    }
</style>