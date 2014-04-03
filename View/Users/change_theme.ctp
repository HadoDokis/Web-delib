<h2>Changement de thême</h2>
<?php

echo $this->Form->create('User', array('url' => array('action' => 'changeTheme'), 'type' => 'post'));
echo $this->Form->input('User.theme', array('type' => 'select', 'options' => $themes, 'value' => $this->data['User']['theme']));
?>
<div class="spacer"></div>
<div class="submit">
    <?php
    echo $this->Html->tag('div', null, array('class' => 'btn-group', 'style' => 'text-align:center; margin-top:10px;'));
    echo $this->Html->link('<i class="fa fa-arrow-left"></i> Annuler', array('action' => 'index'), array('class' => 'btn', 'escape' => false, 'title' => 'Annuler', 'style' => 'float:none;'));
    echo $this->Form->button("<i class='fa fa-save'></i> Sauvegarder", array('type' => 'submit', 'id' => 'boutonValider', 'class' => 'btn btn-primary', 'escape' => false, 'title' => 'Enregistrer le thême', 'style' => 'float:none;'));
    echo $this->Html->tag('/div', null);
    ?>
</div>
<?php $this->Form->end(); ?>
