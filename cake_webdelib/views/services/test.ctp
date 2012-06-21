<?php 
    echo $javascript->link('jquery.js');
    echo $javascript->link('jquery.autocomplete.js');
    echo $javascript->link('AutocompleteAction.js');

    echo $form->create('Service',array('action' => 'test',
                                       'type'   => 'post')); 
    echo $form->input('Service.libelle', array('size' => '30',        'id'   => 'autoComplete')); 
    echo $form->submit('Tester');
    echo $form->end();
?>
