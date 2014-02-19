<?php 
    echo $this->Html->script('jquery.js');
    echo $this->Html->script('jquery.autocomplete.js');
    echo $this->Html->script('AutocompleteAction.js');

    echo $this->Form->create('Service',array('action' => 'test',
                                       'type'   => 'post')); 
    echo $this->Form->input('Service.libelle', array('size' => '30',        'id'   => 'autoComplete')); 
    echo $this->Form->submit('Tester');
    echo $this->Form->end();
?>
