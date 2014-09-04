<?php
if ($this->Html->value('Sequence.id')) {
    echo $this->Bs->tag('h3', 'Modification d\'une séquence');
    echo $this->Form->create('Sequence', array('url' => '/sequences/edit/' . $this->Html->value('Sequence.id'), 'type' => 'post'));
} else {
    echo $this->Bs->tag('h3', 'Ajout d\'une séquence');
    echo $this->Form->create('Sequence', array('url' => '/sequences/add/', 'type' => 'post'));
}
?>

<div class="required">
    <?php echo $this->Form->input('Sequence.nom', array('label' => 'Nom <acronym title="obligatoire">(*)</acronym>', 'size' => '60')); ?>
</div>
<br/>
<div class="required">
    <?php echo $this->Form->input('Sequence.commentaire', array('label' => 'Commentaire', 'size' => '100')); ?>
</div>
<br/>
<div class="required">
    <?php
    if (Configure::read('INIT_SEQ') && $this->action == 'add')
        echo $this->Form->input('Sequence.num_sequence', array('label' => 'Num&eacute;ro de s&eacute;quence', 'size' => '10', 'value' => 0));
    elseif ($this->action == 'edit')
        echo $this->Form->input('Sequence.num_sequence', array('label' => 'Num&eacute;ro de s&eacute;quence', 'size' => '10', 'disabled' => true));
    elseif ($this->action == 'add')
        echo $this->Form->input('Sequence.num_sequence', array('label' => 'Num&eacute;ro de s&eacute;quence', 'size' => '10', 'readonly' => true, 'value' => 0));
    ?>
</div>
<br/><br/>
<div class="submit">
    <?php
    echo $this->Form->hidden('Typeacte.id');
    echo $this->Html2->btnSaveCancel('', array('action' => 'index'));
    ?>
</div>

<?php echo $this->Form->end(); ?>
